<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params            = JComponentHelper::getParams('com_localise');
$ref_tag           = $params->get('reference', 'en-GB');
$allow_develop     = $params->get('gh_allow_develop', 0);
$customisedref     = $params->get('customisedref', '0');
$gh_branch         = $params->get('gh_branch', 'master');

$last_source       = LocaliseHelper::getLastsourcereference();
$has_installation  = LocaliseHelper::hasInstallation();

	if ($has_installation)
	{
		$clients = array('administrator', 'site', 'installation');
	}
	else
	{
		$clients = array('administrator', 'site');
	}

	$installed_version = new JVersion;
	$installed_version = $installed_version->getShortVersion();

	if ($customisedref == '0')
	{
		$customisedref = $installed_version;
	}

	$report  = '';

	// When 'Enable development reference language' is disaled and any client is using
	// A distinct version than local installed files, it will be noticed
	// due with the feature disabled the normal use is with local installed files as source reference.

	foreach ($clients as $client)
	{
		if (!empty($last_source[$client]) && $installed_version != $last_source[$client] && $allow_develop == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf('COM_LOCALISE_WARNING_DISABLED_ALLOW_DEVELOP_WITHOUT_LOCAL_SET',
					$client,
					$last_source[$client],
					$installed_version),
				'notice');
		}
	}

	if ($allow_develop == 1)
	{
		$report .= '<strong>' . JText::_('COM_LOCALISE_REFERENCES_REPORT_SOURCE') . '</strong><br />';

		foreach ($clients as $client)
		{
			if ($last_source[$client] == '')
			{
				$instance = JText::_('COM_LOCALISE_LOCAL_INSTALLED_INSTANCE');
				$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_CLIENT', strtoupper($client), $instance);
				$version[$client] = '0';
			}
			else
			{
				$instance = $last_source[$client];
				$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_CLIENT', strtoupper($client), $instance);
				$version[$client] = $last_source[$client];
			}

			if ($last_source[$client] == '')
			{
				$report .= ' ' . JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_INSTALLED', $installed_version);
			}

			$report .= '<br />';

		}

		$equal_versions = 1;

		if ($version['administrator'] != $version['site'])
		{
			$equal_versions = 0;
		}

		if ($has_installation)
		{
			if ($version['administrator'] != $version['installation'] || $version['site'] != $version['installation'])
			{
				$equal_versions = 0;
			}
		}

		if (($version['administrator'] == '0' || $version['administrator'] == $installed_version)
		&& ($version['site'] == '0' || $version['site'] == $installed_version))
		{
			$matches = 1;

			if ($has_installation && $matches == 1)
			{
				if ($version['installation'] == '0' || $version['installation'] == $installed_version)
				{
					$equal_versions = 2;
				}
			}
			elseif ($matches == 1)
			{
				$equal_versions = 2;
			}
		}

		$report .= '<i>';

		// Notes
		if ($equal_versions == 0)
		{
			$report .= '<br />' . JText::_('COM_LOCALISE_REFERENCES_REPORT_NOT_EQUAL') . '<br /><br />';
		}
		elseif ($equal_versions == 2)
		{
			$report .= '<br />' . JText::_('COM_LOCALISE_REFERENCES_REPORT_CUSTOM') . '<br /><br />';
		}
		else
		{
			$report .= '<br />' . JText::_('COM_LOCALISE_REFERENCES_REPORT_EQUAL') . '<br /><br />';

		}

		foreach ($clients as $client)
		{
			if (!empty($last_source[$client]))
			{
				$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_CUSTOM_NOTE', strtoupper($client));

				if ($last_source[$client] == $installed_version)
				{
					$report .= JText::_('COM_LOCALISE_REFERENCES_REPORT_MATCH_NOTE');
				}

				$report .= '<br />';
			}
			else
			{
				$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_VERSION_NOTE', strtoupper($client), $installed_version);
				$report .= '<br />';
			}
		}

		$report .= '</i>';
		// End notes

		$report .= '<br /><strong>' . JText::_('COM_LOCALISE_REFERENCES_REPORT_TARGET') . '</strong><br />';

		$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_TARGET_REFERENCE', $gh_branch) . '<br /><br />';

		foreach ($clients as $client)
		{
			$last_update = 'gh_' . $client . '_last_update';
			$target_updates[$client] = $params->get($last_update, '');

			if ($target_updates[$client] == '')
			{
				$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_NEVER_UPDATED', strtoupper($client)) . '<br />';
			}
			else
			{
				$report .= JText::sprintf('COM_LOCALISE_REFERENCES_REPORT_UPDATED', strtoupper($client), $target_updates[$client]) . '<br />';
			}
		}
	}
	else
	{
		$report .= '<br /><strong>' . JText::_('COM_LOCALISE_REFERENCES_REPORT_TARGET') . '</strong><br />';
		$report .= JText::_('COM_LOCALISE_REFERENCES_REPORT_NO_DEVELOP');
	}
?>
<div class="accordion" id="accordionReferences">
	<div class="accordion-group">
		<div class="accordion-heading alert-info">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="accordionReferences" href="#references">
				<?php echo JText::_('COM_LOCALISE_SLIDER_TRANSLATIONS_REFERENCES'); ?>
			</a>
		</div>
		<div id="references" class="accordion-body collapse">
			<div class="accordion-inner">
				<?php echo $report; ?>
			</div>
		</div>
	</div>
</div>
