<?php
namespace App\Http\Home\Services;
use Swoole; 
use App\Models\Role as Role;
// use App\Models\Menu as Menu;
use App\Models\Access as Access;
class Permissions{

   	public function __construct() {

   	}


   	//获取权限树
   	public function getPermissions($params) {

   		$result = Access::join('t_menu', function ($join) use($params) {
            $join->on('t_role_menu.menu_id', '=', 't_menu.id')
                 ->where('t_role_menu.role_id', '=', $params['manager_id'])
                 ->where('t_menu.show','=',1);
        })
        ->select('t_menu.id','t_menu.name','t_menu.title','t_menu.sort','t_menu.level','t_menu.pid')
        ->get()->toArray();
        $tree = [];
        if($result) {
		    $tree = $this->getTree($result,0);
        }
	   	return $tree;
   }


   	//递归生成菜单
   	function getTree($data, $pid) {
		$tree = [];
		foreach($data as $k => $v){
		    if($v['pid'] == $pid){ 
		     	$v['son'] = self::getTree($data, $v['id']);
		      	$tree[] = $v;
		  }
		}
		return $tree;
	}
  
}