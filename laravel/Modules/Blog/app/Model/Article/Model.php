<?php
namespace Modules\Blog\Model\Article;
use Modules\Entity\ModelParent;
use Modules\Entity\Model\File\Model as File;
use Modules\Blog\Events\ArticleSaving;

class Model extends ModelParent
{
	use Presenter;
    protected $table = 'articles';
    
    protected $fillable = ['title', 'description', 'publish'];

    protected $dispatchesEvents = [
        'saving' => ArticleSaving::class,
    ];

   function files()
    {
	  return $this->morphMany(\Modules\Entity\Model\File\Model::class, 'fileable');
     
    }


   
    public function transable(): MorphTo
    {
        
        return $this->morphTo();
    }
}