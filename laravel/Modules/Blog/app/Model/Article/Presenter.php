<?php 
namespace Modules\Blog\Model\Article;

use Cache;

trait Presenter {
	
function the_excerpt($text){
	  $text = preg_replace("~<img(.*)>~siU","",$text);
		return substr(strip_tags($text),0,150);
    }
	
	function getTitleAttribute($v){
		
		return $this->getTransField('title', $v);
    }
	function getDescriptionAttribute($v){
		 
		return $this->getTransField('description', $v);
    }
}

