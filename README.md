#PTraits Useage
-----------------------------------------------------


###/path/to/App/App.php
-----------------------------------------------------
```php
namespace App;
use PTraits;

class App {
	
	protected $traits;

	public function __call($method, $parameters) {
		array_unshift($arguments, $this);
		return $this->traits->$method($parameters);
	}

	public function import($traits) {
		if ($this->traits instanceof \PTraits\PTraits) {
			$this->traits->import($traits);
		} else {
			$this->traits = new \PTraits\PTraits($traits);
		}
	}

	public function sayGoodMorning($name) {
		return "Good morning, ".$name.".";
	}

	public function setGoodnight($name) {
		return "Goodnight, ".$name.".";
	}
}
```
-----------------------------------------------------


###/path/to/App/Traits/HelloGoodbye.php
-----------------------------------------------------
```php
namespace App/Traits;
use PTraits;

/**
 * Please note that trait classes cannot have
 * constructor methods. PTraits will throw a
 * LogicException if one is found.
 */
class HelloGoodbye extends \PTraits\TraitAccess {
	
	public function sayHello($name) {
		return "Hello, ".$name.".";
	}

	public function sayGoodbye($name) {
		return "Goodbye, ".$name.".";
	}
}
```
-----------------------------------------------------


###/path/to/App/index.php
-----------------------------------------------------
```php
require_once($_SERVER['DOCUMENT_ROOT']."/path/to/Utilities/Loader.php");
Loader::register();

$app = new \App\App();
$app->import("\App\Traits\HelloGoodbye");

echo $app->sayHello("world");		// Hello, world.
echo $app->sayGoodMorning("user");	// Good morning, user.
echo $app->sayGoodnight("world");	// Goodnight, world.
echo $app->sayGoodbye("user");		// Good bye, user.
```