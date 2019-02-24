<?php
namespace App\Models;
use DB;
use Log;
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
    public function updateAll(array $multipleDatas, $whenField = 'id', $whereField = 'id')
    {
        $tableName = $this->getTable();


//        // 判断需要更新的数据里包含有放入when中的字段和where的字段
//        if(!$update) return false;
//        $when = [];
//        // 拼装sql，相同字段根据不同条件更新不同数据
//        $sliceNum = 10;
//        $data = (array)$update;
//        for($i=0;$i<count($data);$i+=$sliceNum){
//            echo "Update Finish :" . $i . "/" . count($data).PHP_EOL;
//            $_update = array_slice($data,$i,$sliceNum);
//            $build = \DB::table($table)->whereIn($whereField, array_column($_update,$whereField));
//            foreach ($_update as $sets) {
//                $whenValue = $sets[$whenField];
//                foreach ($sets as $fieldName => $value) {
//                    if ($fieldName == $whenField) continue;
//                    if (is_null($value)) $value = 'null';
//                    $when[$fieldName][] = "case when {$whenField} = '{$whenValue}' then '{$value}' end";
//                }
//            }
//            print_r($when);
//            return;
//            $build->update($when);
//        }
//        return;
        $sliceNum = 500;
        for($i=0;$i<count($multipleDatas);$i+=$sliceNum) {
            $multipleData = array_slice($multipleDatas,$i,$sliceNum);
            if( $tableName && !empty($multipleData) ) {
                // column or fields to update
                $updateColumn = array_keys($multipleData[0]);
                $referenceColumn = $updateColumn[0]; //e.g id
                unset($updateColumn[0]);
                $whereIn = "";

                $q = "UPDATE `".$tableName."` SET ";
                foreach ( $updateColumn as $uColumn ) {
                    $q .=  "`".$uColumn."` = CASE ";

                    foreach( $multipleData as $data ) {
                        $q .= "WHEN ".$referenceColumn." = '".$data[$referenceColumn]."' THEN '".$data[$uColumn]."' ";
                    }
                    $q .= "ELSE `".$uColumn."` END, ";
                }
                foreach( $multipleData as $data ) {
                    $whereIn .= "'".$data[$referenceColumn]."', ";
                }
                $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
                // Update
                \DB::update(DB::raw($q));

            }
        }
        return;

    }
}