<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Language Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseControllerLanguage extends JControllerForm
{
	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param  array  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array()) 
	{
		return JFactory::getUser()->authorise('localise.create', $this->option);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param  array  An array of input data.
	 * @param  string  The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id') 
	{
		return JFactory::getUser()->authorise('localise.edit', $this->option . '.' . $data[$key]);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param  string  The model name. Optional.
	 * @param  string  The class prefix. Optional.
	 * @param  array   Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Language', $prefix = 'LocaliseModel', $config = array('ignore_request' => false)) 
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   int     $recordId  The primary key id for the item.
	 * @param   string  $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') 
	{
		// Get the infos
		$client = JRequest::getVar('client', '', 'default', 'cmd');

		if (empty($client)) 
		{
			$select = JRequest::getVar('filters', array(), 'default', 'array');
			$client = isset($select['select']['client']) ? $select['select']['client'] : 'site';
		}

		if (empty($client)) 
		{
			$data = JRequest::getVar('jform', array(), 'default', 'array');
			$client = isset($data['client']) ? $data['client'] : 'site';
		}

		if (empty($client)) 
		{
			$client = 'site';
		}

		$tag = JRequest::getVar('tag', '', 'default', 'cmd');
		if (empty($tag)) 
		{
			$tag = isset($data['tag']) ? $data['tag'] : '';
		}

		// Get the append string
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append.= '&client=' . $client . '&tag=' . $tag;

		return $append;
	}

	/**
	 * Delete the language
	 */
	public function delete()
	{
		// Get the model.
		$model = $this->getModel();

		// Remove the items.
		if (!$model->delete()) 
		{
			$msg = implode("<br />", $model->getErrors());
			$type = 'error';
		}
		else
		{
			$msg = JText::_('COM_LOCALISE_MSG_LANGUAGES_REMOVED');
			$type = 'message';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=languages', false), $msg, $type);
	}
}
