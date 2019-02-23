<?php
namespace App\Models\Msdw;
use DB;
use Log;
class Model extends \Illuminate\Database\Eloquent\Model{
    protected $connection = 'msdw';
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
}