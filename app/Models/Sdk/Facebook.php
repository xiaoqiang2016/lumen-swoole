<?php
namespace App\Models\Sdk;

use App\Common\Curl;

class Facebook{
    private $token;
    private $batch = false;
    private $batchTasks = [];
    private $apiurl = 'https://graph.facebook.com';
    private $version = '3.2';
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
        #echo PHP_EOL;
        #echo "https://graph.facebook.com/v3.2/me?fields=adaccounts.limit(1000){name,id,amount_spent,spend_cap,account_status,currency,owner,created_time}&access_token=EAAHE0mTSloIBAIjVmFt3NEbmLz1GvIYE5MUhdQqPaK1QJeRvu8whGPJp8DJWTDjTuWuw3gsZAZCBc1zZARE8KPeFfATHopP299Tm31J1aLmHZCJneShLqRgok6TxMG8rUh2lkB9red2RdmWqX6bPNCgJ52ndNlDHnsZCUgoWRnQZDZD";


        return Curl::get($url);
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
}