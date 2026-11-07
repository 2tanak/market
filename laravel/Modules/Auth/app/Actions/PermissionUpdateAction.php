<?php

namespace Modules\Auth\Actions;

use Modules\Entity\Actions\Base\UpdateAction as BaseUpdateAction;

class PermissionUpdateAction extends BaseUpdateAction
{
    /**
     * Переопределяем и расширяем метод запуска.
     * Обрабатываем привязку «право (модель) ↔ роли».
     */
    public function run()
    {
        parent::run();

        if ($this->request->has('roles')) {
			
            $rolesRaw = $this->request->input('roles', []);
            $roleIds = array_map('intval', (array) $rolesRaw);
			//dd($roleIds);
            $this->model->syncRoles($roleIds);
        }else{
			$this->model->syncRoles([2]);
		}
    }
}
