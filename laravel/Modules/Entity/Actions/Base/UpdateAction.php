<?php

namespace Modules\Entity\Actions\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\UploadPhoto;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;
use App\Services\Avatar\Avatar; 
use App\Services\Slider\Slider;
use App\Services\Editor\EditorImage;

use Route;
use Cache;
use Illuminate\Support\Facades\Storage;

class UpdateAction
{
    protected $model;
    protected $validator;
    protected $request;

    // Убираем Request из конструктора
    function __construct(Model $model, Request $request, $validator)
    {

        $this->model = $model;
        $this->validator = $validator;

        $this->request = $request;
    }


    function run()
    {
        DB::transaction(function () {
			//dd($this->request->all());
            $validated = $this->validator->validated();

          

            if (app()->getLocale() && app()->getLocale() != env('APP_LOCALE')) {

                //dd($this->model->relTrans());
                $this->model->relTrans()->updateOrCreate(
                    [
                        'lang' => $this->request->lang,
                        'transable_id' => $this->model->id
                    ],
                    $this->validator->validated()
                );
            } else {

                $this->saveMain($validated);
            }
            
			
			if ($this->request->has('existing_slider_images') || $this->request->has('slider_images')) {
                Slider::save($this->model);
            }
			
			
		});
				
}
  
    private function saveMain(array $data)
    {
        $this->model->fill($data);
        $this->model->save();
    }


    


   
}
