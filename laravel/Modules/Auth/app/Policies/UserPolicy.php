<?php


namespace Modules\Auth\Policies;

// Импортируем модель пользователя, над которой совершается действие
use Modules\Auth\Model\Users\Model as user;
use Illuminate\Auth\Access\HandlesAuthorization;


class UserPolicy
{
    use HandlesAuthorization;
	
    public function list($currentUser): bool
    {
		
	    return $currentUser->can('view_user');
		
    }

    
    public function view($currentUser, TargetUser $targetUser): bool
    {
		dd(101);
        // Разрешить админу, либо если пользователь смотрит свой собственный профиль
        return $currentUser->hasRole('admin') || $currentUser->id === $targetUser->id;
    }

    /**
     * Создание пользователя (GET /user/create и POST /user -> create/store)
     */
    public function create($currentUser): bool
    {
		 return $currentUser->can('create_user');
    }

    /**
     * Редактирование пользователя (GET /user/{user}/edit и PUT /user/{user} -> edit/update)
     */
    public function update($currentUser,user $user): bool
    {
		
		return $currentUser->can('update_user');
    }

    /**
     * Удаление пользователя (DELETE /user/{user} -> destroy)
     */
    public function delete(user $user): bool
    {
		dd(2);
        // Запрещаем удалять самого себя, даже если это админ
        if ($currentUser->id === $targetUser->id) {
            return false;
        }

        return $currentUser->hasRole('admin') || $currentUser->can('users.delete');
    }
}
