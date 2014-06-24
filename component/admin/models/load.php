<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JLoader::register('LocaliseStep', JPATH_COMPONENT_ADMINISTRATOR.'/includes/step.php');

/**
 * Load Model class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseModelLoad extends JModelList
{
	protected $context = 'com_localise.translations';

	/**
	 * Check the steps and his totals
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function checks()
	{
		$count = array();

		$clients = array('site', 'administrator');

		foreach ($clients as $client)
		{
			$scans = LocaliseHelper::getScans($client);

			// For all selected clients
			$cons_path = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

			if (JFolder::exists($cons_path))
			{
				$tags = JFolder::folders($cons_path, '.', false, false, array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

				foreach ($tags as $tag)
				{
					if (!isset($count[$client][$tag])) {
						$count[$client][$tag] = 0;
					}

					// For all selected tags
					$files = JFolder::files("$cons_path/$tag", ".*\.ini$");

					foreach ($files as $file)
					{
						$count[$client][$tag] = $count[$client][$tag] + 1;
					}
				}
			}

		} // end foreach

		// Cleanup the table
		$table = JTable::getInstance('Steps', 'LocaliseTable');
		$table->cleanup();

		// Save the languages, client and total to #__localise_steps table
		foreach ($count as $client_key => $client_value)
		{
			foreach ($client_value as $lang_key => $lang_value)
			{
				$data = array();
				$data['client'] = $client_key;
				$data['name'] = $lang_key;
				$data['total'] = $lang_value;

				$table = JTable::getInstance('Steps', 'LocaliseTable');
				$table->bind($data);
				$table->store();
			}
		}

		// Return
		$this->returnMsg(500, 'DONE');
	}

	/**
	 * Check the steps and his totals
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function step()
	{
		$step = LocaliseStep::getInstance();

		$s = $step->getStep();

		echo $s;
		exit;
	}

	/**
	 * Check the steps and his totals
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function process()
	{
		$params	= JComponentHelper::getParams('com_localise');
		$chunk = $params->get('chunk');

		$step = LocaliseStep::getInstance();
		$scans = LocaliseHelper::getScans($step->client);

		// For all selected clients
		$cons_path = constant('LOCALISEPATH_' . strtoupper($step->client)) . '/language';

		if (JFolder::exists($cons_path))
		{
			$tags = JFolder::folders($cons_path, $step->name, false, false, array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

			// Get the files list and cut the array with the chunk data
			$step->items = array_slice(JFolder::files("{$cons_path}/{$step->name}", ".*\.ini$"), $step->start, $chunk);

			$step->_nextID(count($step->items));
		}

		// Load the changes
		$step->_load($step->name, $step->client);

		// Javascript flags
		if ( $step->cid == $step->stop+1 && $step->total != 0) {
			$step->next = true;
		}

		$empty = false;
		if ($step->cid == 0 && $step->total == 0 && $step->start == 0 && $step->stop == 0) {
			$empty = true;
		} 

		if ($step->stop == 0) {
			$step->stop = -1;
		}

		// Update #__localise_steps table if id = last_id
		if ( ($step->total != 0) && ($empty == false) && ( ($step->total <= $step->cid) || ($step->stop == -1) ) )
		{
			if ($step->id == $step->laststep) {
				$step->end = true;
			}else{
				$step->next = true;
			}

			$step->status = 2;
			$step->_updateStep();
		}

		sleep(1);
		echo $step->getParameters();

		exit;
	}

	/**
	 * returnMsg
	 *
	 * @return	none
	 * @since	1.0
	 */
	public function returnMsg ($number, $text)
	{
		$message['number'] = $number;
		$message['text'] = JText::_($text);
		print(json_encode($message));
		exit;
	}
}
