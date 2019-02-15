<?php
namespace App\Models;

class AdAccountBelong extends Model{
    protected $table = 't_ad_account_belong';
    protected $guarded = [];
    public function adAccount(){
        return $this->hasOne('App\Models\AdAccount','account_id','id');
    }
    public function getAdAds(array $fields=['id']){
        $result = AdAd::where("account_id",$this->account_id)->get($fields);
        return $result->isempty() ? false : $result;
    }
}