<?php

namespace App\Http\Manager\Services;
use App\Models\Manager as ManagerModel;
use App\Models\ManagerAgent as ManagerAgentModel;
use App\Models\Role as Role;
class Manager
{
    public function register($params)
    {
        $id = (new ManagerModel())->insertGetId($params);
        $ret = (new ManagerAgentModel())->insert(array('id'=>$id));
        return $ret;
    }

    //用户分配权限
    public function userAllocation($params) {
    	if($params['type'] == 'Admin') {   //普通后台用户不分等级
    		if($params['user_id'] == 1) {
    			return false;
    		}
    		return (new ManagerModel)->where('id',$params['user_id'])->update(['role_id'=>$params['role_id']]);
    	}

        if(!in_array($params['user_id'],(new ManagerModel())->findChildManager($params['manager_id'],true))) {
            return false;
        }

        $roleArray = (new Role())->findChildRole($params['manager_id']);
        if($roleArray) {
        	$roleArray = array_column($roleArray, 'id');
        	if(!in_array($params['role_id'])) return false;
        }
        
        
    }
}