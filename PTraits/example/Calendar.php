<?php
namespace PTraits\example;

class Calendar {
	
	protected $stamp;

	private $traits;

	public function __construct($stamp = null) {
		$this->stamp = (!is_null($stamp)) ? $stamp : mktime();
	}

	public function __call($method, $arguments) {
		array_unshift($arguments, $this);
		return $this->traits->$method($arguments);
	}

	public function getStamp() {
		return $this->stamp;
	}

	public function import($traits) {
		$this->traits = new \PTraits\PTraits($traits);
	}

	public function setStamp($stamp) {
		$this->stamp = $stamp;
		return $this;
	}
}
?>