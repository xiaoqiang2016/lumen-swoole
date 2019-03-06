<?php
namespace App\Models;

class AdIndustryAverageStats extends Model{
    protected $table = 'ad_industry_average_stats';
    protected $guarded = [];
    public $incrementing = false;
    public function getInsights($map,$fields=[]){
        $tableName = $this->getTable();
        $_map = [];
        $fields[] = 'category_level_1';
        $fields[] = 'category_level_2';
        $fields[] = 'category_level_3';
        foreach($map as $v){
            $m = [];
            for($i=1;$i<=3;$i++){
                $m[] = "category_level_{$i} git";
            }
            $_map[] = "(category_level_1 = '{$v['category1_cn']}' AND category_level_2 = '{$v['category2_cn']}' AND category_level_3 = '{$v['category3_cn']}')";
        }
        $_map = implode(" OR ",$_map);
        $sql = "SELECT ".(implode(",",$fields))." FROM {$tableName} WHERE {$_map}";
        echo $sql;
        $r = $this->getDB()->select($sql);
        print_r($r);
    }
}