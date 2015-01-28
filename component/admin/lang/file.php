<?php
/**
 * @package     Com_Localise
 * @subpackage  Lang
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Language file
 *
 * @since  1.0
 */
class LocaliseLangFile
{
	/**
	 * Word blacklist used to validate lines.
	 *
	 * @var  mixed
	 */
	protected $blackList;

	/**
	 * Contents of the file.
	 *
	 * @var  string
	 */
	protected $contents;

	/**
	 * Errors found.
	 *
	 * @var  array
	 */
	protected $errors = array(
		'global' => array(),
		'lines'  => array()
	);

	/**
	 * Instance of the file to read operations.
	 *
	 * @var  mixed  SplFileObject if fine | null otherwise
	 */
	protected $file;

	/**
	 * Path to the language file.
	 *
	 * @var  string
	 */
	protected $filePath;

	/**
	 * File language strings.
	 *
	 * @var  mixed  Array on parse success | FALSE on error
	 */
	protected $strings;

	/**
	 * Cached instances.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Constructor.
	 *
	 * @param   string  $filePath  Path to the language file
	 *
	 * @since   1.0
	 */
	public function __construct($filePath)
	{
		$this->filePath = trim($filePath);
	}

	/**
	 * Add a global error.
	 *
	 * @param   string  $errorMessage  Description of the error
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	protected function addError($errorMessage)
	{
		array_push($this->errors['global'], $errorMessage);

		return $this;
	}

	/**
	 * Add error found in a line.
	 *
	 * @param   integer  $lineNumber    Line failing
	 * @param   string   $errorMessage  Description of the error
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	protected function addLineError($lineNumber, $errorMessage)
	{
		if (!isset($this->errors['lines'][$lineNumber]))
		{
			$this->errors['lines'][$lineNumber] = array();
		}

		array_push($this->errors['lines'][$lineNumber], $errorMessage);

		return $this;
	}

	/**
	 * Check that file has no errors.
	 *
	 * @return  boolean
	 */
	public function check()
	{
		$file = $this->getFile();

		if (!($file instanceof SplFileObject))
		{
			return false;
		}

		$errors = 0;

		foreach ($file as $lineNumber => $line)
		{
			if (!$this->checkLine($lineNumber, $line))
			{
				++$errors;
			}
		}

		$this->destroyFile();

		return $errors ? false : true;
	}

	/**
	 * Check the format of one line of the file.
	 *
	 * @param   integer  $lineNumber  Number of the line being validated
	 * @param   string   $line        Line contents
	 *
	 * @return  boolean
	 */
	protected function checkLine($lineNumber, $line)
	{
		// Avoid BOM error as BOM is OK when using parse_ini.
		if ($lineNumber == 0)
		{
			$line = str_replace("\xEF\xBB\xBF", '', $line);
		}

		$line = trim($line);

		// Ignore comment lines.
		if (!strlen($line) || $line['0'] == ';')
		{
			return true;
		}

		// Ignore grouping tag lines, like: [group]
		if (preg_match('#^\[[^\]]*\](\s*;.*)?$#', $line))
		{
			return true;
		}

		$realNumber = $lineNumber + 1;

		// Check that string does not begin or end with "_QQ_"
		$checkLine = str_replace('"_QQ_"', 'QQQQQ', $line);

		$lineParts = explode('=', $checkLine, 2);

		if (count($lineParts) != 2)
		{
			$this->addLineError($realNumber, 'Incorrect format');

			return false;
		}

		$string = $lineParts[1];

		if (strlen($string) < 2 || $string[0] != '"' || $string[strlen($string) - 1] != '"')
		{
			$this->addLineError($realNumber, 'Incorrect format');

			return false;
		}

		// Remove the "_QQ_" from the equation
		$line = str_replace('"_QQ_"', '', $line);

		// Check for any incorrect uses of _QQ_.
		if (strpos($line, '_QQ_') !== false)
		{
			$this->addLineError($realNumber, 'Invalid use of _QQ_');

			return false;
		}

		// Check for odd number of double quotes.
		if (substr_count($line, '"') % 2 != 0)
		{
			$this->addLineError($realNumber, 'Odd number of double quotes');

			return false;
		}

		// Check that the line passes the necessary format.
		if (!preg_match('#^[A-Z][A-Z0-9_\-\.]*\s*=\s*".*"(\s*;.*)?$#', $line))
		{
			$this->addLineError($realNumber, 'Incorrect format');

			return false;
		}

		// Check that the key is not in the blacklist.
		$key = strtoupper(trim(substr($line, 0, strpos($line, '='))));

		if (in_array($key, $this->getBlackList()))
		{
			$this->addLineError($realNumber, 'Key in blackList');

			return false;
		}

		return true;
	}

