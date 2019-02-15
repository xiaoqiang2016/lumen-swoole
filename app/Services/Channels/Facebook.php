<?php

namespace App\Services\Channels;
use Swoole;
use App\Models;
use App\Models\Sdk;
use Swoole\Coroutine as co;
class Facebook extends Channel{
    private $id = 1;
    public function getSdk($token=false){
        return (new Sdk\Facebook())->setToken($token);
    }
    #同步远端账号
    public function syncAdAccountByUser(Models\User $user){
        $tokens = $user->getTokens();

        if($tokens) foreach($tokens as $token){
            go(function() use ($token,$user){
                $adAccountModel = new \App\Models\AdAccount();
                $sdk = $this->getSdk($token->token);
                $adAccountList = $sdk->getAccountList();
                $syncData = [];
                if($adAccountList){
                    foreach($adAccountList as $adAccount){
                        $data = [];
                        $data['id'] = $adAccount['id'];
                        $data['name'] = $adAccount['name'];
                        $data['balance'] = $adAccount['balance'] / 1000;
                        $data['remote_status'] = $adAccount['account_status'];
                        $data['user_id'] = $user->getID();
                        $data['client_id'] = 0;
                        $data['token_id'] = $token->id;
                        $data['created_time'] = $this->parseTime($adAccount['created_time']);
                        $data['channel_id'] = $this->id;
                        $syncData[] = $data;
                    }
                }
                $adAccountModel->syncDataByUser(['user_id'=>$user->getID(),'token_id'=>$token->id],$syncData);
            });
        }
        #print_r($tokens);
    }
    #时间格式转换
    private function parseTime($time){
        return date("Y-m-d H:i:s",strtotime($time));
    }
    #同步广告系列
    public function syncAdCampaignByUser(Models\User $user){
        $result = [];
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);
        $tokens = [0=>false,1692=>'EAAHE0mTSloIBAIjVmFt3NEbmLz1GvIYE5MUhdQqPaK1QJeRvu8whGPJp8DJWTDjTuWuw3gsZAZCBc1zZARE8KPeFfATHopP299Tm31J1aLmHZCJneShLqRgok6TxMG8rUh2lkB9red2RdmWqX6bPNCgJ52ndNlDHnsZCUgoWRnQZDZD'];
        $i = 0;
        if($adAccounts) {
            $allTime = microtime(true);
            $chan = new co\Channel(1);
            $r = [];
            foreach($adAccounts as $adAccount){
                $r[] = $adAccount->account_id;
                go(function() use ($adAccount,&$tokens,$i,$chan){
                    $starttime = microtime(true);
                    #$adAccount->account_id = 'act_326129137884056';
                    #$adAccount->token_id = 1692;

                    $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);
                    $sdkDatas = $sdk->getAdCampaignListByAdAccountID($adAccount->account_id);
                    $data = [];
                    if($sdkDatas) foreach($sdkDatas as $sdkData){
                        $_data = [];
                        $_data['id'] = $sdkData['id'];
                        $_data['name'] = $sdkData['name'];
                        $_data['objective'] = $sdkData['objective'];
                        $_data['status'] = $sdkData['effective_status'];
                        $_data['created_time'] = $this->parseTime($sdkData['created_time']);
                        $data[] = $_data;
                    }
                    $syncMap = ['channel_id'=>$this->id,'account_id'=>$adAccount->account_id];
                    $runtime = microtime(true)-$starttime;
                    (new Models\AdCampaign())->syncData($syncMap,$data);
                    $chan->push(['index'=>$i,'runtime'=>$runtime,'account_id'=>$adAccount->account_id]);
                });
                $i++;
            }
            while(1){
                $data = $chan->pop();
                $result[$data['index']] = $data;
                #echo count($result)."/".count($adAccounts);
                #echo PHP_EOL;
                if(count($result) == count($adAccounts)){
                    break;
                }
            }
            print_r($result);
            echo PHP_EOL;
            echo  microtime(true) - $allTime;

        }
        return $result;
    }
    #同步广告组
    public function syncAdSetByUser(Models\User $user){
        $result = [];
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);

        if($adAccounts) {
            $tokens = $this->getTokensByAdAccount($adAccounts);
            $chan = new co\Channel(1);
            $r = [];
            $i = 0;
            foreach($adAccounts as $adAccount){
                $r[] = $adAccount->account_id;
                go(function() use ($adAccount,&$tokens,$i,$chan){
                    $starttime = microtime(true);
                    $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);
                    $sdkDatas = $sdk->getAdSetListByAdAccountID($adAccount->account_id);
                    $data = [];
                    if($sdkDatas) foreach($sdkDatas as $sdkData){
                        $_data = [];
                        $_data['id'] = $sdkData['id'];
                        $_data['campaign_id'] = $sdkData['campaign_id'];
                        $_data['name'] = $sdkData['name'];
                        $_data['budget'] = $sdkData['daily_budget'] ?? 0;
                        $_data['status'] = $sdkData['effective_status'];
                        $_data['created_time'] = $this->parseTime($sdkData['created_time']);
                        $data[] = $_data;
                    }
                    $syncMap = ['channel_id'=>$this->id,'account_id'=>$adAccount->account_id];
                    $runtime = microtime(true)-$starttime;
                    (new Models\AdSet())->syncData($syncMap,$data);
                    $chan->push(['index'=>$i,'runtime'=>$runtime,'account_id'=>$adAccount->account_id]);
                });
                $i++;
            }
        }
    }
    public function syncAdAdByUser(Models\User $user){
        $result = [];
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);

        if($adAccounts) {
            $tokens = $this->getTokensByAdAccount($adAccounts);
            $chan = new co\Channel(1);
            $r = [];
            $i = 0;
            foreach($adAccounts as $adAccount){
                $r[] = $adAccount->account_id;
                go(function() use ($adAccount,&$tokens,$i,$chan){
                    $starttime = microtime(true);
                    $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);
                    $sdkDatas = $sdk->getAdAdListByAdAccountID($adAccount->account_id);
                    $data = [];
                    if($sdkDatas) foreach($sdkDatas as $sdkData){
                        $_data = [];
                        $_data['id'] = $sdkData['id'];
                        $_data['campaign_id'] = $sdkData['campaign_id'];
                        $_data['set_id'] = $sdkData['adset_id'];
                        $_data['name'] = $sdkData['name'];
                        #$_data['budget'] = $sdkData['daily_budget'] ?? 0;
                        $_data['status'] = $sdkData['effective_status'];
                        $_data['created_time'] = $this->parseTime($sdkData['created_time']);
                        $data[] = $_data;
                    }
                    $syncMap = ['channel_id'=>$this->id,'account_id'=>$adAccount->account_id];
                    $runtime = microtime(true)-$starttime;
                    #print_r($data);
                    (new Models\AdAd())->syncData($syncMap,$data);
                    #$chan->push(['index'=>$i,'runtime'=>$runtime,'account_id'=>$adAccount->account_id]);
                });
                $i++;
            }
        }
    }
    private function getTokensByAdAccount($adAccounts){
        $_tokens = array_unique(array_column($adAccounts->toArray(),'token_id'));
        $tokens = [];
        foreach($_tokens as $token){
            if($token == 0){
                $tokens[$token] = false;
            }else{
                $tokens[$token] = \App\Models\UserToken::find($token)->toArray()['token'];
            }

        }
        return $tokens;
    }
