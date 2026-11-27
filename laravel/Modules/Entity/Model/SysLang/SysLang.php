<?php

namespace Modules\Entity\Model\SysLang;

use Illuminate\Database\Eloquent\Model;

class SysLang extends Model
{
    protected $table = 'sys_lang';

    protected $fillable = ['sys_key', 'name'];

    const RU = 1;

    const KZ = 2;

    const EN = 3;

    public static function getAr()
    {
        return static::pluck('name', 'sys_key')->toArray();
    }

    public static function getKeyAr()
    {
        return static::pluck('sys_key', 'id')->toArray();
    }
}
