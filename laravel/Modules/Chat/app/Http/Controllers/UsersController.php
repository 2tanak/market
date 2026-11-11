<?
namespace Modules\Chat\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Auth\Model\Users\Model as Users;
use App\Http\Controllers\Controller;
use Modules\Chat\Resources\UserResource;
use Illuminate\Support\Facades\Redis;
use Modules\Entity\Model\Messages\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Route;

class UsersController extends Controller
{
	
	
public function chat(Users $user,Request $request){
dd(11);
$authUser = auth()->user();
$token = $this->verify_token($authUser);
	
return view('chat::chat',[
  'user' =>$user,
  'auth' => $authUser,
  'token' => $token,
]);
}	
	
public function verify_token($authUser){
	//единство токена

$redisKey = "user:{$authUser->id}:token";


// 1. Берем токен из базы
    $tokenInstance = $authUser->tokens()->where('name', 'web-access')->first();
	$token = null;
	$myId = auth()->id();
    
	
  if ($tokenInstance) {
        // 2. Если токен протух (24 часа прошло)
        if ($tokenInstance->created_at->addHours(24)->isPast()) {
            $tokenInstance->delete(); // Удаляем из БД
           
            Redis::del($redisKey);    // Чистим Редис
			
			// один раз будет создвать новый когда протух
		   $token = $authUser->createToken('web-access')->plainTextToken;
			Redis::setex($redisKey, 86400, $token);
			//нахуй здесь view, код итак не зайдет туда
        } else {
            // Если живой — тянем оригинал из Редиса
            $token = Redis::get($redisKey);
			//dd($token);
			 if (!$token) {
				  $token = $authUser->createToken('web-access')->plainTextToken;
                  Redis::setex($redisKey, 86400, $token);
			 }
        }
    }
   
		if (!$tokenInstance) {
		
        $token = $authUser->createToken('web-access')->plainTextToken;
        Redis::setex($redisKey, 86400, $token);
       }
     return $token;
}
	


public function totalUnread()
{
    $myId = auth()->id();

    // Считаем все сообщения, где получатель Я и статус 2 (не прочитано)
    $count = Model::where('receiver_id', $myId)
                  ->where('status', 2)
                  ->count();

    return response()->json([
        'total_unread' => $count
    ]);
}




}
