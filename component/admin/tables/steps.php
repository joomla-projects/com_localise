<?php
/**
 * @package     Com_Localise
 * @subpackage  tables
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Localise Steps Table class for the Localise Component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseTableSteps extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__localise_steps', 'id', $db);
	}

	/**
	 * Cleanup table
	 */
	function cleanup()
	{
		// Cleanup the table
		$query = $this->_db->getQuery(true);
		$query->delete()->from('#__localise_steps');

		try {
			$this->_db->setQuery($query)->execute();
		} catch (RuntimeException $e) {
			throw new RuntimeException($e->getMessage());
		}
	}
}
