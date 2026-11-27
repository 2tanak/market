<?php

namespace Modules\Admin\Traits\Api;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Entity\ModelParent;

trait MainDeleteMethod
{
    public function delete(Request $request, ModelParent $item)
    {

        try {
            $action = new $this->action_delete($item, $request);
            $action->run();

        } catch (QueryException $e) {

        }
    }
}
