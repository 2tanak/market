<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;

use Modules\Auth\Actions\PermissionUpdateAction as ModelCreateAction;

use Modules\Entity\Actions\Base\DeleteAction as ModelDeleteAction;

use Modules\Auth\Model\Permission\Model;

use Modules\Auth\Actions\PermissionUpdateAction as ModelUpdateAction;

use Modules\Admin\Traits\MainCrudMethod;



class PermissionsController extends Controller
{
	use MainCrudMethod;

	protected $informers = true;
	protected $view_path = 'auth::permission';
	protected $route_path = 'permission';
	protected $title_path = 'title.user';
	protected $def_model = Model::class;
	protected $action_create = ModelCreateAction::class;

	protected $action_update = ModelUpdateAction::class;
	protected $action_delete = ModelDeleteAction::class;
	protected $validator_class = \Modules\Auth\Validations\PermissionValidation::class;
}
