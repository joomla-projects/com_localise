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

/**
 * Class GitHub
 *
 * @package FOORemotes
 */
class GitHub extends AbstractRemote
{
	/**
	 * @var  \JGithub
	 */
	private $gitHub;

	/**
	 * Constructor.
	 *
	 * @param   string  $project  The project name.
	 */
	public function __construct($project, $repository)
	{
		$this->project = $project;
		$this->repository = $repository;
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

		$options = new \JRegistry;

		$options->set('api.username', $username);
		$options->set('api.password', $password);

		$this->gitHub = new \JGithub($options);
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

		$sha = 'master';

		$resourcesObject = $this->gitHub->data->trees->getRecursively($project, $repository, $sha);

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
	 * @throws  \DomainException
	 *
	 * @return \stdClass
	 */
	public function getResource($project, $repository, $resource, $language)
	{
		$r = $this->gitHub->repositories->contents->get(
			$project,
			$repository,
			$resource->path . '/' . $resource->name
		);

		if (!isset($r->content))
		{
			throw new \DomainException('Can not fetch ' . $resource->name . ' - old Joomla!&reg; GitHubPackage??');
		}

		$resource = new \stdClass;

		$resource->content = base64_decode($r->content);

		return $resource;
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
		// Nothing special required here (so far...).
		return $resource->name;
	}
}
