<?php

namespace Modules\Auth\Model\Role;

use Modules\Auth\Model\Role\Model as Role;

trait Presenter
{

	function the_excerpt($text)
	{
		$text = preg_replace("~<img(.*)>~siU", "", $text);
		return substr(strip_tags($text), 0, 150);
	}

	
   public static function getRolesAr(): array
    {
        // Твоя логика, которая возвращает массив [id => slug]
        return self::pluck('name', 'id')->toArray();
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
