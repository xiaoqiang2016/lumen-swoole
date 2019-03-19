<?php

namespace App\Http\Manager\Services;
use App\Models\Manager as ManagerModel;
use App\Models\ManagerAgent as ManagerAgentModel;
class Manager
{
    public function register($params)
    {
        $id = (new ManagerModel())->insertGetId($params);
        $ret = (new ManagerAgentModel())->insert(array('id'=>$id));
        return $ret;
    }
}