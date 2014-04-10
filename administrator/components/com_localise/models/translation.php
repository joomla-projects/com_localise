<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.stream');
jimport('joomla.client.helper');
jimport('joomla.access.rules' );

/**
 * Translation Model class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseModelTranslation extends JModelForm
{
	protected $item;
	protected $contents;

	protected function populateState()
	{
		// Get the infos
		$client   = JRequest::getVar('client'  , '', 'default', 'cmd');
		$tag      = JRequest::getVar('tag'     , '', 'default', 'cmd');
		$filename = JRequest::getVar('filename', '', 'default', 'cmd');
		$storage  = JRequest::getVar('storage' , '', 'default', 'cmd');

		$this->setState('translation.client'  , !empty($client) ? $client : 'site');
		$this->setState('translation.tag'     , $tag);
		$this->setState('translation.filename', $filename);
		$this->setState('translation.storage' , $storage);

		// Get the id
		$id = JRequest::getVar('id', '0', 'default', 'int');
		$this->setState('translation.id', $id);

		// Get the layout
		$layout = JRequest::getVar('layout', 'edit', 'default', 'cmd');
		$this->setState('translation.layout', $layout);

		// Get the parameters
		$params = JComponentHelper::getParams('com_localise');

		// Get the reference tag
		$ref = $params->get('language.reference', 'en-GB');
		$this->setState('translation.reference', $ref);

		// Get the paths
		$path = LocaliseHelper::getTranslationPath($client, $tag, $filename, $storage);
		if ($filename == 'lib_joomla')
		{
			$refpath = LocaliseHelper::findTranslationPath('administrator', $ref, $filename);

			if (!JFile::exists($path))
			{
				$path2 = LocaliseHelper::getTranslationPath($client=='administrator' ? 'site' : 'administrator', $tag, $filename, $storage);

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
	 * @param   type    $type     The table type to instantiate
	 * @param   string  $prefix   A prefix for the table class name. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A database object
	 */
	public function getTable($type = 'Localise', $prefix = 'LocaliseTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

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

	public function getItem()
	{
		if (!isset($this->item))
		{
			$conf    = JFactory::getConfig();
			$caching = $conf->get('caching') >= 1;

			if ($caching)
			{
				$keycache = $this->getState('translation.client') . '.' . $this->getState('translation.tag') . '.' . $this->getState('translation.filename') . '.' . 'translation';
				$cache = JFactory::getCache('com_localise', '');
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
				$path = JFile::exists($this->getState('translation.path')) ? $this->getState('translation.path') : $this->getState('translation.refpath');

				$this->item = new JObject(array('reference' => $this->getState('translation.reference'), 'bom' => 'UTF-8', 'svn' => '', 'version' => '', 'description' => '', 'creationdate' => '', 'author' => '', 'maincopyright' => '', 'additionalcopyright' => array(), 'license' => '', 'exists' => JFile::exists($this->getState('translation.path')), 'translated' => 0, 'unchanged' => 0, 'extra' => 0, 'total' => 0, 'complete' => false, 'source' => '', 'error' => array()));

				if (JFile::exists($path))
				{
					$this->item->source = file_get_contents($path);
					$stream = new JStream();
					$stream->open($path);
					$begin  = $stream->read(4);
					$bom    = strtolower(bin2hex($begin));

					if ($bom == '0000feff')
					{
						$this->item->bom = 'UTF-32 BE';
					}
					else if ($bom == 'feff0000')
					{
						$this->item->bom = 'UTF-32 LE';
					}
					else if (substr($bom, 0, 4) == 'feff')
					{
						$this->item->bom = 'UTF-16 BE';
					}
					else if (substr($bom, 0, 4) == 'fffe')
					{
						$this->item->bom = 'UTF-16 LE';
					}

					$stream->seek(0);
					$continue   = true;
					$lineNumber = 0;

					$params = JComponentHelper::getParams('com_localise');
					$isTranslationsView = JRequest::getVar('view') == 'translations';

					while (!$stream->eof())
					{
						$line = $stream->gets();
						$lineNumber++;

						if ($line[0] == '#' || $line[0] == ';')
						{
							if (preg_match('/^(#|;).*(\$Id.*\$)/', $line, $matches))
							{
								$this->item->svn = $matches[2];
							}
							elseif (preg_match('/(#|;)\s*@?(\pL+):?.*/', $line, $matches))
							{
								switch (strtolower($matches[2]))
								{
									case 'note':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->complete = $this->item->complete || strtolower($matches2[3]) == 'complete';
										break;
									case 'version':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->version = $matches2[3];
										break;
									case 'desc':
									case 'description':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->description = $matches2[3];
										break;
									case 'date':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->creationdate = $matches2[3];
										break;
									case 'author':
										if ($params->get('author') && !$isTranslationsView)
										{
											$this->item->author = $params->get('author');
										}
										else
										{
											preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
											$this->item->author = $matches2[3];
										}
										break;
									case 'copyright':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										if (empty($this->item->maincopyright))
										{
											if($params->get('copyright') && !$isTranslationsView)
											{
												$this->item->maincopyright = $params->get('copyright');
											}
											else
											{
												$this->item->maincopyright = $matches2[3];
											}
										}
										if(empty($this->item->additionalcopyright))
										{
											if($params->get('additionalcopyright') && !$isTranslationsView)
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
										if($params->get('license') && !$isTranslationsView)
										{
											$this->item->license = $params->get('license');
										}
										else
										{
											preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
											$this->item->license = $matches2[3];
										}
										break;
									case 'package':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->package = $matches2[3];
										break;
									case 'subpackage':
										preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
										$this->item->subpackage = $matches2[3];
										break;
									case 'link':
										break;
									default:
										if (empty($this->item->author))
										{
											if($params->get('author') && !$isTranslationsView)
											{
												$this->item->author = $params->get('author');
											}
											else
											{
												preg_match('/(#|;)\s*(.*)/', $line, $matches2);
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

					if(empty($this->item->license) && $params->get('license') && !$isTranslationsView)
					{
						$this->item->license = $params->get('license');
					}

					if(empty($this->item->maincopyright) && $params->get('copyright') && !$isTranslationsView)
					{
						$this->item->maincopyright = $params->get('copyright');
					}

					if(empty($this->item->additionalcopyright) && $params->get('additionalcopyright') && !$isTranslationsView)
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
							if (!empty($sections['keys']) && array_key_exists($key, $sections['keys']) && $sections['keys'][$key] != '')
							{

								if ($sections['keys'][$key] != $string || $this->getState('translation.path') == $this->getState('translation.refpath'))
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
							if (empty($refsections['keys']) || !array_key_exists($key, $refsections['keys']))
							{
								$this->item->extra++;
							}
						}
					}

					$this->item->completed = $this->item->total ? intval(100 * $this->item->translated / $this->item->total) + $this->item->unchanged / $this->item->total : 100;

					$this->item->complete = $this->item->complete ? 1 : ($this->item->completed == 100 ? 1 : 0);
				}

				if ($this->getState('translation.id'))
				{
					$table = $this->getTable();
					$table->load($this->getState('translation.id'));
					$user  = JFactory::getUser($table->checked_out);
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

			//$this->item->error = $this->getErrors();
		}

		return $this->item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param  array  $data    Data for the form.
	 * @param  boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed  A JForm object on success, false on failure
	 * @since  1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_localise.translation', 'translation', array('control' => 'jform', 'load_data' => $loadData));

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
	 * @since  1.6
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
	 * @param  object  A form object.
	 * @param  mixed  The data expected for the form.
	 * @param  string  The name of the plugin group to import (defaults to "content").
	 * @throws  Exception if there is an error in the form event.
	 * @since  1.6
	 */
	protected function preprocessForm(JForm $form, $item, $group = 'content')
	{
		// Initialize variables.
		$filename = $this->getState('translation.filename');
		$client   = $this->getState('translation.client');
		$tag      = $this->getState('translation.tag');
		$origin   = LocaliseHelper::getOrigin($filename, $client);
		$app      = JFactory::getApplication();
		$false    = false;

		// Compute all known languages
		static $languages = array();
		jimport('joomla.language.language');

		if (!array_key_exists($client, $languages))
		{
			$languages[$client] = JLanguage::getKnownLanguages(constant('LOCALISEPATH_' . strtoupper($client)));
		}

		if (is_object($item))
		{
			$form->setFieldAttribute('legend', 'unchanged'   , $item->unchanged, 'legend');
			$form->setFieldAttribute('legend', 'translated'  , $item->translated, 'legend');
			$form->setFieldAttribute('legend', 'untranslated', $item->total - $item->translated - $item->unchanged, 'legend');
			$form->setFieldAttribute('legend', 'extra'       , $item->extra, 'legend');
		}

		if ($this->getState('translation.layout') != 'raw')
		{
			$path        = $this->getState('translation.path');
			$refpath     = $this->getState('translation.refpath');
			$sections    = LocaliseHelper::parseSections($path);
			$refsections = LocaliseHelper::parseSections($refpath);
			$addform     = new JXMLElement('<form />');

			$group = $addform->addChild('fields');
			$group->addAttribute('name', 'strings');

			$fieldset = $group->addChild('fieldset');
			$fieldset->addAttribute('name', 'Default');
			$fieldset->addAttribute('label', 'Default');

			if (JFile::exists($refpath))
			{
				$stream = new JStream();
				$stream->open($refpath);
				$header = true;
				$lineNumber = 0;

				while (!$stream->eof())
				{
					$line = $stream->gets();
					$lineNumber++;

					// Blank lines
					if (preg_match('/^\s*$/', $line))
					{
						$header = true;
						$field = $fieldset->addChild('field');
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
						$addform = new JXMLElement('<form />');
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
						$header = false;
						$key = $matches[1];
						$field = $fieldset->addChild('field');
						$string = $refsections['keys'][$key];
						$translated = isset($sections['keys'][$key]);
						$modified = $translated && $sections['keys'][$key]!=$refsections['keys'][$key];
						$status = $modified ? 'translated' : ($translated ? 'unchanged' : 'untranslated');
						$default = $translated ? $sections['keys'][$key] : '';
						$label = '<b>' . $key . '</b><br />' . htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
						$field->addAttribute('status', $status);
						$field->addAttribute('description', $string);
						if($default) $field->addAttribute('default', $default);
						else $field->addAttribute('default', $string);
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

				if (!empty($sections['keys']))
				{
					foreach ($sections['keys'] as $key=>$string)
					{
						if (!isset($refsections['keys'][$key]))
						{
							if (!$newstrings)
							{
								$newstrings = true;
								$form->load($addform, false);
								$section  = 'New Strings';
								$addform  = new JXMLElement('<form />');
								$group    = $addform->addChild('fields');
								$group->addAttribute('name', 'strings');
								$fieldset = $group->addChild('fieldset');
								$fieldset->addAttribute('name', $section);
								$fieldset->addAttribute('label', $section);
							}

							$field   = $fieldset->addChild('field');
							$status  = 'extra';
							$default = $string;
							$label   = '<b>' . $key . '</b>';
							$field->addAttribute('status', $status);
							$field->addAttribute('description', $string);

							if($default)
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

		if ($form->getValue('description') == '' && array_key_exists($tag,$languages[$client]))
		{
			$form->setValue('description', $filename . ' ' . $languages[$client][$tag]['name']);
		}

		return $form;
	}

	public function saveFile($data)
	{
		$path      = $this->getState('translation.path');
		$refpath   = $this->getState('translation.refpath');
		$exists    = JFile::exists($path);
		$refexists = JFile::exists($refpath);
		$client    = $this->getState('translation.client');

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
			$data['description'] = str_replace(array("\r\n", "\n", "\r"), " ", $data['description']);
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
				$contents2.= "; " . $data['svn'] . "\n;\n";
			}

			if (!empty($data['package']))
			{
				$contents2.= "; @package     " . $data['package'] . "\n";
			}

			if (!empty($data['subpackage']))
			{
				$contents2.= "; @subpackage  " . $data['subpackage'] . "\n";
			}

			if (!empty($data['description']))
			{
				$contents2.= "; @description " . $data['description'] . "\n";
			}

			if (!empty($data['version']))
			{
				$contents2.= "; @version     " . $data['version'] . "\n";
			}

			if (!empty($data['creationdate']))
			{
				$contents2.= "; @date        " . $data['creationdate'] . "\n";
			}

			if (!empty($data['author']))
			{
				$contents2.= "; @author      " . $data['author'] . "\n";
			}

			if (!empty($data['maincopyright']))
			{
				$contents2.= "; @copyright   " . $data['maincopyright'] . "\n";
			}

			foreach ($additionalcopyrights as $copyright)
			{
				$contents2.= "; @copyright   " . $copyright . "\n";
			}

			if (!empty($data['license']))
			{
				$contents2.= "; @license     " . $data['license'] . "\n";
			}

			if (array_key_exists('complete', $data) && ($data['complete'] == '1'))
			{
				$contents2.= "; @note        Complete\n";
			}

			$contents2.= "; @note        Client " . ucfirst($client) . "\n";
			$contents2.= "; @note        All ini files need to be saved as UTF-8 - No BOM\n\n";

			$contents = array();
			$stream   = new JStream();

			if ($exists)
			{
				$stream->open($path);

				while (!$stream->eof())
				{
					$line = $stream->gets();

					// Comment lines
					if (preg_match('/^(;.*)$/', $line, $matches))
					{
						//$contents[] = $matches[1]."\n";
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
						$contents[] = $matches[1]."\n";
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
						$contents[] = $key.'="' . str_replace('"', '"_QQ_"', $strings[$key]) . "\"\n";
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
				$contents[] = "\n[New Strings]\n\n";

				foreach($strings as $key=>$string)
				{
					$contents[] = $key.'="' . str_replace('"', '"_QQ_"', $string) . "\"\n";
				}
			}

			$stream->close();
			$contents = implode($contents);
			$contents = $contents2.$contents;
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
		else if (!$return)
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_TRANSLATION_FILESAVE', $path));
			return false;
		}

		// Remove the cache
		$conf    = JFactory::getConfig();
		$caching = $conf->get('caching') >= 1;

		if ($caching)
		{
			$keycache = $this->getState('translation.client') . '.' . $this->getState('translation.tag') . '.' . $this->getState('translation.filename') . '.' . 'translation';
			$cache = JFactory::getCache('com_localise', '');
			$cache->remove($keycache);
		}
	}

	public function save($data)
	{
		// Fix DOT saving issue
		$strings_array   = JRequest::get( 'post' );
		$strings         = $strings_array['jform']['strings'];
		$data['strings'] = $strings;

		// Special case for lib_joomla
		if ($this->getState('translation.filename')=='lib_joomla')
		{
			$tag = $this->getState('translation.tag');

			if (JFolder::exists(JPATH_SITE . "/language/$tag" ))
			{
				$this->setState('translation.client', 'site');
				$this->setState('translation.path', JPATH_SITE . "/language/$tag/$tag.lib_joomla.ini");
				$this->saveFile($data);
			}

			if (JFolder::exists(JPATH_ADMINISTRATOR . "/language/$tag" ))
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
