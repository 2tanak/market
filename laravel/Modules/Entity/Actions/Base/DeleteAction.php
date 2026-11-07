<?php
namespace Modules\Entity\Actions\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Storage;
class DeleteAction {
    private $model = false;
    private $request = false;

    function __construct(Model $model, Request $request){
		
        $this->model = $model; 
        $this->request = $request; 
    }

    function run(){
		
		
		if(isset($this->model->files()->id)){
	        Storage::disk('public')->delete($this->model->files->small);
			Storage::disk('public')->delete($this->model->files->medium);
			Storage::disk('public')->delete($this->model->files->large);
			$this->model->files()->delete();
		}
		
	
		
		$this->model->delete();
		
		
		
     }

}