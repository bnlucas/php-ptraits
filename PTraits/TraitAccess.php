<?php
/**
 * Access modifier for trait classes. Extend all trait classes to this.
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

class TraitAccess {

	/**
	 * Calling class.
	 * @access  protected
	 * @var     object
	 */
	protected $instance;

	/**
	 * ReflectionClass instance.
	 * @access  protected
	 * @var     ReflectionClass
	 */
	protected $reflect;
	
	/**
	 * Magic method __call. Calls calling class methods, no matter the access.
	 * @access  public
	 * @param   string $method
	 * @param   array $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters) {
		if (!$this->reflect->hasMethod($method)) {
			throw new LogicException("Method ".$this->reflect->getName()."::".$method." is not defined.");
		}
		$method = $this->reflect->getMethod($method);
		if ($method->isPrivate() || $method->isProtected()) {
			$method->setAccessible(true);
		}
		$i = 0;
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
		return $method->invokeArgs($this->instance, $parameters);
	}

	/**
	 * Magic method __get. Gets calling class property, no matter the access.
	 * @access  public
	 * @param   string $property
	 * @return  mixed
	 */
	public function __get($property) {
		if (!$this->reflect->hasProperty($property)) {
			throw new LogicException("Property ".$this->reflect->getName()."::".$property." is not defined.");
		}
		$property = $this->reflect->getProperty($property);
		if ($property->isPrivate() || $property->isProtected()) {
			$property->setAccessible(true);
		}
		return $property->getValue($this->instance);
	}

	/**
	 * Magic method __set. Sets calling class property, no matter the access.
	 * @access  public
	 * @param   string $property
	 * @param   mixed $value
	 * @return  void
	 */
	public function __set($property, $value) {
		if (!$this->reflect->hasProperty($property)) {
			throw new LogicException("Property ".$this->reflect->getName()."::".$property." is not defined.");
		}
		$property = $this->reflect->getProperty($property);
		if ($property->isPrivate() || $property->isProtected()) {
			$property->setAccessible(true);
		}
		$property->setValue($this->instance, $value);
	}

	/**
	 * Called from \PTraits\PTraits::__call(). Sets reflection class.
	 * @access  public
	 * @param   object $instance
	 * @param   ReflectionMethod $method
	 * @param   array $parameters
	 * @return  mixed
	 */
	public function call($instance, ReflectionMthod $method, $parameters) {
		$this->instance = $instance;
		$this->reflect = new ReflectionClass($instance);
		return $method->invokeArgs($this, $parameters);
	}
}
?>
