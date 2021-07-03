<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('stylesheet', 'com_localise/localise.css', null, true);

$parts = explode('-', $this->state->get('translation.reference'));
$src   = $parts[0];
$parts = explode('-', $this->state->get('translation.tag'));
$dest  = $parts[0];

// No use to filter if target language is also reference language
if ($this->state->get('translation.reference') != $this->state->get('translation.tag'))
{
	$istranslation = 1;
}
else
{
	$istranslation = 0;
}

$params            = JComponentHelper::getParams('com_localise');
$ref_tag           = $params->get('reference', 'en-GB');
$allow_develop     = $params->get('gh_allow_develop', 0);
$saved_ref         = $params->get('customisedref', 0);
$source_ref        = $saved_ref;
$istranslation     = $this->item->istranslation;
$installed_version = new JVersion;
$installed_version = $installed_version->getShortVersion();

	if ($saved_ref == 0)
	{
		$source_ref = $installed_version;
	}

	if ($saved_ref != 0 && $allow_develop == 1 && $ref_tag == 'en-GB' && $istranslation == 0)
	{
		JFactory::getApplication()->enqueueMessage(
		JText::sprintf('COM_LOCALISE_NOTICE_EDIT_REFERENCE_HAS_LIMITED_USE', $source_ref),
		'notice');
	}

$input	= JFactory::getApplication()->input;
$posted	= $input->post->get('jform', array(), 'array');

$has_translatedkeys   = !empty($this->item->translatedkeys) ? 1 : 0;
$has_untranslatedkeys = !empty($this->item->untranslatedkeys) ? 1 : 0;
$has_unchangedkeys    = !empty($this->item->unchangedkeys) ? 1 : 0;
$has_textchangedkeys  = !empty($this->item->textchangedkeys) ? 1 : 0;

if (isset($posted['select']['keystatus'])
	&& !empty($posted['select']['keystatus'])
	&& $posted['select']['keystatus'] != 'allkeys'
	)
{
	$filter			= $posted['select']['keystatus'];
	$keystofilter	= array ($this->item->$filter);
	$tabchoised		= 'strings';
}
elseif (empty($posted['select']['keystatus']))
{
	$filter			= 'allkeys';
	$keystofilter	= array();
	$tabchoised		= 'default';
}
else
{
	$filter			= 'allkeys';
	$keystofilter	= array();
	$tabchoised		= 'default';
}

$document = JFactory::getDocument();
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

