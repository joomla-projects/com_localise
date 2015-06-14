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
				'warning');
		}
	}

	if ($allow_develop == 1)
	{

	$report .= '<b>Source reference</b><br />';

		foreach ($clients as $client)
		{
			if ($last_source[$client] == '')
			{
				$report .= strtoupper($client) . ' client is actualy using Joomla ' . $installed_version . ' language files as baseline.';
				$version[$client] = $installed_version;
			}
			else
			{
				$report .= strtoupper($client) . ' client is actualy using Joomla ' . $last_source[$client] . ' language files as baseline.';
				$version[$client] = $last_source[$client];
			}

			if ($last_source[$client] == $installed_version)
			{
				$report .= ' This version matches with the local installed instance.';
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

		if ($equal_versions == 0)
		{
			$report .= '<br />Please, note that now the clients are not using the same language files version.<br />';
		}
		else
		{
			$report .= '<br />All the clients are using the same language files version.<br />';

		}

		foreach ($clients as $client)
		{
			if (!empty($last_source[$client]) && $installed_version != $last_source[$client])
			{
				$report .= '<br />Note that now the client ' . strtoupper($client) . ' is using customised reference files as source en-GB baseline.<br />';
			}
		}

		$report .= '<br /><b>Target reference</b><br />';

		$report .= 'The detected modified values or new keys in development to show are comming from the target language files within <b>' . $gh_branch . '</b> branch at Github.<br /><br />';

		foreach ($clients as $client)
		{
			$last_update = 'gh_' . $client . '_last_update';
			$target_updates[$client] = $params->get($last_update, '');

			if ($target_updates[$client] == '')
			{
				$report .= 'The develop files of client ' . strtoupper($client) . ' never have been updated.<br />';
			}
			else
			{
				$report .= 'The develop files of client ' . strtoupper($client) . ' have been updated on ' . $target_updates[$client] . '.<br />';
			}
		}
	}
	else
	{
		$report .= '<br /><b>Target reference</b><br />';
		$report .= 'No modified values or new keys from development branches will be displayed as "Enable development reference language" is disabled.<br />';
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
