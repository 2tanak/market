<?php
namespace Modules\Entity\Model\SysLang;

use Illuminate\Database\Eloquent\Model;

class SysLang extends Model {
    protected $table = 'sys_lang';
    protected $fillable = [ 'sys_key', 'name'];

    CONST RU = 1;
    CONST KZ = 2; 
    CONST EN = 3; 

    static function getAr(){
        return static::pluck('name', 'sys_key')->toArray();
    }

    static function getKeyAr(){
        return static::pluck('sys_key', 'id')->toArray();
    }
}
