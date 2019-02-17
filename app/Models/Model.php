<?php
namespace App\Models;
use DB;
class Model extends \Illuminate\Database\Eloquent\Model{
    protected $connection = 'sinoclick';
    protected $guarded = [];
    public function getDB(){
        $db = app('db')->connection($this->connection);
        return $db;
    }
    public function addAll($data){
        return $this->insertAll($data);
    }
    public function insertAll($data){
        $tableName = $this->table;
        $sliceNum = 500;
        for($i=0;$i<count($data);$i+=$sliceNum){
            $v = array_slice($data,$i,$sliceNum);
            $this->insert($v);
        }
        return;
        #return $rs;
    }
    public function syncData($map,$data){
        $t = $this;
        foreach($map as $k=>&$v){
            if(is_array($v)){
                $t = $t->whereIn($k,$v);
                unset($map[$k]);
            }else{
                $t = $t->where($k,"=",$v);
            }
        }
        $t->delete();
        if(count($map) > 0) foreach($data as &$v) $v = array_merge($v,$map);
        $this->insertAll($data);
    }
}