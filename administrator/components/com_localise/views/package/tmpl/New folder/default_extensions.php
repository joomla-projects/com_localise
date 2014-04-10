<?php defined('_JEXEC') or die('Restricted access');
echo $this->pane->startPanel($this->panel_title, $this->panel . "-page");
?>

<table class="adminlist">
  <thead>
    <?php echo $this->loadTemplate('header'); ?>
  </thead>
  <tfoot>
    <?php echo $this->loadTemplate('footer'); ?>
  </tfoot>
  <tbody>
    <?php echo $this->loadTemplate('body'); ?>
  </tbody>
</table>
<?php echo $this->pane->endPanel();
