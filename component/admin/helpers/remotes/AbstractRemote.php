<?php
/**
 * @package     Com_Localise
 * @subpackage  helper
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace FOORemotes;

/**
 * Class AbstractRemote
 *
 * @package  FOORemotes
 * @since    1.0
 * */
abstract class AbstractRemote
{
	protected $project = '';

	protected $repository = '';

	protected $credentials = array();

	/**
	 * Set the credentials for the remote.
	 *
	 * @param   string  $username  The user name.
	 * @param   string  $password  The password.
	 *
	 * @return  $this
	 */
	public function setCredentials($username, $password)
	{
		$this->credentials = array(
			'user' => $username,
			'pass' => $password
		);

		return $this;
	}

	/**
	 * Get a list of resources.
	 *
	 * @param   string  $project     The project name
	 * @param   string  $repository  The repository name.
	 * @param   string  $path        The repository path.
	 * @param   string  $filter      The search filter.
	 *
	 * @return object[]
	 */
	abstract public function getResources($project, $repository, $path = '', $filter = '');

	/**
	 * Get a resource.
	 *
	 * @param   string  $project     The project name.
	 * @param   string  $repository  The repository name.
	 * @param   object  $resource    The resource object.
	 * @param   string  $language    The language tag.
	 *
	 * @return \stdClass
	 */
	abstract public function getResource($project, $repository, $resource, $language);

	/**
	 * Get a standard file name.
	 *
	 * @param   string  $language   The language tag.
	 * @param   object  $resource   The resource object.
	 * @param   string  $extension  The file extension.
	 *
	 * @return string
	 */
	abstract public function getFileName($language, $resource, $extension);
}
