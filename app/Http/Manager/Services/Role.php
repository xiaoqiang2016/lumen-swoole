<?php

namespace App\Http\Manager\Services;
use App\Models\Role as RoleModel;
use App\Models\Manager as ManagerModel;
class Role
{

    public function findChildRole($params) {

        $userInfo = (new ManagerModel())->where('id','=',$params['manager_id'])->first()->toArray();
        if($userInfo['type'] == 'Agent') {
            $agentInfo = $userInfo;
        }else{
        	$agentInfo = (new ManagerModel)->where(['id'=>$userInfo['parent_id'],'type'=>'Agent'])->first()->toArray();
        }

        return  app('db')->connection("sinoclick")->select("SELECT id,name FROM `t_role` WHERE (id IN (SELECT `role_id` FROM `t_manager` WHERE FIND_IN_SET('{$agentInfo['id']}',parent_ids)) OR create_manager_id = {$agentInfo['id']}) AND status = 1 ");
    }

    public function roleAdd($params) {
    	$userInfo = (new ManagerModel())->where('id','=',$params['manager_id'])->first()->toArray();
    	if($usreInfo['type'] == 'Agent') {
    		$saveData['create_manager_id'] = $params['manager_id'];
    	}else{
    		$agentInfo = (new ManagerModel)->where(['id'=>$userInfo['parent_id'],'type'=>'Agent'])->first()->toArray();
    		$saveData['create_manager_id'] = $agentInfo['id'];
    	}
    	$saveData['name'] = $params['name'];
    	return (new RoleModel())->insert($saveData);
    }	
}