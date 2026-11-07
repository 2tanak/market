<?php

namespace Modules\Auth\Model\Permission;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Entity\Contracts\UniversalModelInterface;
use Modules\Entity\Traits\UniversalLogic;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Modules\Entity\Traits\FilterModel;

class Model extends SpatiePermission
{
	use UniversalLogic, FilterModel;
	
	  protected function filterClass()
     {
        return Filter::class;
     }
	
	
	
    protected $fillable = [
        'guard_name',
		'name',
        'slug', 
    ];

 }
