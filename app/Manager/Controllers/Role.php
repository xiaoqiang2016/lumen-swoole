<?php

namespace App\Manager\Controllers;
use Illuminate\Http\Request;

class Role extends Controller {

    public function role_add() {
    	$params['manager_id'] = 1;
    	$a = $this->getParams();
    	dd($a);
        //return $permission->getPermissions($params);
    }


}