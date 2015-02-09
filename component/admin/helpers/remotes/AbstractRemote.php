<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 30.01.15
 * Time: 07:23
 */

namespace FOORemotes;

abstract class AbstractRemote
{
	protected $project = '';

	protected $repository = '';

	protected $credentials = array();

	/**
	 * @param $user
	 * @param $password
	 *
	 * @return $this
	 */
	public function setCredentials($user, $password)
	{
		$this->credentials = array(
			'user' => $user,
			'pass' => $password
		);

		return $this;
	}

	/**
	 * @param        $project
	 * @param        $repository
	 * @param        $path
	 * @param string $filter
	 *
	 * @return mixed
	 */
	abstract public function getResources($project, $repository, $path = '', $filter = '');

	/**
	 * @param $project
	 * @param $repository
	 * @param $resourceName
	 * @param $language
	 *
	 * @return mixed
	 */
	abstract public function getResource($project, $repository, $resourceName, $language);

	/**
	 * @param $language
	 * @param $resourceName
	 * @param $extension
	 *
	 * @return mixed
	 */
	abstract public function getFileName($language, $resourceName, $extension);
}
