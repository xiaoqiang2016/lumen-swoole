<?php
namespace App\Models;

class AdAccount extends Model{
    protected $table = 't_ad_account';
    protected $guarded = [];
    public $incrementing = false;
    static public function syncFromStorageByClient(Client $client){
        $_map = [];
        $_map['clientid'] = $client->id;
        $fields = ['auid','status as local_status','accountname as name','channelid as channel_id'];
        $storageData = Storage\Adaccounts::where($_map)->get($fields);
        if($storageData){
            $storageData = $storageData->toArray();
            foreach($storageData as &$v){
                $v['id'] = $v['auid'];
                unset($v['auid']);
            }
            (new self())->syncDataByUser(['user_id'=>$client->getUserID(),'token_id'=>0,'client_id'=>$client->id],$storageData);
        }
        return true;
    }
    //同步数据。
    //需要筛选条件
    //map = ['user_id'=>用户ID,'token_id'=>'授权ID','client_id'=>'公司ID'];
    public function syncDataByUser($map,$data){
        $map['token_id'] = $map['token_id'] ?? 0;
        $map['client_id'] = $map['client_id'] ?? 0;
        $belongModel = new AdAccountBelong();
        $belongModel->where($map)->delete();
        if($data){

            $data = array_column($data,NULL,'id');
            $adaccount_ids = array_column($data,'id');

            $belongData = [];
            foreach($data as &$d){
                $_belongData = [];
                $_belongData['user_id'] = $map['user_id'];
                $_belongData['channel_id'] = $d['channel_id'];
                $_belongData['client_id'] = $map['client_id'] ?? 0;
                $_belongData['account_id'] = $d['id'];
                $_belongData['token_id'] = $d['token_id'] ?? 0;
                $belongData[] = $_belongData;
                unset($d['client_id'],$d['token_id'],$d['user_id']);
            }
            $belongModel->insertAll($belongData);
            #本表Exists
            $exists = $this->whereIn('id',$adaccount_ids)->get(['id']);
            if($exists){
                $exists = array_column($exists->toArray(),'id');
                foreach($exists as $account_id){
                    unset($data[$account_id]);
                }
            }
            if($data){
                $this->insertAll($data);
            }
        }
        return;
    }
}