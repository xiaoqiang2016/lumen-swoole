<?php
namespace App\Models;

class Menu extends Model{
	protected $connection = 'sinoclick';
    protected $table = 't_menu';

    public $timestamps = false;

    //获取可操作菜单列表
    // public function findMenu($role_id) {
    // 	return app('db')->connection('facetol')->select("SELECT  FROM t_role_menu AS a INNER JOIN t_menu ");
    // }

}