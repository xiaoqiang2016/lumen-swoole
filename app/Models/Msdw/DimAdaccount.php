<?php
namespace App\Models\Msdw;
class DimAdaccount extends Model{
    protected $table = 'msdw.dim_adaccount';
    public function getAdAccountKeys(array $ids){
        #select  max(adaccount_key) as adaccount_key,source_adaccountid from dim_adaccount where source_adaccountid IN ('act_1397197153711689','act_1442946859136718')   group by source_adaccountid  ;
        $r = $this->whereIn('source_adaccountid',$ids)->get(['adaccount_key','source_adaccountid']);
        $result = [];
        foreach($r as $_r){
            $result[$_r['source_adaccountid']] = $result[$_r['source_adaccountid']] ?? 0;
            if($result[$_r['source_adaccountid']] < $_r['adaccount_key']) $result[$_r['source_adaccountid']] =  $_r['adaccount_key'];
        }
        return $result;
    }
}