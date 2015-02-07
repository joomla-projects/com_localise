<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use BabDev\Transifex\Transifex;

defined('_JEXEC') or die;

/**
 * Languages Model class for the Localise component
 *
 * @since  1.0
 */
class LocaliseModelRemotes extends JModelList
{
	protected $filter_fields = array('tag', 'client', 'name');

	protected $context = 'com_localise.languages';

	protected $items = [];

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = JFactory::getApplication();
		$data = $app->input->get('filters', array(), 'array');

		if (empty($data))
		{
			$data           = array();
			$data['select'] = $app->getUserState('com_localise.select');
			$data['search'] = $app->getUserState('com_localise.remotes.search');
		}
		else
		{
			$app->setUserState('com_localise.select', isset($data['select']) ? $data['select'] : '');
			$app->setUserState('com_localise.remotes.search', $data['search']);
		}

		$this->setState('filter.search', isset($data['search']['expr']) ? $data['search']['expr'] : '');

		$this->setState('filter.client', isset($data['select']['client']) ? $data['select']['client'] : '');

		$this->setState('filter.tag', isset($data['select']['tag']) ? $data['select']['tag'] : '');

		$this->setState('filter.name', isset($data['select']['name']) ? $data['select']['name'] : '');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_localise');
		$this->setState('params', $params);

		// Call auto-populate parent method
		parent::populateState('tag', 'asc');
	}

	/**
	 * Method to get the row form.
	 *
	 * @return  mixed  JForm object on success, false on failure.
	 */
	public function getForm()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

		$form = JForm::getInstance(
			'com_localise.remotes', 'remotes',
			array('control' => 'filters', 'event' => 'onPrepareForm')
		);

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.select', array());

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind(array('select' => $data));
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.remotes.search', array());

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind(array('search' => $data));
		}

		return $form;
	}

	/**
	 * Get the items (according the filters and the pagination)
	 *
	 * @return  array  array of object items
	 */
	public function getItems()
	{
		if (!$this->items)
		{
			$this->items = [];

			$item = new stdClass;

			$item->id = 6;
			$item->type = 'Transifex';

			$item->user     = 'j3corelang';
			$item->project  = 'joomla';
			$item->scope    = 'administrator';
			$item->language = 'pt-BR';

			$item->filter = 'admin-*';
			$item->path   = '';

			$this->items[] = $item;

			$item = new stdClass;

			$item->id = 7;
			$item->type = 'Transifex';

			$item->user     = 'j3corelang';
			$item->project  = 'joomla';
			$item->scope    = 'site';
			$item->language = 'pt-BR';

			$item->filter = 'site-*';
			$item->path   = '';

			$this->items[] = $item;

			$item = new stdClass;

			$item->id = 8;
			$item->type = 'Transifex';

			$item->user     = 'j3corelang';
			$item->project  = 'joomla';
			$item->scope    = 'installation';
			$item->language = 'pt-BR';

			$item->filter = '^install-*';
			$item->path   = '';

			$this->items[] = $item;

			$item = new stdClass;

			$item->id = 9;
			$item->type = 'GitHub';

			$item->user     = 'joomlagerman';
			$item->project  = 'joomla';
			$item->scope    = 'administrator';
			$item->language = 'de-DE';
			$item->filter   = '';
			$item->path   = 'administrator/language/de-DE/';

			$this->items[] = $item;

			$item = new stdClass;

			$item->id = 10;
			$item->type = 'GitHub';

			$item->user     = 'joomlagerman';
			$item->project  = 'joomla';
			$item->scope    = 'site';
			$item->language = 'de-DE';
			$item->filter   = '';
			$item->path   = 'language/de-DE/';

			$this->items[] = $item;

			$item = new stdClass;

			$item->id = 11;
			$item->type = 'GitHub';

			$item->user     = 'joomlagerman';
			$item->project  = 'joomla';
			$item->scope    = 'installation';
			$item->language = 'de-DE';
			$item->filter   = '';
			$item->path   = 'installation/language/de-DE/';

			$this->items[] = $item;
		}
			return $this->items;

		/////

			// Call out to Transifex
//			$translation = $transifex->translations->getTranslation(
//				$this->getApplication()->get('transifex.project'),
//				strtolower(str_replace('.', '-', $extension)) . '-' . strtolower($domain),
//				str_replace('-', '_', $language)
//			);

			$languages = $this->getLanguages();
			$count     = count($languages);
			$start     = $this->getState('list.start');
			$limit     = $this->getState('list.limit');

			if ($start > $count)
			{
				$start = 0;
			}

			if ($limit == 0)
			{
				$start = 0;
				$limit = null;
			}

			$this->items = array_slice($languages, $start, $limit);


		return $this->items;
	}

	/**
	 * Get total number of items (according to filters)
	 *
	 * @return  int  number of languages
	 */
	public function getTotal()
	{
		return count($this->getItems());
	}

	public function getRemotePath($remote)
	{
		return implode('/',
			[
				JPATH_COMPONENT_ADMINISTRATOR,
				'build',
				'remotes',
				$remote->type,
				$remote->user,
				$remote->project,
				$remote->scope,
				$remote->language
			]
		);
	}
}
