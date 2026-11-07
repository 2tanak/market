<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;


trait MainCreateMethod
{



	public function create(Request $request)
	{
        
		$general = false;
		if ($request->general) {
			$general = $request->general;
		}
		

		return view($this->view_path . '.update', [
			'general' => $general,
			'title' => trans($this->title_path . '.create'),
			
			'ar_bread' => [
				route($this->route_path . '.create') => trans($this->title_path . '.create')
			]
		]);
	}









	public function store(Request $request)
	{
		
		$validator = $this->validateRequest($request);
		if ($validator && $validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

         $action = new $this->action_create(
			new $this->def_model(),
			$request,
			$validator
		);

		try {
           $action->run();
		} catch (\Exception $e) {
			
			return redirect()->back()->with('error', $e->getMessage())->withInput();
		}

	     return redirect()->route($this->route_path . '.index')->with('success', trans('main.created_model'));
	}
}
