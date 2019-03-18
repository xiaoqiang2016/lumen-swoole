<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//角色model
class Role extends Model{
	protected $connection = 'sinoclick';
    protected $table = 't_role';

    public function roles() {
    	return $this->belongsToMany('App\Models\Menu','t_role_menu','role_id','menu_id','id','id');
    }

}