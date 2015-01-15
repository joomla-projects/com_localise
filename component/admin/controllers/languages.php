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
 * Languages Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseControllerLanguages extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $text_prefix  = 'COM_LOCALISE_LANGUAGES';

	/**
	 * Method to purge the localise table.
	 *
	 * @return  void
	 *
	 * @since   3.3
	 */
	public function purge()
	{
		$model = $this->getModel('languages');
		$model->purge();
		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=languages', false));
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = '', $prefix = '', $config = array())
	{
		if (empty($name))
		{
			$name = 'Language';
		}

		$config = array_merge($config, array('ignore_request' => true));

		return parent::getModel($name, $prefix, $config);
	}
}
