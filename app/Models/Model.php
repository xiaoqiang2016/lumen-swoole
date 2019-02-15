<?php
namespace App\Models;
use DB;
class Model extends \Illuminate\Database\Eloquent\Model{
    protected $connection = 'sinoclick';
    protected $guarded = [];
    public function getDB(){
        $db = DB::connect($this->connection);
        return $db;
    }
    public function addAll($data){
        $rs = DB::table($this->table)->insert($data);
        return $rs;
    }
    public function syncData($map,$data){
        $this->where($map)->delete();
        foreach($data as &$v) $v = array_merge($v,$map);
        $this->addAll($data);
    }
}