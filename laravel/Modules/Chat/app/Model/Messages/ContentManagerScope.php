<?php

namespace Modules\Chat\Model\Messages;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;
use RoleService;

class ContentManagerScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!Auth::guest()) {
            $user = Auth::user()->id;

            if (
                RoleService::getRole(Auth::user()->type_id) == 'GID'
                || RoleService::getRole(Auth::user()->type_id) == 'TYROPERATOR'
            ) {
                $url_get = Request::url();
                $admin = strpos($url_get, 'admin');

                if ($admin) {
                    $builder->where('user_id', $user);
                }
            }
        }
    }
}
