<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.stream');
jimport('joomla.client.helper');
jimport('joomla.access.rules');

/**
 * Translation Model class for the Localise component
 *
 * @since  1.0
 */
class LocaliseModelTranslation extends JModelAdmin
{
	protected $item;

	protected $contents;

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
		$input = JFactory::getApplication()->input;

		// Get the infos
		$client   = $input->getCmd('client', '');
		$tag      = $input->getCmd('tag', '');
		$filename = $input->getCmd('filename', '');
		$storage  = $input->getCmd('storage', '');

		$this->setState('translation.client', !empty($client) ? $client : 'site');
		$this->setState('translation.tag', $tag);
		$this->setState('translation.filename', $filename);
		$this->setState('translation.storage', $storage);

		// Get the id
		$id = $input->getInt('id', '0');
		$this->setState('translation.id', $id);

		// Get the layout
		$layout = $input->getCmd('layout', 'edit');
		$this->setState('translation.layout', $layout);

		// Get the parameters
		$params = JComponentHelper::getParams('com_localise');

		// Get the reference tag
		$ref = $params->get('reference', 'en-GB');
		$this->setState('translation.reference', $ref);

		// Get the paths
		$path = LocaliseHelper::getTranslationPath($client, $tag, $filename, $storage);

		if ($filename == 'lib_joomla')
		{
			$refpath = LocaliseHelper::findTranslationPath('administrator', $ref, $filename);

			if (!JFile::exists($path))
			{
				$path2 = LocaliseHelper::getTranslationPath($client == 'administrator' ? 'site' : 'administrator', $tag, $filename, $storage);

				if (JFile::exists($path2))
				{
					$path = $path2;
				}
			}
		}
		else
		{
			$refpath = LocaliseHelper::findTranslationPath($client, $ref, $filename);
		}

