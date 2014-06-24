<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Localise step class
 *
 * @package		Localise
 */
class LocaliseStep
{	
	public $id = null;
	public $client = null;
	public $name = null;
	public $cid = 0;
	public $cache = 0;
	public $status = 0;
	public $total = 0;
	public $start = 0;
	public $stop = 0;
	public $items = '';
	public $laststep = 0;
	public $chunk = 0;
	public $first = false;
	public $next = false;
	public $middle = false;
	public $end = false;
	
	/**
	 * @var      
	 * @since  1.0
	 */
	protected $_db = null;

	/**
	 * @var      
	 * @since  1.0
	 */
	protected $_table = '#__localise_steps';

	function __construct($name = null, $client = null)
	{
		jimport('legacy.component.helper');
		JLoader::import('helpers.localise', JPATH_COMPONENT_ADMINISTRATOR);

		// Creating dabatase instance for this installation
		$this->_db = JFactory::getDBO();

		// Load the last step from database
		if ($name !== false)
		{
			$this->_load($name, $client);
		}
	}

	/**
	 *
	 * @param   stdClass   $options  Parameters to be passed to the database driver.
	 *
	 * @return  Localise  A Localise object.
	 *
	 * @since  1.0
	 */
	static function getInstance($name = null)
	{
		// Create our new Localise connector based on the options given.
		try
		{
			$instance = new LocaliseStep($name);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to load LocaliseStep object: %s', $e->getMessage()));
		}

		return $instance;
	}

	/**
	 * Method to set the parameters. 
	 *
	 * @param   array  $parameters  The parameters to set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setParameters($data)
	{
		// Ensure that only valid OAuth parameters are set if they exist.
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (property_exists ( $this , $k ))
				{
					// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
					$this->$k = urldecode((string) $v);
				}
			}
		}
	}

	/**
	 * Method to get the parameters. 
	 *
	 * @return  array  $parameters  The parameters of this object.
	 *
	 * @since   1.0
	 */
	public function getParameters()
	{
		$return = array();

		foreach ($this as $k => $v)
		{
			if (property_exists ( $this , $k ))
			{
				if (!is_object($v) && !is_array($v)) {
					if ($v != "" || $k == 'total' || $k == 'start' || $k == 'stop' || $k == 'next' || $k == 'laststep') {
						// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
						$return[$k] = urldecode((string) $v);
					}
				}	else if (is_array($v)) {
					$return[$k] = json_encode($v);
				}
			}
		}

		return json_encode($return);
	}

	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep($name = false, $json = true) {

		// Check if step is loaded
		if (empty($name) && empty($this->name)) {
			return false;
		}

		JLoader::import('helpers.localise', JPATH_COMPONENT_ADMINISTRATOR);
		$params	= JComponentHelper::getParams('com_localise');

		$limit = $this->chunk = $params->get('chunk');

		// We must to fragment the steps
		if ($this->total > $limit) {

			if ($this->cache == 0 && $this->status == 0) {

				if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
					$this->cache = round( ($this->total-1) / $limit, 0, PHP_ROUND_HALF_DOWN);
				}else{
					$this->cache = round( ($this->total-1) / $limit);
				}
				$this->start = 0;
				$this->stop = $limit - 1;
				$this->first = true;
				$this->debug = "{{{1}}}";

			} else if ($this->cache == 1 && $this->status == 1) {

				$this->start = $this->cid;
				$this->cache = 0;
				$this->stop = $this->total - 1;
				$this->debug = "{{{2}}}";
				$this->first = false;

			} else if ($this->cache > 0) { 

				$this->start = $this->cid;
				$this->stop = ($this->start - 1) + $limit;
				$this->cache = $this->cache - 1;
				$this->debug = "{{{3}}}";
				$this->first = false;

				if ($this->stop > $this->total) {
					$this->stop = $this->total - 1;
					$this->next = true;
				}else{
					$this->middle = true;
				}
			}

			// Status == 1
			$this->status = 1;

		}else if ($this->total == 0) {

			$this->stop = -1;
			$this->next = 1;
			$this->first = true;
			if ($this->name == $this->laststep) {
				$this->end = true;
			}
			$this->cache = 0;
			$this->status = 2;
			$this->debug = "{{{4}}}";

		}else{

			$this->start = 0;
			$this->first = 1;
			$this->cache = 0;
			$this->status = 1;
			$this->stop = $this->total - 1;
			$this->debug = "{{{5}}}";
		}

		// Mark if is the end of the step
		if ($this->name == $this->laststep && $this->cache == 1) {
			$this->end = true;
		}

		// updating the status flag
		$this->_updateStep();

		return $this->getParameters();
	}

	/**
	 * Getting the current step from database and put it into object properties
	 *
	 * @return   step object
	 */
	public function _load($name = null, $client = null) {

		// Get the data
		$query = $this->_db->getQuery(true);
		$query->select('e.*');
		$query->from($this->_table.' AS e');

		if (!empty($name) && !empty($client)) {
			$query->where("e.name = '{$name}'");
			$query->where("e.client = '{$client}'");
		}else{
			$query->where("e.status != 2");
		}

		$query->order('e.id ASC');
		$query->limit(1);

		$this->_db->setQuery($query);
		$step = $this->_db->loadAssoc();

		// Check for query error.
		$error = $this->_db->getErrorMsg();
		if ($error) {
			return false;
		}

		// Check if step is an array
		if (!is_array($step)) {
			return false;
		}

		// Reset the $query object
		$query->clear();

		// Select last step
		$query->select('id');
		$query->from($this->_table);
		$query->where("status = 0", "OR");
		$query->where("status = 1");
		$query->order('id DESC');
		$query->limit(1);

		$this->_db->setQuery($query);
		$step['laststep'] = $this->_db->loadResult();

		// Set the parameters
		$this->setParameters($step);

		return true;
	}

	/**
	 * updateStep
	 *
	 * @return	none
	 * @since	2.5.2
	 */
	public function _updateStep() {

		$query = $this->_db->getQuery(true);
		$query->update($this->_table);

		$columns = array('status', 'cache', 'total', 'start', 'stop');

		foreach ($columns as $column) {
			if (!empty($this->$column)) {
				$query->set("{$column} = '{$this->$column}'");
			}
		}

		$query->where("name = {$this->_db->quote($this->name)}");
		$query->where("client = {$this->_db->quote($this->client)}");

		// Execute the query
		$this->_db->setQuery($query)->execute();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		return true;
	}

	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _updateID($id)
	{
		$query = $this->_db->getQuery(true);
		$query->update($this->_table);
		$query->set("`cid` = '{$id}'");
		$query->where("client = {$this->_db->quote($this->client)}");
		$query->where("name = {$this->_db->quote($this->name)}");

		// Execute the query
		return $this->_db->setQuery($query)->execute();
	}

	/**
	 * Update the cid row
	 *
	 * @return  string  A • if is CLI or nothing if is Web
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _nextID($total = false)
	{
		$total = ($total != false) ? $total : 1;

		$update_cid = (int) $this->_getStepID() + $total;
		$this->_updateID($update_cid);
	}

	/**
	 * Get the step id
	 *
	 * @return  int  The step id
	 *
	 * @since   1.0
	 */
	public function _getStepID()
	{
		$this->_load($this->name);
		return $this->cid;
	}
}
