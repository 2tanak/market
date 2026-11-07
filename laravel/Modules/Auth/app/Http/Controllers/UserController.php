<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Routing\Controller;
use Modules\Admin\Traits\MainCrudMethod;
use Modules\Auth\Actions\UserUpdateAction as ModelCreateAction;
use Modules\Auth\Actions\UserUpdateAction as ModelUpdateAction;
use Modules\Entity\Actions\MainSaveAction as ModelSaveLangAction;
use Modules\Entity\Actions\Base\DeleteAction as ModelDeleteAction;
use Illuminate\Validation\Rule;
use Modules\Auth\Model\Users\Model;
use Illuminate\Support\Facades\Validator;




class UserController extends Controller
{
	use MainCrudMethod;

	protected $informers = true;
	protected $view_path = 'auth::user';
	protected $route_path = 'user';
	protected $title_path = 'title.user';
	protected $def_model = Model::class;
	protected $action_create = ModelCreateAction::class;
	protected $action_update = ModelUpdateAction::class;
	protected $action_delete = ModelDeleteAction::class;
	protected $validator_class = \Modules\Auth\Validations\UserValidation::class;
}
