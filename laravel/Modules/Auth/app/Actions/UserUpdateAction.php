<?php
namespace Modules\Auth\Actions;

use Modules\Entity\Actions\Base\UpdateAction as BaseUpdateAction;

class UserUpdateAction extends BaseUpdateAction
{
    /**
     * Переопределяем и расширяем метод запуска
     */
    public function run()
    {
       parent::run(); 
         if ($this->request->has('roles')) {
            $rolesRaw = $this->request->input('roles', []);
            $roleIds = array_map('intval', (array) $rolesRaw);
            $this->model->syncRoles($roleIds);
          }
    }
}
