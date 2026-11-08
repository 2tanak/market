<?php

namespace Modules\Entity\Actions\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UpdateAction
{
    protected $model;
    protected $validator;
    protected $request;


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
        });
    }

    private function saveMain(array $data)
    {
        $this->model->fill($data);
        $this->model->save();
    }
}
