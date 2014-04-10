<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('stylesheet','com_localise/localise.css', null, true);

$parts = explode('-', $this->state->get('translation.reference'));
$src   = $parts[0];
$parts = explode('-', $this->state->get('translation.tag'));
$dest  = $parts[0];

$document = JFactory::getDocument();
//$document->addScript('http://www.google.com/jsapi');
$document->addScriptDeclaration("
	if (typeof(Localise) === 'undefined') {
		Localise = {};
	}
	Localise.language_src = '" . $src . "';
	Localise.language_dest = '" . $dest . "';
");

$fieldSets = $this->form->getFieldsets();
$sections  = $this->form->getFieldsets('strings');
$ftpSets   = $this->formftp->getFieldsets();

//Prepare Bing translation
JText::script('COM_LOCALISE_BINGTRANSLATING_NOW');
?>
<script type="text/javascript">
	var bingTranslateComplete = false, translator;
	var Localise = {};
	Localise.language_src = '<?php echo $src; ?>';
	Localise.language_dest = '<?php echo $dest; ?>';

	function AzureTranslator(obj, targets, i, token, transUrl){
		var idname = jQuery(obj).attr('rel');
		if(translator && !translator.status){
			alert(Joomla.JText._('COM_LOCALISE_BINGTRANSLATING_NOW'));
			return;
		}

		translator =jQuery.ajax({
			type:'POST',
			uril:'index.php',
			data:'option=com_localise&view=translator&format=json&id=<?php echo $this->form->getValue('id');?>&from=<?php echo $src;?>&to=<?php echo $dest;?>&text='+encodeURI(jQuery('#'+idname+'text').val())+'&'+token+'=1',
			dataType:'json',
			success:function(res){
				if(res.success){
					jQuery('#'+idname).val(res.text);
				}
				if(targets && targets.length > (i+1)){
					AzureTranslator(targets[i+1], targets, i+1, token);
					jQuery('html,body').animate({scrollTop:jQuery(targets[i+1]).offset().top-150}, 0);
				} else {
					bingTranslateComplete = false;
					if(targets.length > 1)
						jQuery('html,body').animate({scrollTop:0}, 0);
				}
			}
		});
	}

	function returnAll()
	{
		$$('i.return').each(function(e){
			if(e.click)
				e.click();
			else
				e.onclick();
		});
	}

	function translateAll()
	{
		if(bingTranslateComplete){
			alert(Joomla.JText._('COM_LOCALISE_BINGTRANSLATING_NOW'));
			return false;
		}

		bingTranslateComplete = true;
		var targets = $$('i.translate');
		AzureTranslator(targets[0], targets, 0, '<?php echo JSession::getFormToken();?>');
	}

	/* if (typeof(google) !== 'undefined')
	{
		google.load('language', '1');
		google.setOnLoadCallback(null);
	} */
 
	Joomla.submitbutton = function(task)
	{
		if (task == 'translation.cancel' || document.formvalidator.isValid(document.id('localise-translation-form')))
		{
			Joomla.submitform(task, document.getElementById('localise-translation-form'));
		}
	}
</script>
<form action="" method="post" name="adminForm" id="localise-translation-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Localise Translation -->
		<div class="span12 form-horizontal">
			<fieldset>
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => $this->ftp ? 'ftp' : 'default')); ?>
					<?php if ($this->ftp) : ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'ftp', JText::_($ftpSets['ftp']->label, true)); ?>
						<?php if (!empty($ftpSets['ftp']->description)):?>
						<p class="tip"><?php echo JText::_($ftpSets['ftp']->description); ?></p>
						<?php endif;?>
						<?php if (JError::isError($this->ftp)): ?>
						<p class="error"><?php echo JText::_($this->ftp->message); ?></p>
						<?php endif; ?>
						<?php foreach($this->formftp->getFieldset('ftp',false) as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
						<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endif; ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'default', JText::_($fieldSets['default']->label, true)); ?>
						<?php if (!empty($fieldSets['default']->description)):?>
						<p class="alert alert-info"><?php echo JText::_($fieldSets['default']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('default') as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
						<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'strings', JText::_('COM_LOCALISE_FIELDSET_TRANSLATION_STRINGS')); ?>
						<div class="key">
							<div class="accordion" id="com_localise_legend_translation">
								<div class="accordion-group">
									<div class="accordion-heading">
										<a class="accordion-toggle alert-info" data-toggle="collapse" data-parent="com_localise_legend_translation" href="#legend">
											<?php echo JText::_($fieldSets['legend']->label);?>
										</a>
									</div>
									<div id="legend" class="accordion-body collapse">
										<div class="accordion-inner">
											<?php if (!empty($fieldSets['legend']->description)):?>
											<p class="tip"><?php echo JText::_($fieldSets['legend']->description); ?></p>
											<?php endif;?>
											<ul class="adminformlist">
												<?php foreach($this->form->getFieldset('legend') as $field): ?>
												<li>
													<?php echo $field->label; ?>
													<?php echo $field->input; ?>
												</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<div id="translationbar">
								<a href="javascript:void(0);" class="btn bnt-small" id="translateall" onclick="translateAll();">
									<i class="icon-translate-bing"></i> <?php echo JText::_('COM_LOCALISE_BUTTON_TRANSLATE_ALL');?>
								</a>
								<a href="javascript:void(0);" class="btn bnt-small" onclick="returnAll();">
									<i class="icon-reset"></i> <?php echo JText::_('COM_LOCALISE_BUTTON_RESET_ALL');?>
								</a>
							</div>
							<?php
								if (count($sections) > 1) :
									echo '<br />';
									echo JHtml::_('bootstrap.startAccordion', 'localise-translation-sliders');
									$i = 0;
									foreach ($sections as $name => $fieldSet) :
										echo JHtml::_('bootstrap.addSlide', 'localise-translation-sliders', JText::_($fieldSet->label), 'collapse' . $i++);
							?>
							<ul class="adminformlist">
								<?php foreach ($this->form->getFieldset($name) as $field) :?>
								<li>
									<?php echo $field->label; ?>
									<?php echo $field->input; ?>
								</li>
								<?php endforeach;?>
							</ul>
							<?php
										echo JHtml::_('bootstrap.endSlide');
									endforeach;
									echo JHtml::_('bootstrap.endAccordion');
							?>
							<?php else : ?>
							<ul class="adminformlist">
								<?php $sections = array_keys($sections);?>
								<?php foreach ($this->form->getFieldset($sections[0]) as $field) :?>
								<li>
									<?php echo $field->label; ?>
									<?php echo $field->input; ?>
								</li>
								<?php endforeach;?>
							</ul>
							<?php endif;?>
						</div>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_($fieldSets['permissions']->label, true)); ?>
						<?php if (!empty($fieldSets['permissions']->description)):?>
						<p class="tip"><?php echo JText::_($fieldSets['permissions']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('permissions') as $field) : ?>
						<div class="control-group form-vertical">
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
						<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<input type="hidden" name="task" value="" />
					<?php echo JHtml::_('form.token'); ?>

				<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			</fieldset>
		</div>
		<!-- End Localise Translation -->
	</div>
</form>
