<?php
namespace App\Models\Storage;

class Model extends \Illuminate\Database\Eloquent\Model{
    protected $connection = 'facebook';
    public function getDB(){
        $db = \DB::connect($this->connection);
        return $db;
    }
    public static function addAll($data){
        $rs = \DB::table((new self())->getTable())->insert($data);
        return $rs;
    }
}