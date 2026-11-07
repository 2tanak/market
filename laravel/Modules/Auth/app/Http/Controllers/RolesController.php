<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Routing\Controller;
use Modules\Entity\Actions\Base\UpdateAction as ModelCreateAction;
use Modules\Entity\Actions\Base\UpdateAction as ModelUpdateAction;
use Modules\Entity\Actions\Base\DeleteAction as ModelDeleteAction;
use Illuminate\Validation\Rule;
use Modules\Auth\Model\Role\Model;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Traits\MainCrudMethod;



class RolesController extends Controller
{
	use MainCrudMethod;
	
	protected $informers = true;
	protected $view_path = 'auth::role';
	protected $route_path = 'role';
	protected $title_path = 'title.user';
	protected $def_model = Model::class;
	protected $action_create = ModelCreateAction::class;
	protected $action_update = ModelUpdateAction::class;
	protected $action_delete = ModelDeleteAction::class;
	protected $validator_class = \Modules\Auth\Validations\RoleValidation::class;

 }