		$this->setState('translation.path', $path);
		$this->setState('translation.refpath', $refpath);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 */
	public function getTable($type = 'Localise', $prefix = 'LocaliseTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get contents
	 *
	 * @return string
	 */
	public function getContents()
	{
		if (!isset($this->contents))
		{
			$path = $this->getState('translation.path');

			if (JFile::exists($path))
			{
				$this->contents = file_get_contents($path);
			}
			else
			{
				$this->contents = '';
			}
		}

		return $this->contents;
	}

	/**
	 * Get a translation
	 *
	 * @param   integer  $pk  The id of the primary key (Note unused by the function).
	 *
	 * @return  JObject|null  Object on success, null on failure.
	 */
	public function getItem($pk = null)
	{
		if (!isset($this->item))
		{
			$conf    = JFactory::getConfig();
			$caching = $conf->get('caching') >= 1;

			if ($caching)
			{
				$keycache   = $this->getState('translation.client') . '.' . $this->getState('translation.tag') . '.' .
					$this->getState('translation.filename') . '.' . 'translation';
				$cache      = JFactory::getCache('com_localise', '');
				$this->item = $cache->get($keycache);

				if ($this->item && $this->item->reference != $this->getState('translation.reference'))
				{
					$this->item = null;
				}
			}
			else
			{
				$this->item = null;
			}

			if (!$this->item)
			{
				$path                        = JFile::exists($this->getState('translation.path'))
							       ? $this->getState('translation.path')
							       : $this->getState('translation.refpath');

				// Get Special keys cases
				$params                       = JComponentHelper::getParams('com_localise');
				$tag                          = $this->getState('translation.tag');
				$target_tag                   = preg_quote($tag, '-');
				$special_keys_types           = array ('untranslatablestrings', 'blockedstrings', 'keystokeep');
				$regex_syntax                 = '/\[' . $target_tag . '\](.*?)\[\/' . $target_tag . '\]/s';
				$regex_lines                  = '/\r\n|\r|\n/';
				$global_special_keys          = array();
				$special_keys                 = array();

				foreach ($special_keys_types as $special_keys_case)
				{
					$global_special_keys[$special_keys_case] = $params->get($special_keys_case, '');

					if (preg_match($regex_syntax, $global_special_keys[$special_keys_case]))
					{
						preg_match_all($regex_syntax, $global_special_keys[$special_keys_case], $preg_result, PREG_SET_ORDER);

						$special_keys[$special_keys_case] = preg_split($regex_lines, $preg_result[0][1]);
					}
					else
					{
						$special_keys[$special_keys_case] = array();
					}

					$this->setState('translation.' . $special_keys_case, (array) $special_keys[$special_keys_case]);
				}

				$untranslatablestrings = $special_keys['untranslatablestrings'];
				$blockedstrings        = $special_keys['blockedstrings'];
				$keystokeep            = $special_keys['keystokeep'];

				$this->item = new JObject(
									array
										(
										'reference'             => $this->getState('translation.reference'),
										'bom'                   => 'UTF-8',
										'svn'                   => '',
										'version'               => '',
										'description'           => '',
										'creationdate'          => '',
										'author'                => '',
										'maincopyright'         => '',
										'additionalcopyright'   => array(),
										'license'               => '',
										'exists'                => JFile::exists($this->getState('translation.path')),
										'translated'            => 0,
										'untranslatable'        => 0,
										'blocked'               => 0,
										'unchanged'             => 0,
										'extra'                 => 0,
										'keytodelete'           => 0,
										'total'                 => 0,
										'complete'              => false,
										'source'                => '',
										'untranslatablestrings' => (array) $untranslatablestrings,
										'blockedstrings'        => (array) $blockedstrings,
										'keystokeep'            => (array) $keystokeep,
										'error'                 => array()
										)
				);

				if (JFile::exists($path))
				{
					$this->item->source = file_get_contents($path);
					$stream             = new JStream;
					$stream->open($path);
					$begin = $stream->read(4);
					$bom   = strtolower(bin2hex($begin));

					if ($bom == '0000feff')
					{
						$this->item->bom = 'UTF-32 BE';
					}
					else
					{
						if ($bom == 'feff0000')
						{
							$this->item->bom = 'UTF-32 LE';
						}
						else
						{
							if (substr($bom, 0, 4) == 'feff')
							{
								$this->item->bom = 'UTF-16 BE';
							}
							else
							{
								if (substr($bom, 0, 4) == 'fffe')
								{
									$this->item->bom = 'UTF-16 LE';
								}
							}
						}
					}

					$stream->seek(0);
					$continue           = true;
					$lineNumber         = 0;
					$params             = JComponentHelper::getParams('com_localise');
					$isTranslationsView = JFactory::getApplication()->input->get('view') == 'translations';

					while (!$stream->eof())
					{
						$line = $stream->gets();
						$lineNumber++;

						if ($line[0] == '#')
						{
							$this->item->error[] = $lineNumber;
						}
						elseif ($line[0] == ';')
						{
							if (preg_match('/^(;).*(\$Id.*\$)/', $line, $matches))
							{
								$this->item->svn = $matches[2];
							}
							elseif (preg_match('/(;)\s*@?(\pL+):?.*/', $line, $matches))
							{
								switch (strtolower($matches[2]))
								{
									case 'note':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->complete = $this->item->complete || strtolower($matches2[3]) == 'complete';
										break;
									case 'version':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->version = $matches2[3];
										break;
									case 'desc':
									case 'description':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->description = $matches2[3];
										break;
									case 'date':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->creationdate = $matches2[3];
										break;
									case 'author':
										if ($params->get('author') && !$isTranslationsView)
										{
											$this->item->author = $params->get('author');
										}
										else
										{
											preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
											$this->item->author = $matches2[3];
										}
										break;
									case 'copyright':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);

										if (empty($this->item->maincopyright))
										{
											if ($params->get('copyright') && !$isTranslationsView)
											{
												$this->item->maincopyright = $params->get('copyright');
											}
											else
											{
												$this->item->maincopyright = $matches2[3];
											}
										}

										if (empty($this->item->additionalcopyright))
										{
											if ($params->get('additionalcopyright') && !$isTranslationsView)
											{
												$this->item->additionalcopyright[] = $params->get('additionalcopyright');
											}
											else
											{
												$this->item->additionalcopyright[] = $matches2[3];
											}
										}
										break;
									case 'license':
										if ($params->get('license') && !$isTranslationsView)
										{
											$this->item->license = $params->get('license');
										}
										else
										{
											preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
											$this->item->license = $matches2[3];
										}
										break;
									case 'package':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->package = $matches2[3];
										break;
									case 'subpackage':
										preg_match('/(;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->subpackage = $matches2[3];
										break;
									case 'link':
										break;
									default:
										if (empty($this->item->author))
										{
											if ($params->get('author') && !$isTranslationsView)
											{
												$this->item->author = $params->get('author');
											}
											else
											{
												preg_match('/(;)\s*(.*)/', $line, $matches2);
												$this->item->author = $matches2[2];
											}
										}
										break;
								}
							}
						}
						else
						{
							break;
						}
					}

					if (empty($this->item->author) && $params->get('author') && !$isTranslationsView)
					{
						$this->item->author = $params->get('author');
					}

					if (empty($this->item->license) && $params->get('license') && !$isTranslationsView)
					{
						$this->item->license = $params->get('license');
					}

					if (empty($this->item->maincopyright) && $params->get('copyright') && !$isTranslationsView)
					{
						$this->item->maincopyright = $params->get('copyright');
					}

					if (empty($this->item->additionalcopyright) && $params->get('additionalcopyright') && !$isTranslationsView)
					{
						$this->item->additionalcopyright[] = $params->get('additionalcopyright');
					}

					while (!$stream->eof())
					{
						$line = $stream->gets();
						$lineNumber++;

						if (!preg_match('/^(|(\[[^\]]*\])|([A-Z][A-Z0-9_\-\.]*\s*=(\s*(("[^"]*")|(_QQ_)))+))\s*(;.*)?$/', $line))
						{
							$this->item->error[] = $lineNumber;
						}
					}

					$stream->close();
				}

				$this->item->additionalcopyright = implode("\n", $this->item->additionalcopyright);

				if ($this->getState('translation.layout') != 'raw' && empty($this->item->error))
				{
					$sections    = LocaliseHelper::parseSections($this->getState('translation.path'));
					$refsections = LocaliseHelper::parseSections($this->getState('translation.refpath'));

					if (!empty($refsections['keys']))
					{
						foreach ($refsections['keys'] as $key => $string)
						{
							$this->item->total++;
							$full_line = htmlspecialchars_decode($key . '="' . $string . '"');

							if (!empty($sections['keys']) && array_key_exists($key, $sections['keys']))
							{
								if (in_array($full_line, $blockedstrings))
								{
									$this->item->translated++;
									$this->item->blocked++;
								}
								elseif (in_array($full_line, $untranslatablestrings))
								{
									$this->item->translated++;
									$this->item->untranslatable++;
								}
								elseif ($sections['keys'][$key] != $string || $this->getState('translation.path') == $this->getState('translation.refpath'))
								{
									$this->item->translated++;
								}
								else
								{
									$this->item->unchanged++;
								}
							}
						}
					}

					if (!empty($sections['keys']))
					{
						foreach ($sections['keys'] as $key => $string)
						{
							$full_line = htmlspecialchars_decode($key . '="' . $string . '"');

							if (empty($refsections['keys']) || !array_key_exists($key, $refsections['keys']))
							{
								if (in_array($full_line, $blockedstrings))
								{
									$this->item->blocked++;
								}

								if (in_array($key, $keystokeep))
								{
									$this->item->extra++;
								}
								else
								{
									$this->item->keytodelete++;
								}
							}
						}
					}

					$this->item->completed = $this->item->total
						? intval(100 * $this->item->translated / $this->item->total) + $this->item->unchanged / $this->item->total
						: 100;

					$this->item->complete = $this->item->complete
						? 1
						: ($this->item->completed == 100
							? 1
							: 0);
				}

				if ($this->getState('translation.id'))
				{
					$table = $this->getTable();
					$table->load($this->getState('translation.id'));
					$user = JFactory::getUser($table->checked_out);
					$this->item->setProperties($table->getProperties());

					if ($this->item->checked_out == JFactory::getUser()->id)
					{
						$this->item->checked_out = 0;
					}

					$this->item->editor = JText::sprintf('COM_LOCALISE_TEXT_TRANSLATION_EDITOR', $user->name, $user->username);
				}

				if ($caching)
				{
					$cache->store($this->item, $keycache);
				}
			}
		}

		return $this->item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_localise.translation', 'translation', array('control'   => 'jform', 'load_data' => $loadData));

		$params = JComponentHelper::getParams('com_localise');

		// Set fields readonly if localise global params exist
		if ($params->get('author'))
		{
			$form->setFieldAttribute('author', 'readonly', 'true');
		}

		if ($params->get('copyright'))
		{
			$form->setFieldAttribute('maincopyright', 'readonly', 'true');
		}

		if ($params->get('additionalcopyright'))
		{
			$form->setFieldAttribute('additionalcopyright', 'readonly', 'true');
		}

		if ($params->get('license'))
		{
			$form->setFieldAttribute('license', 'readonly', 'true');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	protected function loadFormData()
	{
		return $this->getItem();
	}

	/**
	 * Method to get the ftp form.
	 *
	 * @return  mixed  A JForm object on success, false on failure or not ftp
	 */
	public function getFormFtp()
	{
		// Get the form.
		$form = $this->loadForm('com_localise.ftp', 'ftp');

		if (empty($form))
		{
			return false;
		}

		// Check for an error.
		if (JError::isError($form))
		{
			$this->setError($form->getMessage());

			return false;
		}

		return $form;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm   $form   A form object.
	 * @param   mixed   $item   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @throws  Exception if there is an error in the form event.
	 * @return  JForm
	 */
	protected function preprocessForm(JForm $form, $item, $group = 'content')
	{
		// Initialize variables.
		$filename              = $this->getState('translation.filename');
		$client                = $this->getState('translation.client');
		$tag                   = $this->getState('translation.tag');
		$origin                = LocaliseHelper::getOrigin($filename, $client);
		$app                   = JFactory::getApplication();
		$false                 = false;
		$untranslatablestrings = (array) $this->getState('translation.untranslatablestrings');
		$blockedstrings        = (array) $this->getState('translation.blockedstrings');
		$keystokeep            = (array) $this->getState('translation.keystokeep');

		// Compute all known languages
		static $languages = array();
		jimport('joomla.language.language');

		if (!array_key_exists($client, $languages))
		{
			$languages[$client] = JLanguage::getKnownLanguages(constant('LOCALISEPATH_' . strtoupper($client)));
		}

		if (is_object($item))
		{
			$form->setFieldAttribute('legend', 'unchanged', $item->unchanged, 'legend');
			$form->setFieldAttribute('legend', 'translated', $item->translated, 'legend');
			$form->setFieldAttribute('legend', 'untranslatable', $item->untranslatable, 'legend');
			$form->setFieldAttribute('legend', 'blocked', $item->blocked, 'legend');
			$form->setFieldAttribute('legend', 'untranslated', $item->total - $item->translated - $item->unchanged, 'legend');
			$form->setFieldAttribute('legend', 'extra', $item->extra, 'legend');
			$form->setFieldAttribute('legend', 'keytodelete', $item->keytodelete, 'legend');
		}

		if ($this->getState('translation.layout') != 'raw')
		{
			$path        = $this->getState('translation.path');
			$refpath     = $this->getState('translation.refpath');
			$sections    = LocaliseHelper::parseSections($path);
			$refsections = LocaliseHelper::parseSections($refpath);
			$addform     = new SimpleXMLElement('<form />');

			$group = $addform->addChild('fields');
			$group->addAttribute('name', 'strings');

			$fieldset = $group->addChild('fieldset');
			$fieldset->addAttribute('name', 'Default');
			$fieldset->addAttribute('label', 'Default');

			if (JFile::exists($refpath))
			{
				$stream = new JStream;
				$stream->open($refpath);
				$header     = true;
				$lineNumber = 0;
				$full_line  = '';

				while (!$stream->eof())
				{
					$line = $stream->gets();
					$lineNumber++;

					// Blank lines
					if (preg_match('/^\s*$/', $line))
					{
						$header = true;
						$field  = $fieldset->addChild('field');
						$field->addAttribute('label', '');
						$field->addAttribute('type', 'spacer');
						$field->addAttribute('class', 'text');
						continue;
					}
					// Section lines
					elseif (preg_match('/^\[([^\]]*)\]\s*$/', $line, $matches))
					{
						$header = false;
						$form->load($addform, false);
						$section = $matches[1];
						$addform = new SimpleXMLElement('<form />');
						$group   = $addform->addChild('fields');
						$group->addAttribute('name', 'strings');
						$fieldset = $group->addChild('fieldset');
						$fieldset->addAttribute('name', $section);
						$fieldset->addAttribute('label', $section);
						continue;
					}
					// Comment lines
					elseif (!$header && preg_match('/^;(.*)$/', $line, $matches))
					{
						$key   = $matches[1];
						$field = $fieldset->addChild('field');
						$field->addAttribute('label', $key);
						$field->addAttribute('type', 'spacer');
						$field->addAttribute('class', 'text');
						continue;
					}
					// Key lines
					elseif (preg_match('/^([A-Z][A-Z0-9_\-\.]*)\s*=/', $line, $matches))
					{
						$header     = false;
						$key        = $matches[1];
						$field      = $fieldset->addChild('field');
						$string     = $refsections['keys'][$key];
						$full_line  = htmlspecialchars_decode($key . '="' . $string . '"');
						$translated = isset($sections['keys'][$key]);
						$modified   = ($translated && $sections['keys'][$key] != $refsections['keys'][$key])
										|| ($translated && in_array($full_line, $untranslatablestrings));
						$status     = $modified ? 'translated' : ($translated ? 'unchanged' : 'untranslated');
						$default    = $translated ? $sections['keys'][$key] : '';
						$label      = '<b>' . $key . '</b><br />' . htmlspecialchars($string, ENT_COMPAT, 'UTF-8');

						if (in_array($full_line, $untranslatablestrings))
						{
							$status = "untranslatable";
						}

						if (in_array($full_line, $blockedstrings))
						{
							$field->addAttribute('isblocked', '1');
						}
						else
						{
							$field->addAttribute('isblocked', '0');
						}

						$field->addAttribute('status', $status);
						$field->addAttribute('description', $string);

						if ($default)
						{
							$field->addAttribute('default', $default);
						}
						else
						{
							$field->addAttribute('default', $string);
						}

						$field->addAttribute('label', $label);
						$field->addAttribute('name', $key);
						$field->addAttribute('type', 'key');
						$field->addAttribute('filter', 'raw');
						continue;
					}
					elseif (!preg_match('/^(|(\[[^\]]*\])|([A-Z][A-Z0-9_\-\.]*\s*=(\s*(("[^"]*")|(_QQ_)))+))\s*(;.*)?$/', $line))
					{
						$this->item->error[] = $lineNumber;
					}
				}

				$stream->close();
				$newstrings = false;
				$todeletestrings = false;

				if (!empty($sections['keys']))
				{
					foreach ($sections['keys'] as $key => $string)
					{
						if (!isset($refsections['keys'][$key]))
						{
							if (in_array($key, $keystokeep))
							{
								if (!$newstrings)
								{
									$newstrings = true;
									$form->load($addform, false);
									$section = 'Keys to keep as extra';
									$addform = new SimpleXMLElement('<form />');
									$group   = $addform->addChild('fields');
									$group->addAttribute('name', 'strings');
									$fieldset = $group->addChild('fieldset');
									$fieldset->addAttribute('name', $section);
									$fieldset->addAttribute('label', $section);
								}

								$status  = 'extra';

							}
							else
							{
								if (!$todeletestrings)
								{
									$todeletestrings = true;
									$form->load($addform, false);
									$section = 'Keys to delete';
									$addform = new SimpleXMLElement('<form />');
									$group   = $addform->addChild('fields');
									$group->addAttribute('name', 'strings');
									$fieldset = $group->addChild('fieldset');
									$fieldset->addAttribute('name', $section);
									$fieldset->addAttribute('label', $section);
								}

								$status  = 'keytodelete';
							}

							$field   = $fieldset->addChild('field');
							$default = $string;
							$label   = '<b>' . $key . '</b>';
							$field->addAttribute('status', $status);
							$field->addAttribute('description', $string);

							if ($default)
							{
								$field->addAttribute('default', $default);
							}
							else
							{
								$field->addAttribute('default', $string);
							}

							$field->addAttribute('label', $label);
							$field->addAttribute('name', $key);
							$field->addAttribute('type', 'key');
							$field->addAttribute('filter', 'raw');

							if (in_array($full_line, $blockedstrings))
							{
								$field->addAttribute('isblocked', '1');
							}
							else
							{
								$field->addAttribute('isblocked', '0');
							}
						}
					}
				}
			}

			$form->load($addform, false);
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.edit.translation.data', array());

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind($data);
		}

		if ($origin != '_thirdparty' && $origin != '_override')
		{
			$packages = LocaliseHelper::getPackages();
			$package  = $packages[$origin];

			if (!empty($package->author))
			{
				$form->setValue('author', $package->author);
				$form->setFieldAttribute('author', 'readonly', 'true');
			}

			if (!empty($package->copyright))
			{
				$form->setValue('maincopyright', $package->copyright);
				$form->setFieldAttribute('maincopyright', 'readonly', 'true');
			}

			if (!empty($package->license))
			{
				$form->setValue('license', $package->license);
				$form->setFieldAttribute('license', 'readonly', 'true');
			}
		}

		if ($form->getValue('description') == '' && array_key_exists($tag, $languages[$client]))
		{
			$form->setValue('description', $filename . ' ' . $languages[$client][$tag]['name']);
		}

		return $form;
	}

	/**
	 * Save a file
	 *
	 * @param   array  $data  Array that represents a file
	 *
	 * @return bool
	 */
	public function saveFile($data)
	{
		$path       = $this->getState('translation.path');
		$refpath    = $this->getState('translation.refpath');
		$exists     = JFile::exists($path);
		$refexists  = JFile::exists($refpath);
		$client     = $this->getState('translation.client');
		$keystokeep = (array) $this->getState('translation.keystokeep');

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');

		// Try to make the file writeable.
		if ($exists && !$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, '0644'))
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_TRANSLATION_WRITABLE', $path));

			return false;
		}

		if (array_key_exists('source', $data))
		{
			$contents = $data['source'];
		}
		else
		{
			$data['description']  = str_replace(array("\r\n", "\n", "\r"), " ", $data['description']);
			$additionalcopyrights = trim($data['additionalcopyright']);

			if (empty($additionalcopyrights))
			{
				$additionalcopyrights = array();
			}
			else
			{
				$additionalcopyrights = explode("\n", $additionalcopyrights);
			}

			$contents2 = '';

			if (!empty($data['svn']))
			{
				$contents2 .= "; " . $data['svn'] . "\n;\n";
			}

			if (!empty($data['package']))
			{
				$contents2 .= "; @package     " . $data['package'] . "\n";
			}

			if (!empty($data['subpackage']))
			{
				$contents2 .= "; @subpackage  " . $data['subpackage'] . "\n";
			}

			if (!empty($data['description']) && $data['description'] != '[Description] [Name of language]([Country code])')
			{
				$contents2 .= "; @description " . $data['description'] . "\n";
			}

			if (!empty($data['version']))
			{
				$contents2 .= "; @version     " . $data['version'] . "\n";
			}

			if (!empty($data['creationdate']))
			{
				$contents2 .= "; @date        " . $data['creationdate'] . "\n";
			}

			if (!empty($data['author']))
			{
				$contents2 .= "; @author      " . $data['author'] . "\n";
			}

			if (!empty($data['maincopyright']))
			{
				$contents2 .= "; @copyright   " . $data['maincopyright'] . "\n";
			}

			foreach ($additionalcopyrights as $copyright)
			{
				$contents2 .= "; @copyright   " . $copyright . "\n";
			}

			if (!empty($data['license']))
			{
				$contents2 .= "; @license     " . $data['license'] . "\n";
			}

			if (array_key_exists('complete', $data) && ($data['complete'] == '1'))
			{
				$contents2 .= "; @note        Complete\n";
			}

			$contents2 .= "; @note        Client " . ucfirst($client) . "\n";
			$contents2 .= "; @note        All ini files need to be saved as UTF-8 - No BOM\n\n";

			$contents = array();
			$stream   = new JStream;

			if ($exists)
			{
				$stream->open($path);

				while (!$stream->eof())
				{
					$line = $stream->gets();

					// Comment lines
					if (preg_match('/^(;.*)$/', $line, $matches))
					{
						// $contents[] = $matches[1]."\n";
					}
					else
					{
						break;
					}
				}

				if ($refexists)
				{
					$stream->close();
					$stream->open($refpath);

					while (!$stream->eof())
					{
						$line = $stream->gets();

						// Comment lines
						if (!preg_match('/^(;.*)$/', $line, $matches))
						{
							break;
						}
					}
				}
			}
			else
			{
				$stream->open($refpath);

				while (!$stream->eof())
				{
					$line = $stream->gets();

					// Comment lines
					if (preg_match('/^(;.*)$/', $line, $matches))
					{
						$contents[] = $matches[1] . "\n";
					}
					else
					{
						break;
					}
				}
			}

			$strings = $data['strings'];

			while (!$stream->eof())
			{
				if (preg_match('/^([A-Z][A-Z0-9_\-\.]*)\s*=/', $line, $matches))
				{
					$key = $matches[1];

					if (isset($strings[$key]))
					{
						$contents[] = $key . '="' . str_replace('"', '"_QQ_"', $strings[$key]) . "\"\n";
						unset($strings[$key]);
					}
				}
				else
				{
					$contents[] = $line;
				}

				$line = $stream->gets();
			}

			if (!empty($strings))
			{
				$contents_to_add = array();
				$contents_to_delete = array();

				foreach ($strings as $key => $string)
				{
					if (in_array($key, $keystokeep))
					{
						$contents_to_add[] = $key . '="' . str_replace('"', '"_QQ_"', $string) . "\"\n";
					}
					else
					{
						$contents_to_delete[] = $key . '="' . str_replace('"', '"_QQ_"', $string) . "\"\n";
					}
				}
			}

			$stream->close();
			$contents = implode($contents);
			$contents = $contents2 . $contents;

			if (!empty($contents_to_add))
			{
				$contents .= "\n[Keys to keep in target]\n\n";
				$contents .= ";The next keys are not present in en-GB language but are used as extra in this language
							(extra plural cases, custom CAPTCHA translations, etc).\n\n";
				$contents_to_add = implode($contents_to_add);
				$contents .= $contents_to_add;
			}

			if (!empty($contents_to_delete))
			{
				$contents .= "\n[Keys to delete]\n\n";
				$contents .= ";This keys are not used in en-GB language and are not required in this language.\n\n";
				$contents_to_delete = implode($contents_to_delete);
				$contents .= $contents_to_delete;
			}
		}

		$return = JFile::write($path, $contents);

		// Try to make the template file unwriteable.

		// Get the parameters
		$coparams = JComponentHelper::getParams('com_localise');

		// Get the file save permission
		$fsper = $coparams->get('filesavepermission', '0444');

		if (!$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, $fsper))
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_TRANSLATION_UNWRITABLE', $path));

			return false;
		}
		else
		{
			if (!$return)
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_TRANSLATION_FILESAVE', $path));

				return false;
			}
		}

