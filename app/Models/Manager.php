<?php
namespace App\Models;

class Manager extends Model
{
    protected $connection = 'facetool';
    protected $table = 't_manager';

    //查找子级用户
    public function findChildManager($id) {
    	$data = $this->getDB()->select("SELECT id,name,parent_id FROM `t_manager` WHERE (FIND_IN_SET({$id},parent_ids) OR id = {$id}) AND use_status = 1");
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

    public function userAdd($params) {
    	$saveData['pwd'] = md5(md5($params['pwd']).'sinoclick');
    	$saveData['name'] = $parmas['name'];
    	$saveData['phone'] = $parmas['phone'];
    	$saveData['type'] = $parmas['type'];
    	$saveData['reg_id'] = !empty($parmas['reg_id']) ? $parmas['reg_id'] : '';
    	$saveData['parent_id'] = $params['parent_id'];
    	$parentData = $this->findParentIds($parmas['parent_id']);
    	$selfIDs = isset($parentData[0]['parent_ids'])?$parentData[0]['parent_ids'].",{$parmas['parent_id']}" : '';
    	$selfLevel = isset($parentData[0]['level'])?(int)$parentData[0]['level'] + 1 : '';
    	$saveData['parent_ids'] = $selfIDs;
    	$saveData['level'] = $selfLevel;
    	return  $this->insert($saveData);
    }


    //查找父类parent_ids
   	public function findParentIds($id) {
   		return $this->select('parent_ids','level')->where('id',$id)->get()->toArray();
   	}



}