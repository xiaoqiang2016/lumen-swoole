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
        $disable_reason = [
            0 => 'NONE',
            1 => 'ADS_INTEGRITY_POLICY',
            2 => 'ADS_IP_REVIEW',
            3 => 'RISK_PAYMENT',
            4 => 'GRAY_ACCOUNT_SHUT_DOWN',
            5 => 'ADS_AFC_REVIEW',
            6 => 'BUSINESS_INTEGRITY_RAR',
            7 => 'PERMANENT_CLOSE',
            8 => 'UNUSED_RESELLER_ACCOUNT',
            9 => 'UNUSED_ACCOUNT',
        ];
        if($tokens) foreach($tokens as $token){
            go(function() use ($token,$user,$disable_reason){
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
                        $data['disable_reason'] = $disable_reason[$adAccount['disable_reason']];
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
                    (new Models\AdSet())->syncData($syncMap,$data);
                    $chan->push(['index'=>$i,'account_id'=>$adAccount->account_id]);
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
        }
        return $result;
    }
    public function syncAdAdByUser(Models\User $user){
        $result = [];
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);


        if($adAccounts) {
            $tokens = $this->getTokensByAdAccount($adAccounts);
            $chan = new co\Channel(1);
            $r = [];
            $i = 0;
            co::set(['max_coroutine' => 8191]);
            $requests = [];
            $taskCount = count($adAccounts);
           # $taskCount = 3;
            foreach($adAccounts as $adAccount){
                if($i >= $taskCount) break;
//                $r[] = $adAccount->account_id;
//
//                $requests[] = ['uri' => 'https://graph.facebook.com/v3.2/'.$adAccount->account_id.'?fields=ads.limit(999999)%7Bname%2Cdaily_budget%2Ccreated_time%2Ceffective_status%2Cid%2Ccampaign_id%2Cadset_id%7D&access_token=EAAHE0mTSloIBAIjVmFt3NEbmLz1GvIYE5MUhdQqPaK1QJeRvu8whGPJp8DJWTDjTuWuw3gsZAZCBc1zZARE8KPeFfATHopP299Tm31J1aLmHZCJneShLqRgok6TxMG8rUh2lkB9red2RdmWqX6bPNCgJ52ndNlDHnsZCUgoWRnQZDZD'];
//
//
//                continue;
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
                        $_data['account_id'] = $adAccount->account_id;
                        $data[] = $_data;
                    }
                    $runtime = microtime(true) - $starttime;
                    $syncData = ['account_id'=>$adAccount->account_id];
                    $insertData = $data;
                    #(new Models\AdAd())->syncData($syncData,$insertData);
                    $chan->push(['index'=>$i,'runtime'=>$runtime,'dataLength'=>count($data),'data'=>$data,'adaccount_id'=>$adAccount->account_id]);
                });
                $i++;
            }
//            go(function() use ($requests){
//                $res = \Swlib\SaberGM::requests($requests);
//                echo "use {$res->time}s\n";
//                echo "success: $res->success_num, error: $res->error_num";
//
//            });
//            return;
            $dataLength = 0;
            $insertData = [];
            while(1){

                $data = $chan->pop();
                $insertData = array_merge($insertData,$data['data']);
                $result[$data['index']] = $data;
                #echo count($result)."/".count($adAccounts);
                #echo PHP_EOL;
                $dataLength += $data['dataLength'];
                echo $dataLength."|".$data['dataLength']."|".$data['adaccount_id']."|{$data['runtime']}|".PHP_EOL;
                if(count($result) == $taskCount){
                    #(new Models\AdAd())->delete();
                    $syncData = ['account_id'=>array_filter(array_column($adAccounts->toArray(),'account_id'))];
                    (new Models\AdAd())->syncData($syncData,$insertData);
                    echo \App\Common\Helper::runTime();
                    break;
                }
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
    public function syncFacebookPageByUser(Models\User $user){
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);



        $tokens = $this->getTokensByAdAccount($adAccounts);
        $chan = new co\Channel(1);
        $starttime = microtime(true);
        $ii = 0;
        $taskCount = count($adAccounts);
        foreach($adAccounts as $adAccount){
            $ads = $adAccount->getAdAds();
            $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);
            go(function() use($adAccount,$sdk,$ii,$chan){
                $sdkDatas = $sdk->getPagesByAdAccountID($adAccount->account_id);
                $result = [];
                if($sdkDatas) foreach($sdkDatas as $sdkData){
                    $_result = [];
                    $_result['page_id'] = $sdkData['id'];
                    $_result['status'] = $sdkData['is_published']?1:0;
                    $_result['name'] = $sdkData['name'];
                    $_result['account_id'] = $adAccount->account_id;
                    $result[] = $_result;
                }
                $chan->push(['index'=>$ii,'result'=>$result]);
            });
            $ii++;
        }
        $result = [];
        $index = 0;
        while(1){
            $data = $chan->pop();
            #$result[$data['index']] = $data['result'];
            $result = array_merge($data['result'],$result);
            echo $index."/".$taskCount;
            echo PHP_EOL;
            $index++;
            #print_r($data);
            #echo PHP_EOL;
            if($index == $taskCount && $result = array_filter($result)){
                #print_r($result);
                $account_ids = array_column($adAccounts->toArray(),'account_id');
                (new \App\Models\FacebookPage())->syncData(['account_id'=>$account_ids],$result);
            }
        }
        #print_r($adAccounts->toArray());
        return;
    }
    public function syncAdAdInsightsByUser(Models\User $user){
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);
        $account_ids = array_column($adAccounts->toArray(),'id');


        $tokens = $this->getTokensByAdAccount($adAccounts);
        $chan = new co\Channel(1);
        $starttime = microtime(true);
        $ii = 0;
        foreach($adAccounts as $adAccount){
            $ads = $adAccount->getAdAds();
            $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);

            if($ads && count($ads) == 1130){
//                echo count($ads).PHP_EOL;
//                continue;
                foreach($ads as $ad){
                    $ii++;
                    go(function() use($ad,$sdk,$ii,$chan,$starttime,&$aa){
                        $runtime = microtime(true)-$starttime;
                        #$ad->id = '23843158762870772';
                        $r = $sdk->getInsightsByAdID($ad->id,['day_offset'=>0]);
                        $chan->push(['index'=>$ii,'runtime'=>$runtime,'result'=>$r]);

                        if($r){

                            #echo $ii;
                            #echo PHP_EOL;
                        }
                    });
                }
                break;
            }

        }
        while(1){
            $data = $chan->pop();
            $result[$data['index']] = $data;
            echo (count($result))."/".$ii;
            echo PHP_EOL;
            #print_r($data);
            #echo PHP_EOL;
            if(count($result) == count($adAccounts)){
                #break;
            }
        }
        #print_r($adAccounts->toArray());
        return;
    }
    public function adDiagnoseByUser(Models\User $user){
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);
        $adAccountIds = array_column($adAccounts->toArray(),"account_id");
        $adDiagnose = new App\Services\AdDiagnose\Dispatcher();
        $adDiagnose->handle(['ad_account_ids'=>$adAccountIds]);
        #print_r($adAccountIds);
        #print_r($adAccounts->getAdAccountIds());
        return;
    }
}