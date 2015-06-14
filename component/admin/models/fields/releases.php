<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

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
		$attributes    = '';
		$params        = JComponentHelper::getParams('com_localise');
		$versions_path = JPATH_ROOT
				. '/administrator/components/com_localise/customisedref/stable_joomla_releases.txt';
		$versions_file = file_get_contents($versions_path);
		$versions      = preg_split("/\\r\\n|\\r|\\n/", $versions_file);

		$gh_user       = 'joomla';
		$gh_project    = 'joomla-cms';
		$gh_token      = $params->get('gh_token', '');

		$options = new JRegistry;

		if (!empty($gh_token))
		{
			$options->set('gh.token', $gh_token);
			$github = new JGithub($options);
		}
		else
		{
			// Without a token runs fatal.
			// $github = new JGithub;

			// Trying with a 'read only' public repositories token
			// But base 64 encoded to avoid Github alarms sharing it.
			$gh_token = base64_decode('MzY2NzYzM2ZkMzZmMWRkOGU5NmRiMTdjOGVjNTFiZTIyMzk4NzVmOA==');
			$options->set('gh.token', $gh_token);
			$github = new JGithub($options);
		}

		try
		{
			$releases = $github->repositories->get(
					$gh_user,
					$gh_project . '/releases'
					);

			// Allowed tricks.
			// Configured to 0 the 2.5.x series are not allowed. Configured to 1 it is allowed.
			$allow_25x = 1;

			foreach ($releases as $release)
			{
				$tag_name = $release->tag_name;
				$tag_part = explode(".", $tag_name);
				$undoted  = str_replace('.', '', $tag_name);
				$excluded = 0;

				if ($tag_part[0] == '2' && $allow_25x == '0')
				{
					$excluded = 1;
				}
				elseif ($tag_part[0] != '3' && $tag_part[0] != '2')
				{
					// Exclude platforms or similar stuff.
					$excluded = 1;
				}

				// Filtering also by "is_numeric" disable betas or similar releases.
				if (!in_array($tag_name, $versions) && is_numeric($undoted) && $excluded == 0)
				{
					$versions[] = $tag_name;
					JFactory::getApplication()->enqueueMessage(
						JText::sprintf('COM_LOCALISE_NOTICE_NEW_VERSION_DETECTED', $tag_name),
						'notice');
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
