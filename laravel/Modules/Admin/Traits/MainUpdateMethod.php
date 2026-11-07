<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;
use Modules\Entity\ModelParent;
use Illuminate\Database\Eloquent\Model;

trait MainUpdateMethod
{

	public function edit(Request $request, Model $item)
	{

		$general = false;

		if ($request->general) {
			$general = $request->general;
		}
       $title = trans($this->title_path . '.update');



		return view($this->view_path . '.update', [
			'general' => $general,
			'title' => $title,
			'ar_bread' => [

				route($this->route_path . '.edit', $item) => trans($this->title_path . '.edit'),

			],
			'model' => $item
		]);
	}
	public function update(Request $request, Model $item)
	{
		//dd($request->all());
		$validator = $this->validateRequest($request, $item->id);

		if ($validator && $validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}
      

		$model = $item;
		$action = new $this->action_update(
			$model,
			$request,
			$validator
		);

		try {
			$action->run();
		} catch (\Exception $e) {
			
			return redirect()->back()->with('error', $e->getMessage());
		}







		return redirect(route($this->route_path . '.edit', $item) . '?lang=' . $request->lang)->with('success', trans('main.updated_model'));
	}
}
