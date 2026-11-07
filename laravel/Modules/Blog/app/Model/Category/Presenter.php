<?php 
namespace Modules\Entity\Model\Category;

use Cache;
use Lang;

trait Presenter {
	
function the_excerpt($text){
	  $text = preg_replace("~<img(.*)>~siU","",$text);
		return substr(strip_tags($text),0,150);
    }
	
	function getGalleryAttribute(){
		
		if(@unserialize($this->gallery_img)){
			return unserialize($this->gallery_img);
		}else{
			return $this->gallery_img;
		}
	 
	}
	
	 function getNameAttribute($v){
		 
		return $this->getTransField('name', $v);
    }
	 function getDescriptionAttribute($v){
		 
		return $this->getTransField('description', $v);
    }
}

