<?php
/**
 * Loader is a simple autoloader to call with an PHP project.
 * 
 * require_once($_SERVER['DOCUMENT_ROOT']."/path/to/Utilities/Loader.php");
 * \Utilities\Loader::register();
 *
 * @author      Nathan Lucas <nathan@plainwreck.com>
 * @copyright   2012 Nathan Lucas
 * @link        http://www.plainwreck.com/Utilities
 * @license     http://www.plainwreck.com/Utilities
 * @version     1.0.0
 * @package     Utilities
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

namespace Utilities;

class Loader {

	/**
	 * @access public
	 * @static
	 * @var array
	 */
	public static $files = array();

	/**
	 * Format filenames for classes to load. Logs filename in Loader::$files array.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function autoload($class) {
		$_class = str_replace(__NAMESPACE__."\\", "", __CLASS__);
		$base = PHP_BASE;
		if (substr($base, -(strlen($_class))) === $_class) {
			$base = substr($base, 0, -(strlen($_class)));
		}
		$class = ltrim($class, "\\");
		$filename = $base.DIRECTORY_SEPARATOR;
		$namespace = "";
		if ($namespace_pos = strripos($class, "\\")) {
			$namespace = substr($class, 0, $namespace_pos);
			$class = substr($class, $namespace_pos + 1);
			$filename .= str_replace("\\", DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
		}
		$filename .= str_replace("_", DIRECTORY_SEPARATOR, $class).".php";
		if (file_exists($filename)) {
			require_once($filename);
			self::$files[] = $filename;
		}
	}

	/**
	 * Clears and sets SPL autoloader.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function register() {
		spl_autoload_register(null, false);
		spl_autoload_register("\Utilities\Loader::autoload");
	}

	/**
	 * Return log of loaded files.
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	public static function log() {
		return self::$files;
	}
}
?>
