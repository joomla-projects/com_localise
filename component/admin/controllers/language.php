<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Language Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseControllerLanguage extends JControllerForm
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Language', $prefix = 'LocaliseModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
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
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('localise.edit', $this->option . '.' . $data[$key]);
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
		$input = JFactory::getApplication()->input;
		$client = $input->get('client', '');

		if (empty($client))
		{
			$data = $input->get('jform', array(), 'array');

			if ($data)
			{
				$client = $data['client'];
			}
		}

		if (empty($client))
		{
			$select = $input->get('filters', array(), 'array');
			$client = isset($select['select']['client']) ? $select['select']['client'] : 'site';
		}

		$tag = $input->get('tag', '');

		if (empty($tag))
		{
			$tag = isset($data['tag']) ? $data['tag'] : '';
		}

		// Get the append string
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&client=' . $client . '&tag=' . $tag;

		return $append;
	}

	/**
	 * Delete the language
	 *
	 * @return void
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

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

	/**
	 * Method for copying the language files from the reference language.
	 *
	 * @return  void
	 *
	 * @since   4.0.17
	 */
	public function copy()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app      = JFactory::getApplication();
		$model    = $this->getModel();
		$params   = JComponentHelper::getParams('com_localise');
		$data     = $app->input->get('jform', array(), 'array');
		$recordId = $app->input->getInt('id');

		$client  = $data['client'];
		$tag     = $data['tag'];
		$ref_tag = $params->get('reference', 'en-GB');

		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=language' . $this->getRedirectToItemAppend($recordId), false));

		// Call model's copy method
		if (!$model->copy())
		{
			$app->enqueueMessage(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_COULD_NOT_COPY_FILES', $client, $ref_tag, $tag), 'error');

			return false;
		}

		$this->setMessage(JText::sprintf('COM_LOCALISE_LANGUAGE_COPY_SUCCESS', $client, $ref_tag, $tag));
	}
}
