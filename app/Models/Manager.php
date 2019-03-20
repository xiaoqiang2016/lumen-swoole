<?php
namespace App\Models;

class Manager extends Model
{
    protected $connection = 'facetool';
    protected $table = 't_manager';

    //查找子级用户
    public function findChildManager($id,$flag=false) {
    	$data = $this->getDB()->select("SELECT id,name,parent_id FROM `t_manager` WHERE (FIND_IN_SET({$id},parent_ids) OR id = {$id}) AND use_status = 1");

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
    	$saveData['pwd'] = md5(md5($params['pwd']).'sinoclick');
    	$saveData['name'] = $params['name'];
    	$saveData['phone'] = $params['phone'];
    	$saveData['type'] = $type;
    	$saveData['reg_id'] = !empty($params['reg_id']) ? $params['reg_id'] : '';
    	$saveData['parent_id'] = $params['parent_id'];
    	$parentData = $this->findParentIds($params['parent_id']);
    	$selfIDs = isset($parentData[0]['parent_ids'])?$parentData[0]['parent_ids'].",{$params['parent_id']}" : $params['parent_id'];
        if($type == 'Agent') {
            $selfLevel = isset($parentData[0]['level'])?(int)$parentData[0]['level'] + 1 : '';
        }else{
            $selfLevel = isset($parentData[0]['level'])?(int)$parentData[0]['level'] : '';
        }
    	$saveData['parent_ids'] = $selfIDs;
    	$saveData['level'] = $selfLevel;
    	return  $this->insert($saveData);
    }


    //查找父类parent_ids
   	public function findParentIds($id) {
   		return $this->select('parent_ids','level')->where('id',$id)->get()->toArray();
   	}





}