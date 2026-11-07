<?php
namespace Modules\Admin\Traits\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Admin\App\Http\Resources\User\Resource;

trait MainListMethod  {
	
	 public function index(Request $request) {
      //$result = $this->def_model::filter($request)->latest()->paginate(20);oldest()
    $result = $this->def_model::filter($request)->oldest()->paginate(20);
    $sections = collect($result->items())
    ->groupBy(fn($item) => $item->created_at->format('d.m.Y'))
    ->map(fn($items, $date) => [
        'title' => $date,
        'data'  => $items->transform(function($item) {
            $data = $item->toArray(); // Превращаем в массив
            //$data['name'] = $item->id.': '.$item->name;
            $data['created_at'] = $item->created_at->format('d.m.Y');
            return $data;
        })->values(),
    ])
    ->values();

//dd($sections->toArray());
return response()->json(['data' => $sections, 'pagination' => [
        'current_page' => $result->currentPage(),
        'has_more'     => $result->hasMorePages(),
    ]]);
  

	 }

    public function test(Request $request) {
		
		$proxy = \Request::create('/api/user', 'get', ['user_id'=>$request->user_id]);
        $resp = json_decode(app()->handle($proxy)->getContent());
		$response =[];
	    if((int) $resp->id === (int) $request->user_id && $resp->id != 1 ){
		
			$items = $this->def_model::filter($request)->get();
			$response['items'] = $items->count() > 0 ? $items : [];
			$response['user_id'] = $resp->id;
			if(isset($request->kart_atribute)){
			  foreach($request->kart_atribute as $atribute){
				$proxy = \Request::create('/api/admin/'.$atribute.'/proxy', 'get', ['user_id'=>$request->user_id]);
				$attr = json_decode(app()->handle($proxy)->getContent());
				$response[$atribute] = $attr;
			  }
			 }
           }
		return $response;
	  }
	  
	  
		public function proxy(Request $request) {
		return $this->def_model::filter($request)->get();
	    }
	
	
	
}







