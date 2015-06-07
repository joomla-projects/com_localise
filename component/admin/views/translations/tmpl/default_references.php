<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$params        = JComponentHelper::getParams('com_localise');
$ref_tag       = $params->get('reference', 'en-GB');
$allow_develop = $params->get('gh_allow_develop', 0);
$customisedref = $params->get('customisedref', '0');
$gh_branch     = $params->get('gh_branch', 'master');

	if ($customisedref == '0')
	{
		$installed_version = new JVersion();
		$local_version     = $installed_version->getShortVersion();
	}

	if ($allow_develop == 1)
	{
		$installed_version = new JVersion();
		$local_version     = $installed_version->getShortVersion();
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

			</div>
		</div>
	</div>
</div>
