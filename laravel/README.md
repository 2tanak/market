1. модель user по не стандартному пути
<br>чтобы система подхватила путь изменил блок в config/auth.php
'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => Modules\Entity\Model\Users\Model::class, 
        ],
<br>чтобы система правильно генерировала Faker в самой модели переопределил метод 
use Modules\Auth\database\factories\UserFactory; 
protected static function newFactory(): UserFactory
    {
        return new UserFactory();
    } 
	
