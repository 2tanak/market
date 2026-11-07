<?php

namespace Modules\Entity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Model\Users\Model as User;
use Modules\Entity\Model\SysLang\SysLang;

class LanguageSeeder extends Seeder
{
   public function run(): void
    {
     SysLang::updateOrCreate(
       ['name' => 'Русский', 
	   'sys_key' => 'ru'],
	  );
	 SysLang::updateOrCreate(
       ['name' => 'Английский', 
	   'sys_key' => 'en']
	   );
    }
}
