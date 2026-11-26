<?php
namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Traits\MainCrudMethod;

use Modules\Entity\Actions\Base\UpdateAction as ModelCreateAction;
use Modules\Entity\Actions\Base\UpdateAction as ModelUpdateAction;
use Modules\Entity\Actions\MainSaveAction as ModelSaveLangAction;
use Modules\Entity\Actions\Base\DeleteAction as ModelDeleteAction;

use Modules\Blog\Model\Article\Model as Model;
use Modules\Admin\App\Http\Requests\RoleRequest;


class StartController extends Controller {
    use MainCrudMethod;
	protected $informers = true;
    protected $view_path = 'blog::admin';
    protected $route_path = 'blog';
    protected $title_path = 'title.blog';
    protected $def_model = Model::class;
    protected $action_create = ModelCreateAction::class;
    protected $action_update = ModelUpdateAction::class;
    protected $action_delete = ModelDeleteAction::class;
	protected $validator_class = \Modules\Blog\Validations\BlogValidation::class;
	
}
