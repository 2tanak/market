<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait MainListMethod
{



	public function index(Request $request)
	{
// Добавьте обратный слэш перед Throwable, чтобы обратиться к глобальному классу PHP


	
		$items = $this->def_model::filter($request)->latest()->paginate(env('PAGINATION_LIMIT', 5));

		$title = trans($this->title_path . '.index');
		return view($this->view_path . '.index', [
			'title' => $title,
			'items' => $items
		]);
	}
}
