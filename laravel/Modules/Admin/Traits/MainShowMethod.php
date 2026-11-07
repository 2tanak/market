<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;
use Modules\Entity\ModelParent;

trait MainShowMethod
{
    public function show(Request $request, ModelParent $item)
    {
        dd(13000000);

        /*
		if($request->lang != 'ru'){
	   $item->setLocale($request->lang);
	}

          if($request->lang){
		  $item->setLocale($request->lang);
          }
           
	
	
	
		   
		   
        $title = trans($this->title_path.'_show');
   
       

        return view($this->view_path.'.show', [
            'title' => $title,
            'model' => $item,
            'ar_bread' => [
                route($this->route_path) => trans($this->title_path.'')
            ]
        ]);
		*/
    }
}
