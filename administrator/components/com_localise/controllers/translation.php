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
 * Translation Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseControllerTranslation extends JControllerForm
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param  string  The model name. Optional.
	 * @param  string  The class prefix. Optional.
	 * @param  array   Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Translation', $prefix = 'LocaliseModel', $config = array('ignore_request' => false)) 
	{
		return parent::getModel($name, $prefix, $config);
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
		return JFactory::getUser()->authorise('localise.edit', 'com_localise.' . $data[$key]);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param  int     $recordId  The primary key id for the item.
	 * @param  string  $urlVar    The name of the URL variable for the id.
	 *
	 * @return string  The arguments to append to the redirect URL.
	 * @since  1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') 
	{
		// Get the infos
		$client   = JRequest::getVar('client'  , '', 'default', 'cmd');
		$tag      = JRequest::getVar('tag'     , '', 'default', 'cmd');
		$filename = JRequest::getVar('filename', '', 'default', 'cmd');
		$storage  = JRequest::getVar('storage' , '', 'default', 'cmd');

		// Get the append string
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append.= '&client=' . $client . '&tag=' . $tag . '&filename=' . $filename . '&storage=' . $storage;
		return $append;
	}
}
