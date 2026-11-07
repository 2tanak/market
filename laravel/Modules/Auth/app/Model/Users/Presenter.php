<?php 
namespace Modules\Auth\Model\Users;

use Cache;
use Modules\Entity\Model\LibCity\Model as City;
use Lang;

trait Presenter {
	
function the_excerpt($text){
	  $text = preg_replace("~<img(.*)>~siU","",$text);
		return substr(strip_tags($text),0,150);
    }
	
	
	 
	 function getTextAttribute($v){
		 
		return $this->getTransField('text', $v);
    }
}