	/**
	 * To avoid unclosed files ensure that the file is destroyed.
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	public function destroyFile()
	{
		$this->file = null;

		return $this;
	}

	/**
	 * Get the blackList.
	 *
	 * @return  array
	 */
	public function getBlackList()
	{
		if (null === $this->blackList)
		{
			$this->loadDefaultBlackList();
		}

		return $this->blackList;
	}

	/**
	 * Get the contents of the file.
	 *
	 * @return  mixed  String on success | FALSE otherwise
	 */
	public function getContents()
	{
		if (null === $this->contents)
		{
			$this->loadContents();
		}

		return $this->contents;
	}

	/**
	 * Get last error encountered.
	 *
	 * @return  string
	 */
	public function getLastError()
	{
		return end($this->errors['global']);
	}

	/**
	 * Get all the errors encountered.
	 *
	 * @return  array
	 */
	public function getErrors()
	{
		return $this->errors['global'];
	}

	/**
	 * Get the lines errors.
	 *
	 * @return  array
	 */
	public function getLinesErrors()
	{
		return $this->errors['lines'];
	}

	/**
	 * Create and return a cached instance.
	 *
	 * @param   string  $filePath  Path to the language file
	 *
	 * @return  LocaliseLangFile
	 */
	public static function getInstance($filePath)
	{
		if (empty(static::$instances[$filePath]))
		{
			static::$instances[$filePath] = new static($filePath);
		}

		return static::$instances[$filePath];
	}

	/**
	 * Get an instance of SplFileObject.
	 * WARNING: Remember to use destroyFile() to avoid file collissions
	 *
	 * @return  mixed  SplFileObject on success | null otherwise
	 */
	public function getFile()
	{
		if (null === $this->file)
		{
			$this->loadFile();
		}

		return $this->file;
	}

	/**
	 * Get a language string.
	 *
	 * @param   string  $key  Key of the language string
	 *
	 * @return  mixed  String on success | null otherwise
	 */
	public function getString($key)
	{
		$strings = $this->getStrings();

		if ($strings && isset($strings[$key]))
		{
			return $strings[$key];
		}

		return null;
	}

	/**
	 * Get the file language strings.
	 *
	 * @param   array  $options  Parsing options
	 *
	 * @return  mixed  Array on success | FALSE otherwise
	 */
	public function getStrings($options = array('process_sections' => false, 'scanner_mode' => INI_SCANNER_NORMAL))
	{
		if (null === $this->strings)
		{
			$this->loadStrings($options);
		}

		return $this->strings;
	}

	/**
	 * Fast check to see if the file is parseable with parse_ini_string.
	 *
	 * @return  boolean
	 */
	public function isParseable()
	{
		if (empty($this->filePath) || !file_exists($this->filePath))
		{
			$this->addError('File not parseable (' . $this->filePath . '): File does not exist');

			return false;
		}

		return true;
	}

	/**
	 * Load the file contents with file_get_contents.
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	protected function loadContents()
	{
		$this->contents = false;

		if (!$this->isParseable())
		{
			return $this;
		}

		$this->contents = file_get_contents($this->filePath);

		return $this;
	}

	/**
	 * Load the default blacklist from Joomla.
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	protected function loadDefaultBlackList()
	{
		$this->blackList = array('YES', 'NO', 'NULL', 'FALSE', 'ON', 'OFF', 'NONE', 'TRUE');

		return $this;
	}

	/**
	 * Method to load an instance of SplFileObject to read the file.
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	protected function loadFile()
	{
		if ($this->isParseable())
		{
			$this->file = new SplFileObject($this->filePath);
		}

		return $this;
	}

	/**
	 * Load the file language strings.
	 *
	 * @param   array  $options  Parsing options
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	protected function loadStrings($options = array('process_sections' => false, 'scanner_mode' => INI_SCANNER_NORMAL))
	{
		$this->strings = false;

		$content = $this->getContents();

		if (false === $content)
		{
			return $this;
		}

		$processSections = isset($options['process_sections']) ? $options['process_sections'] : false;
		$scannerMode     = isset($options['scanner_mode']) ? $options['scanner_mode'] : INI_SCANNER_NORMAL;

		ini_set('track_errors', '1');
		$this->strings = @parse_ini_string($content, $processSections, $scannerMode);

		if (false === $this->strings)
		{
			$error = isset($php_errormsg) ? $php_errormsg : '';

			$this->addError("Error parsing file contents of " . $this->filePath . ": " . $error);
		}

		return $this;
	}

	/**
	 * Method to override the blackList
	 *
	 * @param   array  $blackList  Array with the desired black list words
	 *
	 * @return  LocaliseLangFile  Self instance for chaining
	 */
	public function setBlackList($blackList = array())
	{
		$this->blackList = $blackList;

		return $this;
	}
}
