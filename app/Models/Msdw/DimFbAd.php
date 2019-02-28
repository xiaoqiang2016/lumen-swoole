<?php
namespace App\Models\Msdw;
class DimFbAd extends Model{
    protected $table = 'msdw.dim_fb_ad';
    public function getCategoryByAdIds(array $ids){
        $sql = "SELECT ad_id,category1_cn,category2_cn,category3_cn FROM {$this->getTable()} WHERE ad_id IN (".implode(",",$ids).") AND category1_cn != ''";
        $result = $this->getDB()->select($sql);
        return $result;
    }
}