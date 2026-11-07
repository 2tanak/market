<?php
namespace Modules\Entity\Actions\Gid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\UploadPhoto;

class GidSaveAction {
    private $model = false;
    private $request = false;

    function __construct(Model $model, Request $request){
        $this->model = $model; 
        $this->request = $request; 
    }

    function run(){
        $this->saveMain();
		$this->saveInforms();
        //$this->saveRequirement();
    }

    private function saveMain(){
	
        $ar = $this->request->all();
		
        $ar['user_id'] = $this->request->user()->id;

       if ($this->request->has('photo')){
		   /*
		   if(!is_dir($dir)) {
             mkdir($dir, 0777, true);
             }
			 */
			
            $ar['photo'] = UploadPhoto::upload($this->request->photo);
	   }
        else 
            unset($ar['photo']);
        $this->model->fill($ar);
        $this->model->save();
    }

    private function saveInforms(){
        $this->model->relInforms->updateOrCreate(['gid_id' => $this->model->id], $this->request->data);
    }


 



}