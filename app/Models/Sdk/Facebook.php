<?php
namespace App\Models\Sdk;

use App\Common\Curl;

class Facebook{
    private $token;
    private $batch = false;
    private $batchTasks = [];
    private $apiurl = 'https://graph.facebook.com';
    private $version = '3.2';
    private $bm_id = '630723763692369';
    public function __construct()
    {
        $this->setToken(env('SDK_FACEBOOK_TOKEN'));
    }

    public function getToken(){

        return $this->token;
    }

    public function setToken($token=false){
        if($token !== false) $this->token = $token;
        return $this;
    }

    public function getAccountList(){
        $fields = 'fields=adaccounts.limit(1000){name,id,amount_spent,spend_cap,account_status,currency,disable_reason,created_time,balance}';
        $response = $this->get('me?'.$fields);
        $data = $response['adaccounts']['data']??[];
        return $data;
    }
    public function get($query){
        #echo $query.PHP_EOL;
        $query = $this->parseQuery($query);
        $separate = strpos($query,"?") ? '&' : '?';
        $url = "{$this->apiurl}/v{$this->version}/{$query}{$separate}access_token={$this->token}";
        #echo $url;
        #echo $url.PHP_EOL;
        #echo PHP_EOL;
        #echo "https://graph.facebook.com/v3.2/me?fields=adaccounts.limit(1000){name,id,amount_spent,spend_cap,account_status,currency,owner,created_time}&access_token=EAAHE0mTSloIBAIjVmFt3NEbmLz1GvIYE5MUhdQqPaK1QJeRvu8whGPJp8DJWTDjTuWuw3gsZAZCBc1zZARE8KPeFfATHopP299Tm31J1aLmHZCJneShLqRgok6TxMG8rUh2lkB9red2RdmWqX6bPNCgJ52ndNlDHnsZCUgoWRnQZDZD";


        return Curl::get($url);
    }
    public function post($query,$data=[]){
        $query = $this->parseQuery($query);
        $url = "{$this->apiurl}/v{$this->version}/{$query}";
        $data['access_token'] = $this->token;
        return Curl::post($url,$data);
    }
    public function getAdCampaignListByAdAccountID($account_id){
        $fields = "campaigns.limit(99999){name,objective,created_time,effective_status,id}";
        $query = "{$account_id}?fields={$fields}";
        $response = $this->get($query);
        $result = $response['campaigns']['data']??[];
        return $result;
    }
    public function getAdSetListByAdAccountID($account_id){
        $fields = "adsets.limit(99999){name,daily_budget,created_time,effective_status,id,campaign_id}";
        $query = "{$account_id}?fields={$fields}";
        #echo $query.PHP_EOL;
        $response = $this->get($query);
        $result = $response['adsets']['data']??[];
        return $result;
    }
    public function getAdAdListByAdAccountID($account_id){
        $fields = "ads.limit(999999){name,daily_budget,created_time,effective_status,id,campaign_id,adset_id}";
        $query = "{$account_id}?fields={$fields}";
        #echo $query.PHP_EOL;
        $response = $this->get($query);
        $result = $response['ads']['data']??[];
        return $result;
    }
    public function getInsightsByAdID($ad_id,$params=[]){

        $date = $params['date'] ?? false;
        $fields = 'spend,impressions,cpc,clicks,objective,actions,inline_link_clicks';
        $time_range = [];
        $query = "";
        $query = "{$ad_id}/insights?fields={$fields}";
        if($date){
            $time_range = [
                "since"=>mix($date),
                "until"=>max($date),
            ];

            $time_range = json_encode($time_range);
            $query .=  $query .= "&time_range={$time_range}";
        }

        $response = $this->get($query);
        #echo $query;
        #echo PHP_EOL;
        #print_r($response);
        return $response ?? false;
        #$fields =
    }
    private function parseQuery($query){
        return $query;
    }
    public function getPagesByAdAccountID($account_id){
        $fields = "promote_pages.limit(9999){id,is_published,name}";
        $query = "{$account_id}?fields={$fields}";
        #echo $query.PHP_EOL;
        $response = $this->get($query);
        $result = $response['promote_pages']['data']??[];
        return $result;
    }
    public function getOeRequestList(){
        $query = '630723763692369/resellervettingrequests';
        $response = $this->get($query);
        $result = $response['data'];
        return $result;
    }
    public function getOpenaccountRequestDetail($request_id){
        $query = "{$request_id}?fields=status,is_test,id,english_legal_entity_name,credit_card_id,contact,disapproval_reasons,chinese_legal_entity_name,business,creator,appeal_reason,address_in_local_language,extended_credit_id,advertiser_business,address_in_chinese,official_website_url,oe_request_id,legal_entity_name_in_local_language,is_under_authorization,is_smb,planning_agency_business,business_registration_id,address_in_english,additional_comment,planning_agency_business_id,promotable_app_ids,promotable_page_ids,promotable_urls,request_change_reasons,subvertical,ad_accounts_info,time_created,ad_accounts_currency,vertical,adaccounts.limit(100){account_id,account_status,name,timezone_id,timezone_name}";
        $response = $this->get($query);
    
        $result = $response ?? [];
        return $result;
    }
    public function openAccountAudit($params){
        $oe_id = $params['oe_id'];
        $status = $params['status'];
        $vertical = $params['vertical'];
        $sub_vertical = $params['sub_vertical'];
        $agent_bm_id = $params['agent_bm_id'] ?? '';
        $business_name_en = $params['business_name_en'] ?? '';
        $reason = $params['reason'] ?? '';
        $query = "{$oe_id}";
        $params = [];
        $status = strtoupper($status);
        $params['Status'] = $status;
        $params['Payment'] = 'EXTENDED_CREDIT';
        $params['SpendLimit'] = '0.01';
        $params['Vertical'] = $vertical;
        $params['Subvertical'] = $sub_vertical;
        if($agent_bm_id) $params['PlanningAgencyBusinessID'] = $agent_bm_id;
        if($business_name_en) $params['EnglishBusinessName'] = $business_name_en;
        if($reason) $params['Reason'] = $reason;
        $response = $this->post($query,$params);
        return $response;
    }
    public function getOeToken(){
        $query = "{$this->bm_id}/china_business_onboarding_attributions";
        $response = $this->post($query,[]);
        return $response;
    }
}