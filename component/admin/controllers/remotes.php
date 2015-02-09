<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Packages Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseControllerRemotes extends JControllerAdmin
{
	/**
	 * Get a remote.
	 *
	 * @throws Exception
	 */
	public function getRemote()
	{
		$path = JFactory::getConfig()->get('log_path') . '/com_localise.remotes_log.php';

		if (file_exists($path))
		{
			jimport('joomla.filesystem.file');
			JFile::delete($path);
		}

		JLog::addLogger(
			array(
				'text_file' => 'com_localise.remotes_log.php',
				'text_entry_format' => '{MESSAGE}'
			),
			JLog::ALL,
			array('com_localise')
		);

		$application = JFactory::getApplication();
		$input = $application->input;

		$cids = $input->get('cid', array(), 'array');

		JArrayHelper::toInteger($cids);

		$model = $this->getModel('remotes');

		$remotes = $model->getItems();

		$params = JComponentHelper::getParams('com_localise');

		foreach ($remotes as $remote)
		{
			if (in_array($remote->id, $cids))
			{
				// Kinda autoloader...
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/helpers/remotes/' . $remote->type . '.php';

				if (false == file_exists($path))
				{
					$msg = sprintf('Remote type "%1$s" not found in path "%2$s"', $remote->type, str_replace(JPATH_ROOT, 'JROOT', $path));
					JLog::add($msg, JLog::ERROR, 'com_localise');
					echo $msg;
					continue;
					throw new UnexpectedValueException($msg);
				}

				require_once $path;

				// Kinda Joomla! CMS (R)...
				jimport('joomla.filesystem.file');

				// Kinda name space...
				$className = '\\FOORemotes\\' . $remote->type;

				/* @type \FOORemotes\AbstractRemote $remoteClass */
				$remoteClass = new $className($remote->user, $remote->project);

				if ('Transifex' == $remote->type)
				{
					$remoteClass->setCredentials($params->get('transifex_user'), $params->get('transifex_password'));
				}

				if ('GitHub' == $remote->type)
				{
					$remoteClass->setCredentials($params->get('github_user'), $params->get('github_password'));
				}

				JLog::add(
					'Fetching remote ' . $remote->type . ' - ' . $remote->user . '/' . $remote->project,
					JLog::INFO, 'com_localise'
				);

				$resources = $remoteClass->getResources($remote->user, $remote->project, $remote->path, $remote->filter);

				JLog::add('Fetched ' . count($resources) . ' resources', JLog::INFO, 'com_localise');

				foreach ($resources as $i => $resource)
				{
					JLog::add(
						sprintf(
							'Fetching resource "%s" (%d/%d)',
							$resource->name,
							$i,
							count($resources)
						),
						JLog::INFO, 'com_localise'

					);

					$fileObject = $remoteClass->getResource($remote->user, $remote->project, $resource, $remote->language);

					$path = $model->getRemotePath($remote);

					$fileName = $remoteClass->getFileName($remote->language, $resource, 'ini');

					if (false == JFile::write($path . '/' . $fileName, $fileObject->content))
					{
						throw new \DomainException(sprintf('Failed to write file %s', $path));
					}

					JLog::add('Saved to ' . $fileName, JLog::INFO, 'com_localise');
				}
			}
		}

		$msg = 'Remotes successfully fetched';

		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=remotes', false), $msg);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Remotes', $prefix = 'LocaliseModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Display View
	 *
	 * @param   boolean  $cachable   Enable cache or not.
	 * @param   array    $urlparams  todo: this params can probably be removed.
	 *
	 * @return  void     Display View
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JFactory::getApplication()->input->set('view', 'remotes');

		parent::display($cachable);
	}

	/**
	 * Get a log file contents.
	 */
	public function getLog()
	{
		$path = JFactory::getConfig()->get('log_path') . '/com_localise.remotes_log.php';

		echo file_exists($path)
			? nl2br(file_get_contents($path))
			: 'Log file not found !';
	}
}
