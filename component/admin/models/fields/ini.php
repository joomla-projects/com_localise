<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field Ini class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldIni extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Ini';

	/**
	 * Base path for editor files
	 */
	protected $basePath = 'media/editors/codemirror/';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		JHtml::_('behavior.framework');

		// Load Codemirror
		JHtml::_('script', 'media/editors/codemirror/lib/codemirror.js', false, false, false, false);
		JHtml::_('stylesheet', 'media/editors/codemirror/lib/codemirror.css');

		// Load Joomla language ini parser
		JHtml::_('script', 'media/com_localise/js/parseini.js', false, false, false, false);
		JHtml::_('stylesheet', 'media/com_localise/css/localise.css');

		$rows   = (string) $this->element['rows'];
		$cols   = (string) $this->element['cols'];
		$class  = (string) $this->class ? ' class="' . (string) $this->class . '"' : ' class="text_area"';

		$options = new stdClass;

		$options->mode = 'text/parseini';
		$options->tabMode = 'default';
		$options->smartIndent = true;
		$options->lineNumbers = true;
		$options->lineWrapping = true;
		$options->autoCloseBrackets = true;
		$options->showTrailingSpace = true;
		$options->styleActiveLine = true;
		$options->gutters = array('CodeMirror-linenumbers', 'CodeMirror-foldgutter', 'breakpoints');

		$html = array();
		$html[] = '<textarea' . $class . ' name="' . $this->name . '" id="' . $this->id . '" cols="' . $cols . '" rows="' . $rows . '">'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
		$html[] = '<script type="text/javascript">';
		$html[] = '(function() {';
		$html[] = '		var editor = CodeMirror.fromTextArea(document.getElementById("' . $this->id . '"), ' . json_encode($options) . ');';
		$html[] = '		editor.setOption("extraKeys", {';
		$html[] = '			"Ctrl-Q": function(cm) {';
		$html[] = '				cm.setOption("fullScreen", !cm.getOption("fullScreen"));';
		$html[] = '			},';
		$html[] = '			"Esc": function(cm) {';
		$html[] = '				if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);';
		$html[] = '			}';
		$html[] = '		});';
		$html[] = '		editor.on("gutterClick", function(cm, n) {';
		$html[] = '			var info = cm.lineInfo(n)';
		$html[] = '			cm.setGutterMarker(n, "breakpoints", info.gutterMarkers ? null : makeMarker())';
		$html[] = '		})';
		$html[] = '		function makeMarker() {';
		$html[] = '			var marker = document.createElement("div")';
		$html[] = '			marker.style.color = "#822"';
		$html[] = '			marker.innerHTML = "●"';
		$html[] = '			return marker';
		$html[] = '		}';
		$html[] = '		Joomla.editors.instances[\'' . $this->id . '\'] = editor;';
		$html[] = '})()';
		$html[] = '</script>';

		return implode("\n", $html);
	}

	/**
	 * Get the save javascript code.
	 *
	 * @return  string
	 */
	public function save()
	{
		return "document.getElementById('" . $this->id . "').value = Joomla.editors.instances['" . $this->id . "'].getValue();\n";
	}
}
