<?php
namespace PTraits;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

class PTraits {
	
	protected $traits = array();

	protected $methods = array();

	public function __construct($traits = null) {
		$traits = (!is_array($traits)) ? array($traits) : $traits;
		foreach ($traits as $trait) {
			$this->load($trait);
		}
	}

	public static function autoload($class) {
		$this_class = str_replace(__NAMESPACE__."\\", "", __CLASS__);
		$base_directory = __DIR__;
		if (substr($base_directory, -(strlen($this_class))) === $this_class) {
			$base_directory = substr($base_directory, 0, -(strlen($this_class)));
		}
		$class = ltrim($class, "\\");
		$filename = $base_directory;
		$namespace = "";
		if ($last_namespace_pos = strripos($class, "\\")) {
			$namespace = substr($class, 0, $last_namespace_pos);
			$class = substr($class, $last_namespace_pos + 1);
			$filename .= str_replace("\\", DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
		}
		$filename .= str_replace("_", DIRECTORY_SEPARATOR, $class).".php";
		if (file_exists($filename)) {
			require $filename;
		}
	}

	public static function register() {
		spl_autoload_register(__NAMESPACE__."\\PTraits::autoload");
	}

	public function __call($method, $parameters) {
		$parameters = $parameters[0];
		if ($search = $this->searchMethods($method)) {
			$method = $search->reflect->getMethod($method);
			if (count($parameters) != $method->getNumberOfRequiredParameters()) {
				$error = "Method '".$method->getShortName()."' requires ".$method->getNumberOfRequiredParameters()." parameters. Supplied ";
				$error .= count($parameters).".\n\n".ReflectionMethod::export($search->reflect->getName(), $method->getName(), true);
				throw new InvalidArgumentException($error);
			}
			return $method->invokeArgs($search->trait, $parameters);
		} else {
			throw new LogicException("Method '".$method."' was never loaded.");
		}
	}

	public function load($trait) {
		if (class_exists($trait)) {
			$reflect = new ReflectionClass($trait);
			if ($reflect->hasMethod("__construct")) {
				$error = "Trait class '".$trait."' cannot be instantiated.\n\nThere should be no constructor in a trait class.\n\n";
				$error .= ReflectionMethod::export($trait, "__construct", true));
				throw new LogicException($error);
			}
			$name = $reflect->getShortName();
			$this->traits[$name] = new $trait;
			foreach ($reflect->getMethods() as $method) {
				if ($this->searchMethods($method->getShortName())) {
					throw new LogicException("Cannot redeclare method '".$method->getShortName()."' from '".$reflect->getName()."' trait class.");
				}
				$this->methods[] = (object) array(
					"reflect"	=> $reflect,
					"trait"		=> $this->traits[$name],
					"method"	=> $method->getShortName()
				);
			}
		} else {
			throw new LogicException("Trait class '".$trait."' could not be loaded.");
		}
	}

	public function getMethods() {
		$out = array();
		foreach ($this->methods as $method) {
			$out[] = array(
				"trait"		=> $method->reflect->getName(),
				"method"	=> $method->method
			);
		}
		return $out;
	}

	public function getTraits() {
		$out = array();
		foreach ($this->traits as $trait => $reflect) {
			$out[] = $trait;
		}
		return $out;
	}

	private function searchMethods($method) {
		foreach ($this->methods as $_method) {
			if ($_method->method == $method) {
				return $_method;
			}
		}
		return false;
	}
}