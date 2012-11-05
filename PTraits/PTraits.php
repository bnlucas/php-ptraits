<?php
/**
 * PTraits is a way to emulate traits PHP < 5.4.0.
 *
 * @author      Nathan Lucas <nathan@plainwreck.com>
 * @copyright   2012 Nathan Lucas
 * @link        http://github.com/bnlucas/PTraits
 * @license     http://githut.com/bnlucas/PTraits
 * @example     http://githut.com/bnlucas/PTraits
 * @version     1.0.0
 * @package     PTraits
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace PTraits;

use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

class PTraits {
	
	/**
	 * Trait instances.
	 * @access  protected
	 * @var     array
	 */
	protected $traits = array();

	/**
	 * Trait methods.
	 * @access  protected
	 * @var     array
	 */
	protected $methods = array();

	/**
	 * Constructor. Loads trait classes.
	 * @access  public
	 * @param   string|array $traits
	 * @return  void
	 */
	public function __construct($traits) {
		$this->import($traits);
	}

	/**
	 * Magic method __call. Checks if method has been loaded and calls it.
	 * @access  public
	 * @param   string $method
	 * @param   array $parameters
	 * @return  mixed
	 */
	public function __call($method, array $parameters) {
		$parameters = $parameters[0];
		if ($search = $this->searchMethods($method)) {
			$method = $search->reflect->getMethod($method);
			$i = 0;
			if (count($parameters) == ($method->getNumberOfParameters() + 1)) {
				array_shift($parameters);
			}
			foreach ($method->getParameters() as $parameter) {
				if (isset($parameters[$i])) {
					continue;
				}
				if (!$parameter->isOptional()) {
					throw new InvalidArgumentException("$".$parameter->getName()." is a required parameter in method '".$method->getName()."'.\n\n".ReflectionMethod::export($search->reflect->getName(), $method->getName(), true));
				} else {
					$parameters[$i] = $parameter->getDefaultValue();
				}
				$i++;
			}
			return $method->invokeArgs($search->trait, $parameters);
		} else {
			throw new LogicException("Method '".$method."' was never loaded.");
		}
	}

	/**
	 * Returns list of loaded methods.
	 * @access  public
	 * @return  array
	 */
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

	/**
	 * Returns list of loaded traits.
	 * @access  public
	 * @return  array
	 */
	public function getTraits() {
		$out = array();
		foreach ($this->traits as $trait => $class) {
			$out[] = get_class($class);
		}
		return $out;
	}

	/**
	 * Imports more traits to the PTraits instance.
	 * @access  public
	 * @param   string|array $traits
	 * @return  void
	 */
	public function import($traits) {
		$traits = (!is_array($traits)) ? array($traits) : $traits;
		foreach ($traits as $trait) {
			$this->load($trait);
		}
	}

	/**
	 * Load given trait class. Loads methods and checks if method has already been declared.
	 * @access  protected
	 * @param   string $trait
	 * @return  void
	 */
	protected function load($trait) {
		if (class_exists($trait)) {
			$reflect = new ReflectionClass($trait);
			if ($reflect->hasMethod("__construct")) {
				$error = "Trait class '".$trait."' has a constructor.\n\nTrait classes cannot be instantiated.";
				$error .= "\n\n".ReflectionMethod::export($trait, "__construct", true);
				throw new LogicException($error);
			}
			$name = $reflect->getShortName();
			$this->traits[$name] = new $trait;
			foreach ($reflect->getMethods() as $method) {
				if ($this->searchMethods($method->getShortName())) {
					throw new LogicException("Cannot redeclare method '".$method->getName()."' from '".$reflect->getName()."' trait class.");
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

	/**
	 * Searches for method in loaded traits.
	 * @access  private
	 * @param   string $method
	 * @return  boolean|object
	 */
	private function searchMethods($method) {
		foreach ($this->methods as $_method) {
			if ($_method->method == $method) {
				return $_method;
			}
		}
		return false;
	}
}
?>