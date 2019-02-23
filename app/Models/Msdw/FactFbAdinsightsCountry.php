<?php
namespace App\Models\Msdw;
class FactFbAdinsightsCountry extends Model{
    protected $table = 'fact_fb_ad_insights_country';
    public function getCtrByAdIds(array $ad_ids){
        $db = $this->getDB();
        $result = $db->select("SELECT ");
    }
}