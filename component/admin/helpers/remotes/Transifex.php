<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 30.01.15
 * Time: 07:22
 */

namespace FOORemotes;

if(false == class_exists('AbstractRemote'))
{
	require_once 'AbstractRemote.php';
}

use BabDev\Transifex\Transifex as TransifexAPI;

class Transifex extends AbstractRemote
{
	/**
	 * @var  TransifexAPI
	 */
	private $transifex;

	public function __construct($project)
	{
		$this->project = $project;
	}

	public function setCredentials($username, $password)
	{
		parent::setCredentials($username, $password);

		// UGH..... where is composer??
		require_once '/home/elkuku/repos/com_localise/vendor/autoload.php';

		$options = array('api.username' => $username, 'api.password' => $password);

		$this->transifex = new TransifexAPI($options);
	}

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
