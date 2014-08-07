<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * The Com_Localise ajax controller
 *
 * @package     Com_Localise
 * @subpackage  com_localise
 * @since       1.0
 */
class LocaliseControllerAjax extends JControllerLegacy
{
	/**
	 * @var		string	The context for persistent state.
	 * @since   1.0
	 */
	protected $context = 'com_localise.ajax';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the model class name.
	 *
	 * @return  Com_LocaliseModel
	 * @since   1.0
	 */
	public function getModel($name = '', $prefix = 'LocaliseModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Run the Com_Localise checks
	 */
	public function checks()
	{
		// Get the model for the view.
		$model = $this->getModel('Load');

		// Running the checks
		try {
			$model->checks();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run Com_Localise step
	 */
	public function step()
	{
		// Get the model for the view.
		$model = $this->getModel('Load');

		// Running the step
		try {
			$model->step();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run Com_Localise migrate
	 */
	public function process()
	{
		// Get the model for the view.
		$model = $this->getModel('Load');

		// Running the migrate
		try {
			$model->process();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}
}
