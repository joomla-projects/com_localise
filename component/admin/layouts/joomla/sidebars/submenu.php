<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');
?>

	<script type="text/javascript">
		jQuery(function()
		{
			Joomla.toggleSidebar(true);
		});
	</script>

<div class="toggle-sidebar">
	<?php if (JFile::exists(JPATH_ROOT . '/layouts/joomla/searchtools/default/togglesidebar.php')) : ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default.togglesidebar'); ?>
	<?php else : ?>
		<?php echo JLayoutHelper::render('joomla.sidebars.toggle'); ?>
	<?php endif; ?>
</div>
<div id="sidebar">
	<div class="sidebar-nav">
		<?php if ($displayData->displayMenu) : ?>
		<ul id="submenu" class="nav nav-list">
			<?php foreach ($displayData->list as $item) :
			if (isset ($item[2]) && $item[2] == 1) : ?>
				<li class="active">
			<?php else : ?>
				<li>
			<?php endif;
			if ($displayData->hide) : ?>
				<a class="nolink"><?php echo $item[0]; ?></a>
			<?php else :
				if (strlen($item[1])) : ?>
					<a href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a>
				<?php else : ?>
					<?php echo $item[0]; ?>
				<?php endif;
			endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
