<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;
use View;
use Route;
use Modules\Entity\Model\SysLang\SysLang;

trait MainSystemMethods
{
    public function __construct(Request $request)
    {
        $lang = $request->get('lang');
		app()->setLocale($lang);


		View::share('model', new $this->def_model());
        View::share('view', $this->view_path);
        View::share('route_path', $this->route_path);
        View::share('request', $request);
		View::share('sys_lang', new SysLang());
		View::share('lang', $lang);
    }


    protected function validateRequest($request, $id = null)
    {

        // Если класс валидации не задан в контроллере, пропускаем
        if (!isset($this->validator_class) || !class_exists($this->validator_class)) {
            return null;
        }

        // Создаем экземпляр вашего класса (например, UserValidation)
        $validatorInstance = new $this->validator_class();

        // Получаем правила, передавая данные и ID (для unique)
        $rules = $validatorInstance->rules($request->all(), $id);

        // Проверяем наличие метода messages, если нет — пустой массив
        $messages = method_exists($validatorInstance, 'messages') ? $validatorInstance->messages() : [];

        // Создаем стандартный валидатор Laravel
        return \Illuminate\Support\Facades\Validator::make(
            $request->all(),
            $rules,
            $messages
        );
    }
}
