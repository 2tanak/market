<?php

namespace Modules\Admin\App\Http\Controllers\Users;

use Illuminate\Routing\Controller;
use Modules\Admin\App\Validations\UserValidation;
use Modules\Admin\Traits\MainCrudMethod;
use Modules\Entity\Actions\Users\DeleteAction as ModelDeleteAction;
use Modules\Entity\Actions\Users\UpdateAction as ModelCreateAction;
use Modules\Entity\Actions\Users\UpdateAction as ModelUpdateAction;
use Modules\Entity\Model\Users\Model;

class StartController extends Controller
{
    use MainCrudMethod;

    protected $informers = true;

    protected $view_path = 'admin::page.user';

    protected $route_path = 'user';

    protected $title_path = 'title.user';

    protected $def_model = Model::class;

    protected $action_create = ModelCreateAction::class;

    protected $action_update = ModelUpdateAction::class;

    protected $action_delete = ModelDeleteAction::class;

    protected $validator_class = UserValidation::class;
}
