<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Admin\Traits\MainCrudMethod;
use Modules\Blog\Model\Article\Model;
use Modules\Blog\Validations\BlogValidation;
use Modules\Entity\Actions\Base\DeleteAction as ModelDeleteAction;
use Modules\Entity\Actions\Base\UpdateAction as ModelCreateAction;
use Modules\Entity\Actions\Base\UpdateAction as ModelUpdateAction;

class StartController extends Controller
{
    use MainCrudMethod;

    protected $informers = true;

    protected $view_path = 'blog::admin';

    protected $route_path = 'blog';

    protected $title_path = 'title.blog';

    protected $def_model = Model::class;

    protected $action_create = ModelCreateAction::class;

    protected $action_update = ModelUpdateAction::class;

    protected $action_delete = ModelDeleteAction::class;

    protected $validator_class = BlogValidation::class;
}
