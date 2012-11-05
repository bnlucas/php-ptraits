<?php
namespace PTraits\example\Traits;

class Dates {
	
	public function addDays($_this, $days) {
		$_this->setStamp(strtotime("+".$days." days", $_this->getStamp()));
	}

	public function addWeeks($_this, $weeks) {
		$_this->setStamp(strtotime("+".$weeks." weeks", $_this->getStamp()));
	}
}
?>