<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//角色model
class Role extends Model{
	protected $connection = 'facetool';
    protected $table = 't_role';
    public $timestamps = false;
    //获取所有有效角色
  	public function findAll() {
  		return $this->select('id','name')->where('status','=',1)->get()->toArray();
  	}

  	//获取子级有效角色列表
  	public function findChildRole($id) {
        if($id == 1) {
            return $this->findAll();
        }

       	return app('db')->connection("{$this->connection}")->select("SELECT id,name FROM `t_role` WHERE (id IN (SELECT `role_id` FROM `t_manager` WHERE FIND_IN_SET('{$id}',parent_ids)) OR create_manager_id = {$id}) AND status = 1 ");
  	}

}