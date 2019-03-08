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
    //根据广告账号ID同步分类
    public function syncAdAdCategoryByAccountIds(array $ad_account_ids){
        if(!$ad_account_ids) return;
        foreach($ad_account_ids as &$v) $v = "'".str_replace("act_","",$v)."'";
        $verticaDB = \App\Common\Helper::getVerticaConn();
        $sql = "select ad_id as id,category1_cn,category2_cn,category3_cn from msdw.dim_fb_ad where account_id IN (".implode(",",$ad_account_ids).") and category1_cn != ''  ;";

        $result = $verticaDB->select($sql);
        (new \App\Models\AdAd())->updateAll($result);
        #print_r($result);
    }
    public function syncAdAdByUser(Models\User $user){
        $result = [];
        $adAccounts = $user->getAdAccountBelongsByChannel($this->id);

        //同步分类
        $adAccountIds = array_column($adAccounts->toArray(),'account_id');

//        return;
//
//        print_r($result);
//        echo 1;
//        return;

        if($adAccounts) {
            $tokens = $this->getTokensByAdAccount($adAccounts);
            $chan = new co\Channel(1);
            $r = [];
            $i = 0;
            $requests = [];
            $taskCount = count($adAccounts);
           # $taskCount = 3;
            foreach($adAccounts as $adAccount){
                if($i >= $taskCount) break;
//                $r[] = $adAccount->account_id;
//
//                $requests[] = ['uri' => 'https://graph.facebook.com/v3.2/'.$adAccount->account_id.'?fields=ads.limit(999999)%7Bname%2Cdaily_budget%2Ccreated_time%2Ceffective_status%2Cid%2Ccampaign_id%2Cadset_id%7D&access_token=EAAHE0mTSloIBAIjVmFt3NEbmLz1GvIYE5MUhdQqPaK1QJeRvu8whGPJp8DJWTDjTuWuw3gsZAZCBc1zZARE8KPeFfATHopP299Tm31J1aLmHZCJneShLqRgok6TxMG8rUh2lkB9red2RdmWqX6bPNCgJ52ndNlDHnsZCUgoWRnQZDZD'];
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
                #echo $dataLength."|".$data['dataLength']."|".$data['adaccount_id']."|{$data['runtime']}|".PHP_EOL;
                if(count($result) == $taskCount){
                    #(new Models\AdAd())->delete();
                    $syncData = ['channel_id'=>$this->id,'account_id'=>array_filter(array_column($adAccounts->toArray(),'account_id'))];
                    (new Models\AdAd())->syncData($syncData,$insertData);
                    #echo \App\Common\Helper::runTime();
                    $this->syncAdAdCategoryByAccountIds($adAccountIds);
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
            //$ads = $adAccount->getAdAds();
            $sdk = $this->getSdk($tokens[$adAccount->token_id??0]);
            go(function() use($adAccount,$sdk,$ii,$chan){
                $sdkDatas = $sdk->getPagesByAdAccountID($adAccount->account_id);
                $result = [];
                if($sdkDatas) foreach($sdkDatas as $sdkData){
                    $_result = [];
                    #$_result['id'] = $sdkData['id'];
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

            if($data['result']) foreach($data['result'] as $v){
                $result[] = $v;
            }

            echo $index."/".$taskCount . " | " . count($result);
            echo PHP_EOL;
            $index++;
            #print_r($data);
            #echo PHP_EOL;
            if($index == $taskCount){
                #print_r($result);
                echo microtime(true) - $starttime;
                echo PHP_EOL;
                echo count($result);
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
        $adDiagnose = new \App\Services\AdDiagnose\Dispatcher();
        foreach($adAccountIds as $ad_account_id){
            $adDiagnose->handle(['ad_account_id'=>$ad_account_id]);
        }
        return;
    }
    /*
     * $params = [
     *    'ad_id' => '',
     *    'insights' => [],
     * ]
     */
    public function adInsightsDiagnose($params=[]){
        if(!$params) return;
        //获取分类
        $ad_ids = array_column($params,'ad_id');
        $catagorys = (new \App\Models\Msdw\DimFbAd())->getCategoryByAdIds($ad_ids);
        #print_r($catagorys);
        $catagoryMap = [];
        foreach($catagorys as $catagory){
            $catagoryMap[$catagory['category1_cn']."_".$catagory['category2_cn']."_".$catagory['category3_cn']] = $catagory;
            foreach($params as &$param){
                if($param['ad_id'] == $catagory['ad_id']){
                    $param['category1_cn'] = $catagory['category1_cn'];
                    $param['category2_cn'] = $catagory['category2_cn'];
                    $param['category3_cn'] = $catagory['category3_cn'];
                }
            }
        }

        $AdIndustryAverageStatsData = (new Models\AdIndustryAverageStats())->getInsights($catagoryMap,['cpm','ctr']);

        #print_r($AdIndustryAverageStatsData);
        foreach($params as &$param){
            $param['industry'] = [];
            $param['diagnose'] = [];
        }
        foreach($AdIndustryAverageStatsData as $adsd){
            foreach($params as &$param){
                if($param['category1_cn'] == $adsd['category1_cn'] && $param['category2_cn'] == $adsd['category2_cn'] && $param['category3_cn'] == $adsd['category3_cn']){
                    $param['industry'] = $adsd;
                    unset($param['industry']['category1_cn'],$param['industry']['category2_cn'],$param['industry']['category3_cn']);
                    foreach($param['insights'] as $k=>$v){
                        $param['insights'][$k] = sprintf("%.2f",$param['insights'][$k]);
                        $param['industry'][$k] = sprintf("%.2f",$param['industry'][$k]);
                        #$param['insights'][$k] = 0.8;
                        $check = $param['insights'][$k] - $param['industry'][$k];
                        if($check > 0){
                            $param['diagnose'][$k] = sprintf("%.2f",$check / $param['industry'][$k] * 100);
                        }else{
                            $param['diagnose'][$k] = sprintf("%.2f",$check / $param['insights'][$k] * 100);
                        }
                    }
                }
            }
        }
        return $params;
    }
}