<?php
namespace Modules\Admin\Traits\Api;

use Illuminate\Http\Request;
use Modules\Entity\ModelParent;
use RealRashid\SweetAlert\Facades\Alert;
trait MainDeleteMethod  {
	
	  
	
    public function delete(Request $request, ModelParent $item) {
	
	    try{
        $action = new $this->action_delete($item, $request);
        $action->run();
       
        
		}catch(\Illuminate\Database\QueryException $e){
			
			 
		}
    }
}