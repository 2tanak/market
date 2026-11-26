<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;
use Modules\Entity\ModelParent;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Model;

trait MainDeleteMethod
{


	public function destroy(Request $request, Model $item)
	{

		try {
			$action = new $this->action_delete($item, $request);
			$action->run();

			
			return redirect()->route($this->route_path . '.index')->with('success', trans('Success delete'));
		} catch (\Illuminate\Database\QueryException $e) {
			
			return redirect()->back()->with('error', $e->getMessage());
		}
	}
}
