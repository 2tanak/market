<?php
namespace Modules\Blog\Model\Article;
use Modules\Entity\ModelParent;
use Modules\Entity\Model\File\Model as File;


class Model extends ModelParent
{
	use Presenter;
    protected $table = 'articles';
    
    protected $fillable = ['title', 'description', 'publish'];

    

   function files()
    {
	  return $this->morphMany(\Modules\Entity\Model\File\Model::class, 'fileable');
     
    }


   
    public function transable(): MorphTo
    {
        
        return $this->morphTo();
    }
}