//    public function syncAdAdInsightsByUser(Models\User $user){
//        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);
//        $tokens = $this->getTokensByAdAccount($adAccounts);
//        $chan = new co\Channel(1);
//        $starttime = microtime(true);
//        $ii = 0;
//        foreach($adAccounts as $adAccount){
//            $ads = $adAccount->getAdAds();
//            $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);
//
//            if($ads && count($ads) == 1130){
////                echo count($ads).PHP_EOL;
////                continue;
//                foreach($ads as $ad){
//                    $ii++;
//                    go(function() use($ad,$sdk,$ii,$chan,$starttime,&$aa){
//                        $runtime = microtime(true)-$starttime;
//                        #$ad->id = '23843158762870772';
//                        $r = $sdk->getInsightsByAdID($ad->id,['date'=>]);
//                        $chan->push(['index'=>$ii,'runtime'=>$runtime,'result'=>$r]);
//
//                        if($r){
//
//                            #echo $ii;
//                            #echo PHP_EOL;
//                        }
//                    });
//                }
//                break;
//            }
//
//        }
//        while(1){
//            $data = $chan->pop();
//            $result[$data['index']] = $data;
//            echo (count($result))."/".$ii;
//            echo PHP_EOL;
//            #print_r($data);
//            #echo PHP_EOL;
//            if(count($result) == count($adAccounts)){
//                #break;
//            }
//        }
//        #print_r($adAccounts->toArray());
//        return;
//    }
}