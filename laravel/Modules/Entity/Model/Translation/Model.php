<?php
namespace Modules\Entity\Model\Translation;
use Modules\Entity\ModelParent;
use Modules\Entity\Model\Translation;



class Model extends ModelParent {
    
    protected $table = 'translations';
    protected $fillable = ['name','title','description','lang'];
	
	
	public function transable()
    {
        return $this->morphTo();
    }
	 
}
