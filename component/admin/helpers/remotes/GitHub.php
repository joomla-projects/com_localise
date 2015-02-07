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

class GitHub extends AbstractRemote
{
	/**
	 * @var  \JGithub
	 */
	private $gitHub;

	public function __construct($project, $repository)
	{
		$this->project = $project;
		$this->repository = $repository;
	}

	public function setCredentials($username, $password)
	{
		parent::setCredentials($username, $password);

		// UGH.....
		//require_once '/home/elkuku/repos/com_localise/vendor/autoload.php';


		$options = new \JRegistry;

		$options->set('api.username', $username);
		$options->set('api.password', $password);

		$this->gitHub = new \JGithub($options);
	}

	public function getResources($project, $repository, $path = '', $filter = '')
	{
		$resources = [];

		$sha = 'master';

		$resourcesObject = $this->gitHub->data->trees->getRecursively($project, $repository, $sha);

		//var_dump($resourcesObject);

		foreach ($resourcesObject->tree as $i => $resource)
		{
			if ($path)
			{
				if (0 !== strpos($resource->path, $path))
				{
					continue;
				}

				$fileName = str_replace($path, '', $resource->path);
			}
			else
			{
				$fileName = $resource->path;
			}

			if ($filter)
			{
				if (!preg_match('/' . $filter . '/', $resource->name))
				{
					continue;
				}

			}

			$r = new \stdClass;

			$r->name = $fileName;
			$r->path = trim($path, '/');

			$resources[] = $r;
		}
var_dump($resources);
		return $resources;
	}

	public function getResource($project, $repository, $resource, $language)
	{
		$r = $this->gitHub->repositories->contents->get(
			$project,
			$repository,
			$resource->path . '/' . $resource->name
		);

		if (!isset($r->content))
		{
			throw new DomainException('Can not fetch ' . $resource->name . ' - old Joomla!&reg; GitHubPackage??');
		}

		$resource = new \stdClass;

		$resource->content = base64_decode($r->content);

		return $resource;
	}

	public function getFileName($language, $resource, $extension)
	{
		// Nothing special required here (so far...).
		return $resource->name;
	}
}
