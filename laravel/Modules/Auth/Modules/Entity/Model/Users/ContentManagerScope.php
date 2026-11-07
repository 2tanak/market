<?php
namespace Modules\Entity\Model\Blog;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
//use Modules\Entity\Model\SysUserType\SysUserType;
use Illuminate\Support\Facades\Request;

use Auth;
use RoleService;

class ContentManagerScope implements Scope{
    public function apply(Builder $builder, Model $model){
				if (!Auth::guest()) {

	 $user = Auth::user()->id;
       if(RoleService::getRole(Auth::user()->type_id) =='GID' ||  RoleService::getRole(Auth::user()->type_id) =='TYROPERATOR'){
		   $url_get = Request::url();
        $admin = strpos($url_get, "admin");
		if($admin){
			
		   $builder->where('user_id', $user);
		}
	   }
    }
	}
	
	
	
	
	
	
}