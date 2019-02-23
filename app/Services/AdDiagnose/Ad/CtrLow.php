<?php

namespace App\Services\AdDiagnose\Ad;
use Swoole;
use App\Models;
use App\Common\Helper;
class CtrLow extends Base
{
    public $name = "CTR较低";
    public $description = "";
    public $connection = 'msdw';
    public function handle(){
        $ad_account_ids = $this->getParam('ad_account_ids');
        $adAd = new Models\AdAd();
        $adAd = $adAd->where('status','ACTIVE');
        $adAd = $adAd->whereIn('account_id',$ad_account_ids);
        $adAd = $adAd->where('category1_cn','!=','');
        $adAd = $adAd->where('created_time','<=',Helper::GND(-2,"Y-m-d 00:00:00"));
        $r = $adAd->get(['id','category1_cn','category2_cn','category3_cn']);
        $r = $r->toArray();
        $r = array_slice($r,0,10);
        $ad_ids = array_column($r,'id');
        $r = (new Models\Msdw\FactFbAdinsightsCountry)->getCtrByAdIds($ad_ids);
        print_r($r);
        return;
        $ad_account_ids = $this->getParam('ad_account_ids');
        app('db')->connection($this->connection);
        print_r($ad_account_ids);
    }
    public function getDescription(){

    }
}