		// Remove the cache
		$conf    = JFactory::getConfig();
		$caching = $conf->get('caching') >= 1;

		if ($caching)
		{
			$keycache = $this->getState('translation.client') . '.'
				. $this->getState('translation.tag') . '.'
				. $this->getState('translation.filename') . '.' . 'translation';
			$cache    = JFactory::getCache('com_localise', '');
			$cache->remove($keycache);
		}
	}

	/**
	 * Saves a translation
	 *
	 * @param   array  $data  translation to be saved
	 *
	 * @return bool
	 */
	public function save($data)
	{
		// Fix DOT saving issue
		$input = JFactory::getApplication()->input;

		$formData = $input->get('jform', array(), 'ARRAY');

		if (!empty($formData['strings']))
		{
			$data['strings'] = $formData['strings'];
		}

		// Special case for lib_joomla
		if ($this->getState('translation.filename') == 'lib_joomla')
		{
			$tag = $this->getState('translation.tag');

			if (JFolder::exists(JPATH_SITE . "/language/$tag"))
			{
				$this->setState('translation.client', 'site');
				$this->setState('translation.path', JPATH_SITE . "/language/$tag/$tag.lib_joomla.ini");
				$this->saveFile($data);
			}

			if (JFolder::exists(JPATH_ADMINISTRATOR . "/language/$tag"))
			{
				$this->setState('translation.client', 'administrator');
				$this->setState('translation.path', JPATH_ADMINISTRATOR . "/language/$tag/$tag.lib_joomla.ini");
				$this->saveFile($data);
			}
		}
		else
		{
			$this->saveFile($data);
		}

		// Bind the rules.
		$table = $this->getTable();
		$table->load($data['id']);

		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}
}