// Prepare Bing translation
JText::script('COM_LOCALISE_BINGTRANSLATING_NOW');
JText::script('COM_LOCALISE_CONFIRM_TRANSLATION_SAVE');
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

	Joomla.submitbutton = function(task)
	{
		var complete = <?php echo (int) JComponentHelper::getParams('com_localise')->get('complete', 0); ?>;

		if ((task == 'translation.apply' || task == 'translation.save') && document.formvalidator.isValid(document.id('localise-translation-form')))
		{
			if (complete)
			{
				if (confirm(Joomla.JText._('COM_LOCALISE_CONFIRM_TRANSLATION_SAVE')))
				{
					Joomla.submitform(task, document.getElementById('localise-translation-form'));
				}
			}
			else
			{
				Joomla.submitform(task, document.getElementById('localise-translation-form'));
			}
		}
		else if (task == 'translation.cancel')
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
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => $this->ftp ? 'ftp' : $tabchoised)); ?>
					<?php if ($this->ftp) : ?>
						<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'ftp', JText::_($ftpSets['ftp']->label, true)); ?>
							<?php if (!empty($ftpSets['ftp']->description)):?>
								<p class="tip"><?php echo JText::_($ftpSets['ftp']->description); ?></p>
							<?php endif;?>
							<?php if (JError::isError($this->ftp)): ?>
								<p class="error"><?php echo JText::_($this->ftp->message); ?></p>
							<?php endif; ?>
							<?php foreach($this->formftp->getFieldset('ftp',false) as $field) : ?>
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
						<?php if (!empty($fieldSets['default']->description)) : ?>
							<p class="alert alert-info"><?php echo JText::_($fieldSets['default']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('default') as $field) : ?>
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
						<div class="accordion" id="com_localise_legend_translation">
							<div class="accordion-group">
								<div class="accordion-heading">
									<a class="accordion-toggle alert-info" data-toggle="collapse" data-parent="com_localise_legend_translation" href="#legend">
										<?php echo JText::_($fieldSets['legend']->label);?>
									</a>
								</div>
								<div id="legend" class="accordion-body collapse">
									<div class="accordion-inner">
										<?php if (!empty($fieldSets['legend']->description)) : ?>
											<p class="tip"><?php echo JText::_($fieldSets['legend']->description); ?></p>
										<?php endif; ?>
										<ul class="adminformlist">
										<?php foreach($this->form->getFieldset('legend') as $field) : ?>
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
						<div class="key">
							<div id="translationbar">
								<?php //if ($istranslation) : ?>
									<div class="pull-left">
										<?php foreach($this->form->getFieldset('select') as $field): ?>
											<?php if ($field->type != "Spacer") : ?>
												<?php
													$field->value = $filter;
													echo JText::_('JSEARCH_FILTER_LABEL');
													echo $field->input;
												?>
											<?php else : ?>
												<?php echo $field->label; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								<?php //endif; ?>
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
								<?php foreach ($this->form->getFieldset($name) as $field) : ?>
								<?php
									$showkey = 0;

									if ($filter != 'allkeys' && !empty($keystofilter))
									{
										foreach ($keystofilter as $data => $ids)
										{
											foreach ($ids as $keytofilter)
											{
												$showkey = 0;
												$pregkey = preg_quote('<b>'. $keytofilter .'</b>', '/<>');

												if (preg_match("/$pregkey/", $field->label))
												{
													$showkey = 1;
													break;
												}
											}
										}

										if ($showkey == '1')
										{
										?>
											<li>
												<?php echo $field->label; ?>
												<?php echo $field->input; ?>
											</li>
										<?php
										}
										else
										{
										?>
											<div style="display:none;">
												<?php echo $field->label; ?>
												<?php echo $field->input; ?>
											</div>
										<?php
										}
									}
									elseif ($filter == 'allkeys')
									{
									?>
										<li>
											<?php echo $field->label; ?>
											<?php echo $field->input; ?>
										</li>
									<?php
									}
									?>
								<?php endforeach; ?>
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
									<?php
										$showkey = 0;

										if ($filter != 'allkeys' && !empty($keystofilter))
										{
											foreach ($keystofilter as $data  => $ids)
											{
												foreach ($ids as $keytofilter)
												{
													$showkey = 0;
													$pregkey = preg_quote('<b>'.$keytofilter.'</b>', '/<>');

													if (preg_match("/$pregkey/", $field->label))
													{
														$showkey = 1;
														break;
													}
												}
											}

											if ($showkey == '1')
											{
											?>
												<li>
													<?php echo $field->label; ?>
													<?php echo $field->input; ?>
												</li>
											<?php
											}
											else
											{
											?>
												<div style="display:none;">
													<?php echo $field->label; ?>
													<?php echo $field->input; ?>
												</div>
											<?php
											}
										}
										elseif ($filter == 'allkeys')
										{
										?>
											<li>
												<?php echo $field->label; ?>
												<?php echo $field->input; ?>
											</li>
										<?php
										}
									?>
								<?php endforeach; ?>
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
<script type="text/javascript">
var has_translatedkeys   = '<?php echo $has_translatedkeys; ?>';
var has_untranslatedkeys = '<?php echo $has_untranslatedkeys; ?>';
var has_unchangedkeys    = '<?php echo $has_unchangedkeys; ?>';
var has_textchangedkeys  = '<?php echo $has_textchangedkeys; ?>';

	if (has_translatedkeys=='0')
	{
		var x = document.getElementById("jform_select_keystatus").options[2].disabled = true;
	}

	if (has_untranslatedkeys=='0')
	{
		var x = document.getElementById("jform_select_keystatus").options[3].disabled = true;
	}

	if (has_unchangedkeys=='0')
	{
		var x = document.getElementById("jform_select_keystatus").options[4].disabled = true;
	}

	if (has_textchangedkeys=='0')
	{
		var x = document.getElementById("jform_select_keystatus").options[5].disabled = true;
	}
</script>
