<?php

namespace Modules\Auth\Model\Role;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Entity\Contracts\UniversalModelInterface;
use Modules\Entity\Traits\UniversalLogic;
use Spatie\Permission\Models\Role as SpatieRole;
use Modules\Entity\Traits\FilterModel;

class Model extends SpatieRole
{
	use UniversalLogic, FilterModel, Presenter;
	
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
