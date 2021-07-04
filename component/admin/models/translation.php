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
				$path = JFile::exists($this->getState('translation.path'))
					? $this->getState('translation.path')
					: $this->getState('translation.refpath');

				$params              = JComponentHelper::getParams('com_localise');
				$allow_develop       = $params->get('gh_allow_develop', 0);
				$gh_client           = $this->getState('translation.client');
				$tag                 = $this->getState('translation.tag');
				$reftag              = $this->getState('translation.reference');
				$refpath             = $this->getState('translation.refpath');
				$istranslation       = 0;

				if (!empty($tag) && $tag != $reftag)
				{
					$istranslation = 1;
				}

				$this->setState('translation.translatedkeys', array());
				$this->setState('translation.untranslatedkeys', array());
				$this->setState('translation.unchangedkeys', array());
				$this->setState('translation.textchangedkeys', array());
				$this->setState('translation.revisedchanges', array());
				$this->setState('translation.developdata', array());

				$translatedkeys   = $this->getState('translation.translatedkeys');
				$untranslatedkeys = $this->getState('translation.untranslatedkeys');
				$unchangedkeys    = $this->getState('translation.unchangedkeys');
				$textchangedkeys  = $this->getState('translation.textchangedkeys');
				$revisedchanges  = $this->getState('translation.revisedchanges');
				$developdata      = $this->getState('translation.developdata');

				$this->item = new JObject(
									array
										(
										'reference'           => $this->getState('translation.reference'),
										'bom'                 => 'UTF-8',
										'svn'                 => '',
										'version'             => '',
										'description'         => '',
										'creationdate'        => '',
										'author'              => '',
										'maincopyright'       => '',
										'additionalcopyright' => array(),
										'license'             => '',
										'exists'              => JFile::exists($this->getState('translation.path')),
										'istranslation'       => $istranslation,
										'developdata'         => (array) $developdata,
										'translatedkeys'      => (array) $translatedkeys,
										'untranslatedkeys'    => (array) $untranslatedkeys,
										'unchangedkeys'       => (array) $unchangedkeys,
										'textchangedkeys'     => (array) $textchangedkeys,
										'revisedchanges'      => (array) $revisedchanges,
										'unrevised'           => 0,
										'translatednews'      => 0,
										'unchangednews'       => 0,
										'translated'          => 0,
										'untranslated'        => 0,
										'unchanged'           => 0,
										'extra'               => 0,
										'total'               => 0,
										'linespath'           => 0,
										'linesrefpath'        => 0,
										'linesdevpath'        => 0,
										'linescustompath'     => 0,
										'complete'            => false,
										'source'              => '',
										'error'               => array()
										)
				);

				if (JFile::exists($path))
				{
					$devpath    = LocaliseHelper::searchDevpath($gh_client, $refpath);
					$custompath = LocaliseHelper::searchCustompath($gh_client, $refpath);

					if ($istranslation == 0 && $reftag == 'en-GB')
					{
						if (!empty($devpath))
						{
							if (!empty($custompath))
							{
								$this->item->source = LocaliseHelper::combineReferences($custompath, $devpath);
							}
							else
							{
								$this->item->source = LocaliseHelper::combineReferences($path, $devpath);
							}
						}
					}
					else
					{
						$this->item->source = file_get_contents($path);
					}

					$stream = new JStream;
					$stream->open($path);
					$begin  = $stream->read(4);
					$bom    = strtolower(bin2hex($begin));

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
					$continue   = true;
					$lineNumber = 0;

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
						$line = str_replace('\"', '"_QQ_"', $line);

						if (!preg_match('/^(|(\[[^\]]*\])|([A-Z][A-Z0-9_\*\-\.]*\s*=(\s*(("[^"]*")|(_QQ_)))+))\s*(;.*)?$/', $line))
						{
							$this->item->error[] = $lineNumber;
						}
					}

					if ($tag != $reftag)
					{
						if (JFile::exists($custompath))
						{
							$this->item->linescustompath = count(file($custompath));
						}
					}

					$stream->close();
				}

				$this->item->additionalcopyright = implode("\n", $this->item->additionalcopyright);

				if ($this->getState('translation.layout') != 'raw' && empty($this->item->error))
				{
					$sections = LocaliseHelper::parseSections($this->getState('translation.path'));

						if (!empty($custompath))
						{
							$refsections = LocaliseHelper::parseSections($custompath);
						}
						else
						{
							$refsections = LocaliseHelper::parseSections($this->getState('translation.refpath'));
						}

					$develop_client_path = JPATH_ROOT
								. '/media/com_localise/develop/github/joomla-cms/en-GB/'
								. $gh_client;
					$develop_client_path = JFolder::makeSafe($develop_client_path);
					$ref_file            = basename($this->getState('translation.refpath'));
					$develop_file_path   = "$develop_client_path/$ref_file";
					$new_keys            = array();

					if (JFile::exists($develop_file_path) && $allow_develop == 1 && $reftag == 'en-GB')
					{
						$info                  = array();
						$info['client']        = $gh_client;
						$info['reftag']        = 'en-GB';
						$info['tag']           = 'en-GB';
						$info['filename']      = $ref_file;
						$info['istranslation'] = $istranslation;

						$develop_sections = LocaliseHelper::parseSections($develop_file_path);
						$developdata      = LocaliseHelper::getDevelopchanges($info, $refsections, $develop_sections);
						$developdata['develop_file_path'] = '';

						if ($developdata['extra_keys']['amount'] > 0  || $developdata['text_changes']['amount'] > 0)
						{
							if ($developdata['extra_keys']['amount'] > 0)
							{
								$new_keys = $developdata['extra_keys']['keys'];
							}

							if ($developdata['text_changes']['amount'] > 0)
							{
								$textchangedkeys = $developdata['text_changes']['keys'];
								$this->item->textchangedkeys = $textchangedkeys;
								$this->setState('translation.textchangedkeys', $textchangedkeys);

								$changesdata['client'] = $gh_client;
								$changesdata['reftag'] = $reftag;

									if ($istranslation == 0)
									{
										$changesdata['tag'] = $reftag;
									}
									else
									{
										$changesdata['tag'] = $tag;
									}

								$changesdata['filename'] = $ref_file;

								foreach ($textchangedkeys as $key_changed)
								{
									$target_text = $developdata['text_changes']['ref_in_dev'][$key_changed];
									$source_text = $developdata['text_changes']['ref'][$key_changed];

									$changesdata['revised']       = '0';
									$changesdata['key']           = $key_changed;
									$changesdata['target_text']   = $target_text;
									$changesdata['source_text']   = $source_text;
									$changesdata['istranslation'] = $istranslation;
									$changesdata['catch_grammar'] = '0';

									$change_status = LocaliseHelper::searchRevisedvalue($changesdata);
									$revisedchanges[$key_changed] = $change_status;

									if ($change_status == 1)
									{
										$developdata['text_changes']['revised']++;
									}
									else
									{
										$developdata['text_changes']['unrevised']++;
									}
								}

								$this->item->revisedchanges = $revisedchanges;
								$this->setState('translation.revisedchanges', $revisedchanges);
							}

							// When develop changes are present, replace the reference keys
							$refsections = $develop_sections;

							// And store the path for future calls
							$developdata['develop_file_path'] = $develop_file_path;
						}
					}

					if (!empty($refsections['keys']))
					{
						foreach ($refsections['keys'] as $key => $string)
						{
							$this->item->total++;

							if (!empty($sections['keys']) && array_key_exists($key, $sections['keys']) && $sections['keys'][$key] != '')
							{
								if ($sections['keys'][$key] != $string && $istranslation == 1)
								{
									if (array_key_exists($key, $revisedchanges) && $revisedchanges[$key] == 0)
									{
										$this->item->unrevised++;
										$translatedkeys[] = $key;
									}
									elseif (in_array($key, $new_keys))
									{
										$this->item->translatednews++;
										$translatedkeys[] = $key;
									}
									else
									{
										$this->item->translated++;
										$translatedkeys[] = $key;
									}
								}
								elseif ($istranslation == 0)
								{
									if (array_key_exists($key, $revisedchanges) && $revisedchanges[$key] == 0)
									{
										$this->item->unrevised++;
									}
									elseif (in_array($key, $new_keys))
									{
										$untranslatedkeys[] = $key;
									}

									$this->item->translated++;
								}
								else
								{
									if (in_array($key, $new_keys))
									{
										$this->item->unchangednews++;
									}
									else
									{
										$this->item->unchanged++;
									}

									$unchangedkeys[] = $key;
								}
							}
							elseif (!array_key_exists($key, $sections['keys']))
							{
								$this->item->untranslated++;
								$untranslatedkeys[] = $key;
							}
						}
					}

					$this->item->translatedkeys   = $translatedkeys;
					$this->item->untranslatedkeys = $untranslatedkeys;
					$this->item->unchangedkeys    = $unchangedkeys;
					$this->item->developdata      = $developdata;

					$this->setState('translation.translatedkeys', $translatedkeys);
					$this->setState('translation.untranslatedkeys', $untranslatedkeys);
					$this->setState('translation.unchangedkeys', $unchangedkeys);
					$this->setState('translation.developdata', $developdata);

					if (!empty($sections['keys']) && $istranslation == 1)
					{
						foreach ($sections['keys'] as $key => $string)
						{
							if (empty($refsections['keys']) || !array_key_exists($key, $refsections['keys']))
							{
								$this->item->extra++;
							}
						}
					}

					$done = $this->item->translated + $this->item->translatednews + $this->item->unchangednews;

					$this->item->completed = $this->item->total
						? intval(100 * $done / $this->item->total)
						: 100;

					$this->item->complete = $this->item->complete == 1 && $this->item->untranslated == 0 && $this->item->unrevised == 0
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

				// Count the number of lines in the ini file to check max_input_vars
				if ($tag != $reftag)
				{
					if (JFile::exists($path))
					{
						$this->item->linespath = count(file($path));
					}

					if (JFile::exists($refpath))
					{
						$this->item->linesrefpath = count(file($refpath));
					}

					if ($this->getState('translation.layout') != 'raw')
					{
						if (isset($develop_file_path) && JFile::exists($develop_file_path))
						{
							$this->item->linesdevpath = count(file($develop_file_path));
						}
					}
				}
				else
				{
					if (JFile::exists($path))
					{
						$this->item->linespath = count(file($path));
					}
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
		if (version_compare(JVERSION, '4.0', 'le') && JError::isError($form))
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
		$filename = $this->getState('translation.filename');
		$client   = $this->getState('translation.client');
		$tag      = $this->getState('translation.tag');
		$origin   = LocaliseHelper::getOrigin($filename, $client);
		$app      = JFactory::getApplication();
		$false    = false;

		$have_develop        = 0;
		$developdata         = array();
		$revisedchanges      = array();
		$istranslation       = '';
		$extras_amount       = 0;
		$text_changes_amount = 0;

		// Compute all known languages
		static $languages = array();
		jimport('joomla.language.language');

		if (!array_key_exists($client, $languages))
		{
			if (version_compare(JVERSION, '3.7', 'ge'))
			{
				$languages[$client] = JLanguageHelper::getKnownLanguages(constant('LOCALISEPATH_' . strtoupper($client)));
			}
			else
			{
				$languages[$client] = JLanguage::getKnownLanguages(constant('LOCALISEPATH_' . strtoupper($client)));
			}
		}

		if (is_object($item))
		{
			$form->setFieldAttribute('legend', 'unchanged', $item->unchanged, 'legend');
			$form->setFieldAttribute('legend', 'translated', $item->translated, 'legend');
			$form->setFieldAttribute('legend', 'untranslated', $item->total - $item->translated - $item->unchanged, 'legend');
			$form->setFieldAttribute('legend', 'extra', $item->extra, 'legend');

			$developdata    = $item->developdata;
			$revisedchanges = $item->revisedchanges;
			$istranslation  = $item->istranslation;
		}

		if ($this->getState('translation.layout') != 'raw')
		{
			$this->setState('translation.devpath', '');

			if (!empty($developdata))
			{
				$extras_amount       = $developdata['extra_keys']['amount'];
				$text_changes_amount = $developdata['text_changes']['amount'];
				$refpath             = $this->getState('translation.refpath');

				$custompath          = LocaliseHelper::searchCustompath($client, $refpath);

				if ($istranslation == '0')
				{
					if (!empty($custompath))
					{
						$refpath     = $custompath;
						$path        = $refpath;
						$refsections = LocaliseHelper::parseSections($refpath);
						$sections    = $refsections;
					}
					else
					{
						$refpath     = $this->getState('translation.refpath');
						$path        = $refpath;
						$refsections = LocaliseHelper::parseSections($refpath);
						$sections    = $refsections;
					}
				}
				else
				{
					if (!empty($custompath))
					{
						$refpath     = $custompath;
						$path        = $this->getState('translation.path');
						$refsections = LocaliseHelper::parseSections($refpath);
						$sections    = LocaliseHelper::parseSections($path);
					}
					else
					{
						$refpath     = $this->getState('translation.refpath');
						$path        = $this->getState('translation.path');
						$refsections = LocaliseHelper::parseSections($refpath);
						$sections    = LocaliseHelper::parseSections($path);
					}
				}

				if ($extras_amount > 0  || $text_changes_amount > 0)
				{
					$have_develop      = 1;
					$develop_file_path = $developdata['develop_file_path'];
					$develop_sections  = LocaliseHelper::parseSections($develop_file_path);
					$oldref            = $refsections;
					$refsections       = $develop_sections;
					$refpath           = $develop_file_path;

					$this->setState('translation.devpath', $develop_file_path);
				}
			}
			else
			{
				$path        = $this->getState('translation.path');
				$refpath     = $this->getState('translation.refpath');
				$sections    = LocaliseHelper::parseSections($path);
				$refsections = LocaliseHelper::parseSections($refpath);
			}

			$addform     = new SimpleXMLElement('<form />');

			$group = $addform->addChild('fields');
			$group->addAttribute('name', 'strings');

			$fieldset = $group->addChild('fieldset');
			$fieldset->addAttribute('name', 'JDEFAULT');
			$fieldset->addAttribute('label', 'JDEFAULT');

			if (JFile::exists($refpath))
			{
				$stream = new JStream;
				$stream->open($refpath);
				$header     = true;
				$lineNumber = 0;

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
					elseif (preg_match('/^([A-Z][A-Z0-9_\*\-\.]*)\s*=/', $line, $matches))
					{
						$header     = false;
						$key        = $matches[1];
						$field      = $fieldset->addChild('field');

						if ($have_develop == '1' && $istranslation == '0' && array_key_exists($key, $oldref['keys']))
						{
							$string = $oldref['keys'][$key];
							$translated = isset($sections['keys'][$key]);
							$modified   = $translated && $sections['keys'][$key] != $oldref['keys'][$key];
						}
						else
						{
							$string = $refsections['keys'][$key];
							$translated = isset($sections['keys'][$key]);
							$modified   = $translated && $sections['keys'][$key] != $refsections['keys'][$key];
						}

						$status     = $modified
							? 'translated'
							: ($translated
								? 'unchanged'
								: 'untranslated');
						$default    = $translated
							? $sections['keys'][$key]
							: '';

						$field->addAttribute('istranslation', $istranslation);
						$field->addAttribute('istextchange', 0);
						$field->addAttribute('isextraindev', 0);

						if ($have_develop == '1' && in_array($key, $developdata['text_changes']['keys']))
						{
							$change     = $developdata['text_changes']['diff'][$key];
							$sourcetext = $developdata['text_changes']['ref'][$key];
							$targettext = $developdata['text_changes']['ref_in_dev'][$key];

							$label   = '<b>'
								. $key
								. '</b><br /><p class="text_changes">'
								. $change
								. '</p>';

							$field->attributes()->istextchange = 1;
							$field->addAttribute('changestatus', $revisedchanges[$key]);
							$field->addAttribute('sourcetext', $sourcetext);
							$field->addAttribute('targettext', $targettext);
						}
						elseif ($have_develop == '1' && in_array($key, $developdata['extra_keys']['keys']))
						{
							$label   = '<span class="new_word"><b>['
								. JText::_('COM_LOCALISE_NEW_KEY_IN_DEVELOP')
								. ']</b> </span><b>'
								. $key
								. '</b><br />'
								. htmlspecialchars($string, ENT_COMPAT, 'UTF-8');

							$field->attributes()->isextraindev = 1;
						}
						else
						{
							$label   = '<b>'
								. $key
								. '</b><br />'
								. htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
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
					elseif (!preg_match('/^(|(\[[^\]]*\])|([A-Z][A-Z0-9_\*\-\.]*\s*=(\s*(("[^"]*")|(_QQ_)))+))\s*(;.*)?$/', $line))
					{
						$this->item->error[] = $lineNumber;
					}
				}

				$stream->close();
				$newstrings = false;

				if (!empty($sections['keys']))
				{
					foreach ($sections['keys'] as $key => $string)
					{
						if (!isset($refsections['keys'][$key]))
						{
							if (!$newstrings)
							{
								$newstrings = true;
								$form->load($addform, false);
								$section = 'COM_LOCALISE_TEXT_TRANSLATION_NOTINREFERENCE';
								$addform = new SimpleXMLElement('<form />');
								$group   = $addform->addChild('fields');
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
		$client     = $this->getState('translation.client');
		$tag        = $this->getState('translation.tag');
		$reftag     = $this->getState('translation.reference');
		$path       = $this->getState('translation.path');
		$refpath    = $this->getState('translation.refpath');
		$devpath    = LocaliseHelper::searchDevpath($client, $refpath);
		$custompath = LocaliseHelper::searchCustompath($client, $refpath);
		$exists     = JFile::exists($path);
		$refexists  = JFile::exists($refpath);

		if ($refexists && !empty($devpath))
		{
			if ($reftag == 'en-GB' && $tag == 'en-GB' && !empty($custompath))
			{
				$params             = JComponentHelper::getParams('com_localise');
				$customisedref      = $params->get('customisedref', '0');
				$custom_short_path  = '../media/com_localise/customisedref/github/'
							. $client
							. '/'
							. $customisedref;

				// The saved file is not using the core language folders.
				$path   = $custompath;
				$exists = JFile::exists($path);

				$ref_file         = basename($refpath);
				$custom_file_path = JFolder::makeSafe("$custompath/$ref_file");
			}
			elseif ($reftag == 'en-GB' &&  $tag != 'en-GB')
			{
				// It is a translation with the file in develop as reference.
				$refpath = $devpath;
			}
		}

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
				$this->setState('translation.complete', 1);
				$contents2 .= "; @note        Complete\n";
			}
			else
			{
				$this->setState('translation.complete', 0);
			}

			$contents2 .= "; @note        Client " . ucfirst($client) . "\n";
			$contents2 .= "; @note        All ini files need to be saved as UTF-8\n\n";

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
			// Mounting the language file in this way will help to avoid save files with errors at the content.

				// Blank lines
				if (preg_match('/^\s*$/', $line))
				{
					$contents[] = "\n";
				}
				// Comments lines
				elseif (preg_match('/^(;.*)$/', $line, $matches))
				{
					$contents[] = $matches[1] . "\n";
				}
				// Section lines
				elseif (preg_match('/^\[([^\]]*)\]\s*$/', $line, $matches))
				{
					$contents[] = "[" . $matches[1] . "]\n";
				}
				// Key lines
				elseif (preg_match('/^([A-Z][A-Z0-9_\*\-\.]*)\s*=/', $line, $matches))
				{
					$key = $matches[1];

					if (isset($strings[$key]))
					{
						$contents[] = $key . '="' . str_replace('"', '\"', $strings[$key]) . "\"\n";
						unset($strings[$key]);
					}
				}
				// Content with EOL
				elseif (preg_split("/\\r\\n|\\r|\\n/", $line))
				{
					$application = JFactory::getApplication();
					$application->enqueueMessage(JText::sprintf('COM_LOCALISE_WRONG_LINE_CONTENT', htmlspecialchars($line)), 'warning');
				}
				// Wrong lines
				else
				{
					$application = JFactory::getApplication();
					$application->enqueueMessage(JText::sprintf('COM_LOCALISE_WRONG_LINE_CONTENT', htmlspecialchars($line)), 'warning');
				}

				$line = $stream->gets();
			}

			if (!empty($strings))
			{
				$contents[] = "\n[New Strings]\n\n";

				foreach ($strings as $key => $string)
				{
					$contents[] = $key . '="' . str_replace('"', '\"', $string) . "\"\n";
				}
			}

			$stream->close();
			$contents = implode($contents);
			$contents = $contents2 . $contents;
		}

		// Make sure EOL is Unix
		$contents = str_replace(array("\r\n", "\n", "\r"), "\n", $contents);

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
			elseif ($reftag == 'en-GB' && $tag == 'en-GB' && !empty($custompath))
			{
				$params             = JComponentHelper::getParams('com_localise');
				$customisedref      = $params->get('customisedref', '0');
				$custom_short_path  = '../media/com_localise/customisedref/github/'
							. $client
							. '/'
							. $customisedref;

				JFactory::getApplication()->enqueueMessage(
					JText::_('COM_LOCALISE_NOTICE_CUSTOM_EN_GB_FILE_SAVED') . $custom_short_path,
					'notice');
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

			if (!empty($formData['text_changes']))
			{
				$data['text_changes'] = $formData['text_changes'];
				$data['source_text_changes'] = $formData['source_text_changes'];
				$data['target_text_changes'] = $formData['target_text_changes'];

				$changes_data = array();
				$changes_data['client'] = $this->getState('translation.client');
				$changes_data['reftag'] = $this->getState('translation.reference');
				$changes_data['tag'] = $this->getState('translation.tag');
				$changes_data['filename'] = basename($this->getState('translation.refpath'));
$died = '';

				foreach ($data['text_changes'] as $key => $revised)
				{
					$changes_data['revised'] = "0";

					if ($revised == '1' || $revised == 'true')
					{
						$changes_data['revised'] = "1";
					}

					$changes_data['key'] = $key;
					$changes_data['target_text'] = $data['target_text_changes'][$key];
					$changes_data['source_text'] = $data['source_text_changes'][$key];

					LocaliseHelper::updateRevisedvalue($changes_data);
				}
			}
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

		if ($this->getState('translation.complete') == 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_LOCALISE_NOTICE_TRANSLATION_COMPLETE'), 'notice');
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_LOCALISE_NOTICE_TRANSLATION_NOT_COMPLETE'), 'notice');
		}

		return true;
	}
}
