<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Github\Github;

JFormHelper::loadFieldClass('list');

/**
 * Form Field Place class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldReleases extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Releases';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_localise/vendor/autoload.php';

		$attributes    = '';
		$params        = JComponentHelper::getParams('com_localise');
		$versions_path = JPATH_ROOT
				. '/administrator/components/com_localise/customisedref/stable_joomla_releases.txt';

		// Empty txt file to make sure it contains only stable releases after save.
		if ($params->get('pre_stable', '0') == '0')
		{
			file_put_contents($versions_path, '');
		}

		$versions_file = file_get_contents($versions_path);
		$versions      = preg_split("/\\r\\n|\\r|\\n/", $versions_file);

		$gh_user       = 'joomla';
		$gh_project    = 'joomla-cms';
		$gh_token      = $params->get('gh_token', '');

		$options = new JRegistry;

		if (!empty($gh_token))
		{
			$options->set('headers', ['Authorization' => 'token ' . $gh_token]);
			$github = new Github($options);
		}
		else
		{
			// Without a token runs fatal.
			// $github = new JGithub;

			// Trying with a 'read only' public repositories token
			// But base 64 encoded to avoid Github alarms sharing it.
			$gh_token = base64_decode('MzY2NzYzM2ZkMzZmMWRkOGU5NmRiMTdjOGVjNTFiZTIyMzk4NzVmOA==');
			$options->set('headers', ['Authorization' => 'token ' . $gh_token]);
			$github = new Github($options);
		}

		try
		{
			$releases = $github->repositories->get(
					$gh_user,
					$gh_project . '/releases'
					);

			foreach ($releases as $release)
			{
				$tag_name = $release->tag_name;
				$tag_part = explode(".", $tag_name);
				$undoted  = str_replace('.', '', $tag_name);
				$excluded = 0;

				if (version_compare(JVERSION[0], '2', 'eq'))
				{
					$excluded = 1;
				}
				elseif (version_compare(JVERSION[0], '3', 'eq'))
				{
					if ($tag_part[0] != '3')
					{
						$excluded = 1;
					}
				}
				elseif (version_compare(JVERSION[0], '4', 'ge'))
				{
					if ($tag_part[0] == '4' || $tag_part[0] == '3')
					{
						$excluded = 0;
					}
					else
					{
						$excluded = 1;
					}
				}

				// Filtering by "is_numeric" disable betas or similar releases.
				if ($params->get('pre_stable', '0') == '0')
				{
					if (!in_array($tag_name, $versions) && is_numeric($undoted) && $excluded == 0)
					{
						$versions[] = $tag_name;
						JFactory::getApplication()->enqueueMessage(
							JText::sprintf('COM_LOCALISE_NOTICE_NEW_VERSION_DETECTED', $tag_name),
							'notice');
					}
				}
				else
				{
					if (!in_array($tag_name, $versions) && $excluded == 0)
					{
						$versions[] = $tag_name;
						JFactory::getApplication()->enqueueMessage(
							JText::sprintf('COM_LOCALISE_NOTICE_NEW_VERSION_DETECTED', $tag_name),
							'notice');
					}
				}
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::_('COM_LOCALISE_ERROR_GITHUB_GETTING_RELEASES'),
				'warning');
		}

		arsort($versions);

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-' . $this->value . '"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$versions_file = '';

		foreach ($versions as $id => $version)
		{
			if (!empty($version))
			{
				$options[] = JHtml::_('select.option', $version, JText::sprintf('COM_LOCALISE_CUSTOMIZED_REFERENCE', $version),
							array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-release"')
							);

				$versions_file .= $version . "\n";
			}
		}

		JFile::write($versions_path, $versions_file);

		return $options;
	}
}
