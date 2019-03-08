<?php
namespace App\Models;

class AdIndustryAverageStats extends Model{
    protected $table = 'ad_industry_average_stats';
    protected $guarded = [];
    public $incrementing = false;
    public function getInsights($map,$fields=[]){
        $tableName = $this->getTable();
        $_map = [];
        $fields[] = 'category_level_1 as category1_cn';
        $fields[] = 'category_level_2 as category2_cn';
        $fields[] = 'category_level_3 as category3_cn';
        foreach($map as $v){
            $m = [];
            for($i=1;$i<=3;$i++){
                $m[] = "category_level_{$i} ".($v["category{$i}_cn"]?" = '".$v["category{$i}_cn"]."'":' is null');
            }
            $_map[] = "(".implode(" AND ",$m).")";
        }
        $_map = implode(" OR ",$_map);
        $sql = "SELECT ".(implode(",",$fields))." FROM {$tableName} WHERE {$_map}";
        $r = $this->getDB()->select($sql);
        $r = json_decode(json_encode($r),true);
        return $r;
    }
}