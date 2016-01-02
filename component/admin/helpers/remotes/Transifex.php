<?php
/**
 * @package     Com_Localise
 * @subpackage  helper
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace FOORemotes;

if (false == class_exists('AbstractRemote'))
{
	require_once 'AbstractRemote.php';
}

use BabDev\Transifex\Transifex as TransifexAPI;

/**
 * Class Transifex
 *
 * @package  FOORemotes
 * @since    1.0
 * */
class Transifex extends AbstractRemote
{
	/**
	 * @var  TransifexAPI
	 */
	private $transifex;

	/**
	 * Constructor.
	 *
	 * @param   string  $project  The project name.
	 */
	public function __construct($project)
	{
		$this->project = $project;
	}

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
		parent::setCredentials($username, $password);

		// UGH..... where is composer??
		require_once '/home/elkuku/repos/com_localise/vendor/autoload.php';

		$options = array('api.username' => $username, 'api.password' => $password);

		$this->transifex = new TransifexAPI($options);

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
	public function getResources($project, $repository, $path = '', $filter = '')
	{
		$resources = array();

		$resourceObjects = $this->transifex->resources->getResources($project);

		foreach ($resourceObjects as $resource)
		{
			if ($filter && ! preg_match('/' . $filter . '/', $resource->name))
			{
				continue;
			}

			$r = new \stdClass;

			$r->name = $resource->name;

			$resources[] = $r;
		}

		return $resources;
	}

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
	public function getResource($project, $repository, $resource, $language)
	{
		// Normalize language tags for Transifex
		$language = str_replace('-', '_', $language);

		return $this->transifex->translations->getTranslation(
			$project,
			$resource->name,
			$language
		);
	}

	/**
	 * Get a standard file name.
	 *
	 * @param   string  $language   The language tag.
	 * @param   object  $resource   The resource object.
	 * @param   string  $extension  The file extension.
	 *
	 * @return string
	 */
	public function getFileName($language, $resource, $extension)
	{
		$resourceName = $resource->name;

		// Strip the scope prefix
		$resourceName = preg_replace('/^admin-|^site-/', '', $resourceName);

		// Special sys inis
		$resourceName = preg_replace('/_sys$/', '.sys', $resourceName);

		// Special lang.ini and install.ini
		if (in_array($resourceName, array('langini', 'install-ini')))
		{
			return $language . '.' . $extension;
		}

		return $language . '.' . $resourceName . '.' . $extension;
	}
}
