<?php
/**
 * @package     Com_Localise
 * @subpackage  tables
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Localise Table class for the Localise Component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseTableRemote extends JTable
{
	/**
	 * @var int
	 */
	public $id = null;

	/**
	 * @var string
	 */
	public $type = '';

	/**
	 * @var string
	 */
	public $user = '';

	/**
	 * @var string
	 */
	public $project = '';

	/**
	 * @var string
	 */
	public $scope = '';

	/**
	 * @var string
	 */
	public $language = '';

	/**
	 * @var string
	 */
	public $filter = '';

	/**
	 * @var string
	 */
	public $path = '';

	/**
	 * Constructor
	 *
	 * @param   object  $db  Database connector object.
	 */
	public function __construct($db)
	{
		parent::__construct('#__localise_remotes', 'id', $db);
	}
}
