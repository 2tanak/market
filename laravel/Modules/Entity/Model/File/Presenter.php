<?php 
namespace Modules\Entity\Model\File;

use Cache;
use Modules\Entity\Model\LibCity\Model as City;
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
	function getCityAr(){
		return City::pluck('name', 'id')->toArray();
    }
	 function getNameAttribute($v){
		 
		return $this->getTransField('name', $v);
    }
	 function getTextAttribute($v){
		 
		return $this->getTransField('text', $v);
    }
}

