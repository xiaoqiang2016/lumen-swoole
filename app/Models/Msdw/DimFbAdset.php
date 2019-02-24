<?php
namespace App\Models\Msdw;
class DimFbAdset extends Model{
    protected $table = 'msdw.dim_fb_adset';
    public function DimFbCampaign(){
        return $this->hasOne('App\Models\Msdw\DimFbCampaign','campaign_id','campaign_id');
    }
}