<?php
namespace App\Models;

class Model extends \Illuminate\Database\Eloquent\Model{
    public function addAll($data){
        $rs = \DB::table($this->getTable())->insert($data);
        return $rs;
    }
}