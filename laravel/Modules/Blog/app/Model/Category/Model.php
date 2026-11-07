<?php
<?php

namespace Modules\Entity\Model\Category;

use Modules\Entity\ModelParent;



class Model extends ModelParent
{
    
    public $timestamps = false;

    protected $table = 'categories';
    
    protected $fillable = [
        'name', 
        'description'
    ];

   function getNameSpace()
    {
        return __NAMESPACE__;
    }
}
