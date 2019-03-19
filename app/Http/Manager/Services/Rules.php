<?php
namespace App\Http\Manager\Services;
use Swoole; 
use App\Models\Access as Access;
class Rules{


   	//获取权限树
   	public function getRules($params,$flag=false) {
        // if($parmas['manager_id'] == 1) {
        //     $result = $this->select('SELECT id,name,title,sort,level,pid From t_menu ORDER BY sort desc');
        // }else
   		$result = Access::join('t_menu', function ($join) use($params) {
            $join->on('t_role_menu.menu_id', '=', 't_menu.id')
                 ->where('t_role_menu.role_id', '=', $params['manager_id'])
                 ->where('t_menu.show','=',1);
        })
        ->select('t_menu.id','t_menu.name','t_menu.title','t_menu.sort','t_menu.level','t_menu.pid')
        ->orderBy('t_menu.sort','desc')
        ->get()->toArray();

        $tree = [];

        if(!$flag) {
            $tree = $this->getTree($result,0);
        }else{
            $tree = implode(',', array_column($result, 'name'));
        }

	   	return $tree;
   }


   	//递归生成权限菜单
   	private function getTree($data, $pid) {
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