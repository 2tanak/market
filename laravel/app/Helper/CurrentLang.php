<?php

namespace App\Helper;

use Illuminate\Support\Facades\Request;
use Session;

class CurrentLang
{
    public static function calc_lang()
    {
        $url_get = Request::url();
        $admin = strpos($url_get, 'admin');
        if ($admin) {
            $lang = Request::get('lang');

            if ($lang) {
                return $lang;
            } else {
                return 'ru';
            }
        }
        $lang = \App::getLocale();
        if ($lang) {
            return $lang;
        }

        return session('current_lang');

    }

    public static function url()
    {
        $url_get = Request::url();
        $admin = strpos($url_get, 'administrator');
        if ($admin) {
            $lang = Request::get('lang');

            if ($lang) {
                return $lang;
            } else {
                return 'ru';
            }
        }
        $lang = \App::getLocale();
        if ($lang) {
            return $lang;
        }
        // return session('current_lang');

    }

    public static function get()
    {
        $lang = 'ru';
        /*
          if (session('current_lang')){
              $lang = session('current_lang');
          }
          */
        if (! empty(app()->getLocale())) {
            $lang = app()->getLocale();
        }

        return $lang;
    }

    public static function set($lang)
    {

        // if (!in_array($lang, array_flip(self::getAr())))
        // return 'ru';

        \App::setLocale($lang);
        session(['current_lang' => $lang]);
        session()->save();

        return $lang;
    }

    public static function getAr()
    {
        return [
            'en' => 'English',
            'ru' => 'Русский',

        ];
    }
}
