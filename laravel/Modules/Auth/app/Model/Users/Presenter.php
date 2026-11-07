<?php

namespace Modules\Auth\Model\Users;

use Modules\Auth\Model\Role\Model as Role;

trait Presenter
{

	function the_excerpt($text)
	{
		$text = preg_replace("~<img(.*)>~siU", "", $text);
		return substr(strip_tags($text), 0, 150);
	}

	public function setPhoneAttribute($value)
	{

		$this->attributes['phone'] = preg_replace('/\D/', '', $value);
	}
     

	/*
	
	 function getNameAttribute($v){
		 
		return $this->getTransField('name', $v);
    }
	 function getTextAttribute($v){
		 
		return $this->getTransField('text', $v);
    }
	*/
}
