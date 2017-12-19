# The Compat Package

This is a simple package that contains forward compatibility classes and interfaces that are registered to the global namespace

## JsonSerializable

`JsonSerializable` is a PHP 5.4 interface that allows you to specify what data to serialize to JSON when you `json_encode` an object that implements the interface.

### Usage

Since this is a PHP 5.4 interface, the `jsonSerialize()` method does not get called automatically when `json_encode`-ing an instance of the class when used in 5.3. To work around this, simply call the `jsonSerialize()` method directly when passing it to `json_encode`. This is forward-compatible with PHP 5.4.

Note in some instances of PHP 5.5 from Debian the interface is also missing (see http://stackoverflow.com/questions/18239405/php-fatal-error-call-to-undefined-function-json-decode) which will also require this interface to be included.

```php
class MyClass implements \JsonSerializable
{
	/**
	 * @var  array  Holds the data this class uses.
	 */
	protected $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function jsonSerialize()
	{
		return $this->data;
	}
}

$obj = new MyClass(array('sample', 'data', 'to', 'encode'));

$encoded = json_encode($obj->jsonSerialize());
```

## Installation via Composer

Add `"joomla/compat": "~1.0"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/compat": "~1.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/compat "~1.0"
```
