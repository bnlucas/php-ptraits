<?php
namespace PTraits\example\Traits;

class Dates extends \PTraits\TraitAccess {
	
	public function addDays($days) {
		$this->stamp = strtotime("+".$days." days", $this->stamp);
	}

	public function addWeeks($weeks) {
		$this->stamp = strtotime("+".$weeks." weeks", $this->stamp);
	}
}
?>