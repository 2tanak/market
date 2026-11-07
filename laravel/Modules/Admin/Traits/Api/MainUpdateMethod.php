<?php
namespace Modules\Admin\Traits\Api;

use Illuminate\Http\Request;
use Modules\Entity\ModelParent;
use Illuminate\Support\Facades\Validator;


trait MainUpdateMethod  {
    public function update(Request $request, ModelParent $item) {
	$general=false;
	//dd($item->slider[0]->name);
	if($request->general){
		 $general = $request->general;
	 }
		
		
	if($request->lang == 'ru' || !isset($request->lang)){
            \App::setLocale('ru');
          }

	
        $title = trans($this->title_path.'.update');
		//echo trans($this->title_path.'');exit(); 
	
        return view($this->view_path.'.update', [
		    'general'=>$general,
            'title' => $title,
            'ar_bread' => [
                //route($this->route_path) => trans($this->title_path.''),
				   route($this->route_path) => trans($this->title_path.'.index'),
				  
            ],
            'model' => $item
        ]);
    }
 public function saveUpdate2(Request $request)
	{
		
		$proxy = \Request::create('/api/user', 'get', ['aaa'=>'b']);
        $resp = json_decode(app()->handle($proxy)->getContent());
		if($resp->id === $request->user_id && $resp->id != 1 ){
			
		$question= new $this->def_model();
         $question->updateOrCreate([
		 'banner_id'=>$request->id
		 ],['status'=>$request->status,'banner_id'=>$request->banner_id,'user_id'=>$request->user_id]);
		 return $resp->id;
		}else{
			return false;
		}
		
		
	}
    public function saveUpdate(Request $request, ModelParent $item) {
  
	   
	 
		try{
			$validator = $this->validator($request->all(),$item);
			if ($validator->fails()) {
				
             Alert::error('Ошибки валидации', 'Проверьте поля');				
		     return redirect()->back()->withErrors($validator);
            };
		
		}
		 catch (\Exception $e) {
		return redirect()->back()->with('error', $e->getMessage());

		}

	   

	

   
     if ($request->lang && $request->lang != 'ru'){
	
            $model = $item->relTrans()->firstOrCreate(['lang'=>$request->lang]);
        }
        else {
            $model = $item;
		}
	
    
        $action = new $this->action_update($model, $request);
        
        try {
            $action->run();
        } catch (\Exception $e) {
			
            return redirect()->back()->with('error', $e->getMessage());
        }
		
		
		if($this->route_path == 'admin_content_manager'){
			 return redirect()->route($this->route_path)->with('success', trans('main.updated_model'));
		}
	   Alert::success('success', 'Успешно обновили');
        if($request->lang){

        return redirect()->route($this->route_path.'_update', $item->id.'?lang='.$request->lang)->with('success', trans('main.updated_model'));
		
		}else{
		
		  return redirect()->route($this->route_path.'_update', $item)->with('success', trans('main.updated_model'));
		}
    }
}