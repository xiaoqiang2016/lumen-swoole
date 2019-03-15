<?php

namespace App\Manager\Controllers;
use Illuminate\Http\Request;

class Permissions extends Controller {


    public function permissions(){
    	$params['manager_id'] = 1;
        $permission = new \App\Manager\Services\Permissions();
        return $permission->getPermissions($params);
    }


}