<?php

namespace Modules\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

trait MainDeleteMethod
{
    public function destroy(Request $request, Model $item)
    {

        try {
            $action = new $this->action_delete($item, $request);
            $action->run();

            return redirect()->route($this->route_path.'.index')->with('success', trans('Success delete'));
        } catch (QueryException $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
