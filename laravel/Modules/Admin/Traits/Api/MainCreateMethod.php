<?php

namespace Modules\Admin\Traits\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



trait MainCreateMethod
{
	
   
   public function create(Request $request)
	{
      
		$general = false;
		if ($request->general) {
			$general = $request->general;
		}

		return view($this->view_path . '.create', [
			'general' => $general,
			'title' => trans($this->title_path . '.create'),
			'ar_bread' => [
				route($this->route_path . '.create') => trans($this->title_path . '.create')
			]
		]);
	}
	
	
	
	
	
	
	
	

	public function store(Request $request)
	{
       
		
		
		
		$action = new $this->action_create(new $this->def_model(), $request,$validator);

		try {

			$action->run();
		} catch (\Exception $e) {
			//dd(170);
			//Alert::error('Errors', $e->getMessage());
			return redirect()->back()->with('error', $e->getMessage());
		}

		//Alert::success('success', 'Успешно создано');

		return redirect()->route($this->route_path . '.index')->with('success', trans('main.created_model'));
	}

   
   
   
   




}
