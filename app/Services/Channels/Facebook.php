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
    public static function syncOeRequest(){
        $sdk = (new self())->getSdk();
        $_oeList = $sdk->getOeRequestList();
        $oeList = [];
        if($_oeList){
            foreach($_oeList as $oe){
                $r = [];
                $advertiser_data = $oe['advertiser_data'];
                $r['oe_id'] = $oe['id'];
                $r['remote_status'] = 'first_'.$oe['status'];
                $r['apply_number'] = $advertiser_data['ad_account_number'] ?? 0;
                $r['bind_bm_id'] = $advertiser_data['business_manager_id'];
                $r['business_license'] = $advertiser_data['business_registration'];
                $r['business_code'] = $advertiser_data['business_registration_id'];
                $r['address_cn'] = $advertiser_data['chinese_address'];
                $r['business_name_cn'] = $advertiser_data['chinese_legal_entity_name'];
                $r['city'] = $advertiser_data['city'];
                $r['contact_email'] = $advertiser_data['contact'];
                $r['website'] = $advertiser_data['official_website_url'];
                $r['mobile'] = isset($advertiser_data['phone_number']) ? $advertiser_data['phone_number']['phone_number'] : '';
                $r['mobile_id'] = isset($advertiser_data['phone_number']) ? $advertiser_data['phone_number']['id'] : '';
                $r['promotable_urls'] = $advertiser_data['promotable_urls'] ?? [];
                $r['promotable_page_ids'] = $advertiser_data['promotable_page_ids'] ?? [];
                $r['promotable_app_ids'] = $advertiser_data['promotable_app_ids'] ?? [];
                $r['timezone_id'] = $advertiser_data['time_zone'];
                $r['facebook_user_id'] = $advertiser_data['user_id'] ?? 0;
                $r['zip_code'] = $advertiser_data['zip_code'] ?? '';
                $r['first_remote_created'] = date("Y-m-d H:i:s",strtotime($oe['time_created']));
                $r['first_remote_updated'] = date("Y-m-d H:i:s",strtotime($oe['time_updated']));
                $r['vertical'] = $advertiser_data['vertical'];
                $r['subvertical'] = $oe['subvertical'] ?? '';
                $r['oe_token_id'] = isset($oe['token']) ? $oe['token']['id'] : '';
                $r['first_change_reasons'] = isset($oe['request_changes_reason']) ? $oe['request_changes_reason'] : '';
                $r['request_id'] = isset($oe['ad_account_creation_request_id']) ? $oe['ad_account_creation_request_id']['id'] : '';
                //格式化
                $r['promotable_urls'] = json_encode($r['promotable_urls']);
                $r['promotable_page_ids'] = json_encode($r['promotable_page_ids']);
                $r['promotable_app_ids'] = json_encode($r['promotable_app_ids']);
                $oeList[] = $r;
            }
        }
        $oeRemoteList = array_column($oeList,null,'oe_id');
        $of = new \App\Models\FacebookOpenAccount();
        $existsData = $of->get(['id','oe_id','oe_remote_updated']);
        if(!$existsData->isEmpty()){
            #$existsData = array_column($existsData->toArray(),null,'oe_id');
        }
        if($oeRemoteList){
            foreach($oeRemoteList as $oeRemoteData){
                $oe = false;
                if(!$existsData->isEmpty()){
                    foreach($existsData as $v) if($v->oe_id == $oeRemoteData['oe_id']){
                        $oe = $v;
                        break;
                    }
                }
                //新增
                if(!$oe){
                    $oe = new \App\Models\FacebookOpenAccount();
                    $oe->fill($oeRemoteData)->notifySave();
                }else{
                    //更新
                    if($oeRemoteData['oe_remote_updated'] > $oe->oe_remote_updated){
                        $oe->fill($oeRemoteData)->notifySave();
                    }
                }
                
            }
        }
    }
    //同步Request数据
    public static function syncFbRequest(){ 
        $sdk = (new self())->getSdk();
        $listen_data = ['first_pending','pending','first_approved'];
        #$requestList = \App\Models\OpenaccountFacebook::whereIn('status',$listen_data)->get(['status','oe_id','id','request_id','remote_status']);
        $requestList = \App\Models\FacebookOpenAccount::get(['status','oe_id','id','request_id','remote_status']);
 
        if(!$requestList->isEmpty()){
            foreach($requestList as $request){
                if(!$request->request_id) continue;
                $remote_data = $sdk->getOpenaccountRequestDetail($request->request_id);
                
                if(!$remote_data) continue;
                if(true || $request['remote_status'] != $remote_data['status']){
           
                    $request->sync_updated = date("Y-m-d H:i:s",time());
                    $request->remote_status = $remote_data['status'];
                    $request->apply_number = count($remote_data['ad_accounts_info']);
                    $request->bind_bm_id = '';
                    //$request->business_license = '';
                    $request->business_code = $remote_data['business_registration_id'];
                    $request->address_cn = $remote_data['address_in_local_language'];

                    $address_in_english = $remote_data['address_in_english'] ?? [];
                    $request->address_en = $address_in_english['address_line_1']??'';
                    $request->business_name_cn = $remote_data['chinese_legal_entity_name']??'';
                    $request->business_name_en = $remote_data['english_legal_entity_name']??'';
                    $request->city = $address_in_english['city'] ?? '';
                    $request->state = $address_in_english['state'] ?? '';

                    $contact = $remote_data['contact'] ?? [];
                    $request->contact_email = $contact['email']??'';
                    $request->contact_name = $contact['name']??'';
                    $request->website = $remote_data['official_website_url'];
                    #$request->mobile = $remote_data['official_website_url'];
                    #$request->mobile_id = $remote_data['official_website_url'];
                    $request->promotable_urls = json_encode($remote_data['promotable_urls'] ?? [],JSON_UNESCAPED_UNICODE);
                    $request->promotable_page_ids = json_encode($remote_data['promotable_page_ids'] ?? [],JSON_UNESCAPED_UNICODE);
                    $request->promotable_app_ids = json_encode($remote_data['promotable_app_ids'] ?? [],JSON_UNESCAPED_UNICODE);

                    //$request->timezone_id = 
                    $request->zip_code = $address_in_english['zip']??'';
                    $request->fb_remote_created = date("Y-m-d H:i:s",strtotime($remote_data['time_created']));
                    $request->vertical = $remote_data['vertical'];
                    $request->subvertical = $remote_data['subvertical'];
                    $request->request_change_reasons = json_encode($remote_data['request_change_reasons']??[],JSON_UNESCAPED_UNICODE);
                    $request->notifySave();
                }
                $requestData = [];  
            }
        }
        echo 'syncFbRequest';
    }
    public static function openAccount($params){
        if($params['apply_id'] ?? 0 > 0){
            $openAccount = \App\Models\FacebookOpenAccount::find($params['apply_id']);
            unset($params['apply_id']);
        }else{
            $openAccount = new \App\Models\FacebookOpenAccount();
        }
        $params['remote_status'] = 'first_pending';
        $openAccount->fill($params)->notifySave();
    }
    //开户审核
    public static function openAccountAudit($params){
        $openAccount = \App\Models\FacebookOpenAccount::find($params['apply_id']);
        //oe 审核
        if($openAccount->oe_id){
            $sdk = new \App\Models\Sdk\Facebook();
            $auditParams = [
                'oe_id' => $openAccount->oe_id,
                'status' => $params['status'],
                'vertical' => $openAccount->vertical,
                'sub_vertical' => $openAccount->sub_vertical,
                'agent_bm_id' => $openAccount->agent_bm_id,
                'business_name_en' => $openAccount->business_name_en,
                'reason' => '',
            ];
            $sdk->openAccountAudit($auditParams);
        }else{

        }
    }
}