<?php
namespace App\Models;

class Manager extends Model
{
    protected $connection = 'facetool';
    protected $table = 't_manager';
    public $timestamps = false;

    //查找子级用户
    public function findChildManager($params,$flag=false) {

        $userInfo = $this->where('id',$params['manager_id'])->first()->toArray();

        $wh = '';
        if(isset($params['listType']) && $params['listType'] == 'Agent') {
            $wh = " AND type = 'Agent'";
        }

        //如果是代理商，查找自己所有下级
        if($userInfo['type'] == 'Agent') {
            $agentInfo = $userInfo;
        }else{
            //若为其他类型用户，查找直属代理商ID
            $agentInfo = $this->where(['id'=>$userInfo['parent_id'],'type'=>'Agent'])->first()->toArray();
        }

        $data = $this->getDB()->select("SELECT id,name,parent_id,type FROM `t_manager` WHERE (FIND_IN_SET({$agentInfo['id']},parent_ids) OR id = {$agentInfo['id']}) AND use_status = 1 {$wh}");

        if($flag) {
            $tree = array_column($data, 'id');
            return $tree;
        }

    	if(!empty($data)) {
    		return $this->getTree($data, 0);
    	}
    	return [];
    }

    private function getTree($data, $pid) {
		$tree = [];
		foreach($data as $k => $v){
		    if($v->parent_id == $pid){ 
		     	$v->son = self::getTree($data, $v->id);
		      	$tree[] = $v;
		  }
		}
		return $tree;
	}


    //添加用户
    public function userAdd($params,$type) {
        $userInfo = $this->where('id',$params['manager_id'])->first()->toArray();
    	$saveData['pwd'] = md5(md5($params['pwd']).'sinoclick');
    	$saveData['name'] = $params['name'];
    	$saveData['phone'] = $params['phone'];
    	$saveData['type'] = $type;
    	$parentData = $this->findParentIds($saveData['parent_id']);
    	$selfIDs = isset($parentData['parent_ids'])?$parentData['parent_ids'].",{$params['parent_id']}" : $params['parent_id'];
        //如果添加代理商
        if($type == 'Agent') {
            $saveData['parent_id'] = $params['parent_id'];
            $saveData['reg_id'] = $params['reg_id'];
            $selfLevel = isset($parentData['level'])?$parentData['level'] + 1 : '';
        }else{
            $saveData['parent_id'] = $userInfo['id'];
            $selfLevel = isset($parentData['level'])?$parentData['level'] : '';
        }
    	$saveData['parent_ids'] = $selfIDs;
    	$saveData['level'] = $selfLevel;
    	return  $this->insert($saveData);
    }


    //查找父类parent_ids
   	public function findParentIds($id) {
   		return $this->select('parent_ids','level')->where('id',$id)->first()->toArray();
   	}


}