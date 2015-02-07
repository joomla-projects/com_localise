<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<?php foreach($this->items as $i => $item) : ?>
	<tr>
		<td width="20" class="center hidden-phone"><?php echo $i + 1; ?></td>
		<td width="20" class="center hidden-phone">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td><?php echo $item->language; ?>
		<td><?php echo $item->scope; ?>
		<td><?php echo $item->type; ?>
		<td><?php echo $item->user; ?>
		<td><?php echo $item->project; ?>
	</tr>
<?php endforeach; ?>
