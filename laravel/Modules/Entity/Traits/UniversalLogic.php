<?php

namespace Modules\Entity\Traits;

use App\Helper\CurrentLang;
use Modules\Entity\Model\Translation\Model as Translation;
use Route;

trait UniversalLogic
{
    protected $lang = false;

    public function relTrans()
    {
        return $this->morphMany(Translation::class, 'transable');

    }

    public function initializeUniversalLogic()
    {
        $this->lang = CurrentLang::url();
    }

    public function setLocale($locale = false)
    {
        if (! $locale) {
            $this->lang = 'ru';
        }
        $this->lang = $locale;
    }

    protected function getTransField($field, $v)
    {

        $lang = app()->getLocale();

        $route = Route::currentRouteName();

        $ar = explode('.', $route);

        if (in_array('create', $ar)) {
            return $v;
        }

        if ($lang === env('APP_LOCALE')) {
            return $v;
        }

        if ($this->lang == '' || $this->lang == 'ru') {
            return $v;
        }

        // $lang = Cache::get($lang.$this->getNameSpace());

        $translation = $this->relTrans()->where('lang', $lang)->first();

        if (! isset($translation->{$field})) {

            return $v;
        }

        return $translation->{$field};
    }

    public function getNameSpace()
    {

        return __NAMESPACE__;
    }

    public function getTransTableNameAttribute()
    {
        return $this->getTable();
    }
}
