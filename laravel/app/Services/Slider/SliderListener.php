<?php

namespace App\Services\Slider;

use Illuminate\Http\Request;
use App\Services\Slider\Slider; 

class SliderListener
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($event): void
    {
        $model = $event->model;
		
        if ($this->request->has('existing_slider_images') || $this->request->has('slider_images')) {
         Slider::save($model);
        }
    }
}
