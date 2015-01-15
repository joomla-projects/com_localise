<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="accordion" id="accordionLegend">
	<div class="accordion-group">
		<div class="accordion-heading alert-info">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="accordionLegend" href="#legend">
				<?php echo JText::_('COM_LOCALISE_SLIDER_TRANSLATIONS_LEGEND'); ?>
			</a>
		</div>
		<div id="legend" class="accordion-body collapse">
			<div class="accordion-inner">
				<table width="100%" class="adminlist">
					<thead>
					<tr>
						<th width="50"><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_LEGEND_ICON'); ?></th>
						<th><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_LEGEND_DESC'); ?></th>
						<th width="100"><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_LEGEND_USAGE'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php $i = 1; ?>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STORAGE_GLOBAL'); ?>"
								class="btn btn-micro disabled icon-16-global hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STORAGE_GLOBAL_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_STORAGE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STORAGE_LOCAL'); ?>"
								class="btn btn-micro disabled icon-16-local hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STORAGE_LOCAL_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_STORAGE'); ?></td>
					</tr>
					<?php foreach ($this->packages as $package): ?>
						<tr class="row<?php echo $i = 1 - $i; ?>">
							<?php if ($package->core == true) : ?>
								<td align="center"><i
								title="<?php echo JText::_($package->title); ?>"
								class="btn btn-micro disabled icon-16-core hasTooltip"></i></td>
							<?php else : ?>
								<td align="center"><i
								title="<?php echo JText::_($package->title); ?>"
								class="btn btn-micro disabled icon-16-other hasTooltip"></i></td>
							<?php endif; ?>
							<td><?php echo JText::_($package->description); ?></td>
							<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_ORIGIN'); ?></td>
						</tr>
					<?php endforeach; ?>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_THIRDPARTY'); ?>"
								class="btn btn-micro disabled icon-16-thirdparty hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_THIRDPARTY_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_ORIGIN'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_OVERRIDE'); ?>"
								class="btn btn-micro disabled icon-16-override hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_OVERRIDE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_ORIGIN_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_ERROR'); ?>"
								class="btn btn-micro disabled icon-16-error hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_ERROR_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_STATE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_INLANGUAGE'); ?>"
								class="btn btn-micro disabled icon-16-inlanguage hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_INLANGUAGE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_STATE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_NOTINREFERENCE'); ?>"
								class="btn btn-micro disabled icon-16-notinreference hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_NOTINREFERENCE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_STATE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_UNEXISTING'); ?>"
								class="btn btn-micro disabled icon-16-unexisting hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_UNEXISTING_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_STATE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_COMPONENT'); ?>"
								class="btn btn-micro disabled icon-16-component hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_COMPONENT_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_MODULE'); ?>"
								class="btn btn-micro disabled icon-16-module hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_MODULE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PLUGIN'); ?>"
								class="btn btn-micro disabled icon-16-plugin hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PLUGIN_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_TEMPLATE'); ?>"
								class="btn btn-micro disabled icon-16-template hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_TEMPLATE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PACKAGE'); ?>"
								class="btn btn-micro disabled icon-16-package hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PACKAGE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_LIBRARY'); ?>"
								class="btn btn-micro disabled icon-16-library hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_LIBRARY_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_FILE'); ?>"
								class="btn btn-micro disabled icon-16-file hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_FILE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_JOOMLA'); ?>"
								class="btn btn-micro disabled icon-16-joomla hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_JOOMLA_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_TYPE'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i title="<?php echo JText::_('COM_LOCALISE_OPTION_CLIENT_SITE'); ?>"
						                      class="btn btn-micro disabled icon-16-site hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_CLIENT_SITE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_CLIENT'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_CLIENT_ADMINISTRATOR'); ?>"
								class="btn btn-micro disabled icon-16-administrator hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_CLIENT_ADMINISTRATOR_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_CLIENT'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i title="<?php echo JText::_('COM_LOCALISE_OPTION_CLIENT_INSTALLATION'); ?>"
						                      class="btn btn-micro disabled icon-16-installation hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_CLIENT_INSTALLATION_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_CLIENT'); ?></td>
					</tr>
					<tr class="row<?php echo $i = 1 - $i; ?>">
						<td align="center"><i
								title="<?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_REFERENCE'); ?>"
								class="btn btn-micro disabled icon-16-reference hasTooltip"></i></td>
						<td><?php echo JText::_('COM_LOCALISE_OPTION_TRANSLATIONS_REFERENCE_DESC'); ?></td>
						<td><?php echo JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_LANGUAGE'); ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
