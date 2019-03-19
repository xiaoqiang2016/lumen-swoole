<?php

namespace App\Http\Channel\Controllers;
use Swoole;
use App;
use App\Services\Channels\Facebook as FacebookService;
class Facebook extends Controller
{
    public $syncAll = 0;
    public function sync($params){
        $this->syncAll = 1;
        $account_id = $params['account_id'];
        $facebook = new App\Repository\Facebook();
        $facebook->account_id = '1634320873332648';
        $facebook->getCampaigns(function($response) use ($facebook){
            $this->result($response);
        });
    }
    public function getCompaigns(){

    }
    public function syncOeRequest(){

    }
    public function syncFbRequest(){
        #\App\Services\Channels\Facebook::syncFbRequest();
    }
    public function getOeLink(){
        $params = $this->getParams();
        $facebook_service = new  \App\Services\Channels\Facebook();
        return $facebook_service->getOeLinkByClientID($params['client_id'],$params['user_id']);
    }
    public function syncOpenAccount(){
        $facebook_service = new  \App\Services\Channels\Facebook();
        $facebook_service->syncOeRequest();
        $facebook_service->syncFbRequest();
        #$result = $facebook_service->getOeLinkByClientID(111);
        #print_r($result);
    }
    public function getVerticalList(){
        $result = \App\Models\FacebookVertical::get(['key','parent_key','level','name_cn','name_en']);
        return $result;
    }
    public function getOpenaccountList(){
        $model = new \App\Models\FacebookOpenAccount();
        $params = $this->getParams();
        $page = $params['page'] ?: 1;
        $page_length = $params['page_length'] ?: 10;
        $client_type = $params['client_type'] ?: 0;
        $fields = $params['fields'] ?: '';
        $client_id = $params['client_id'];
        $status = $params['status'] ?: ''; 
        $map = [];
        $mainFields = ["id as apply_id",'client_id','status','apply_number','bind_bm_id','agent_bm_id','business_license','business_code','address_cn','address_en','business_name_cn','business_name_en','city','state','zip_code','contact_email',
            'contact_name','website','mobile','promotable_urls','promotable_page_ids','promotable_app_ids','timezone_ids','vertical','sub_vertical','change_reasons','facebook_change_reasons'];
        if($client_type == 1){//代理商
            $client_ids = App\Models\Client::where('parent_id','=',$client_id)->get(['id']);
            if(count($client_ids) > 0){
                $client_ids = array_column($client_ids->toArray(),'id');

            }
            $client_ids[] = $client_id;
            $model = $model->whereIn('client_id',$client_ids);
        }else{
            $model = $model->where('client_id','=',$client_id);
        }
        if($_fields = array_filter(explode(",",$fields))){
            $fields = ['id as apply_id'];
            foreach($_fields as $v){
                if(in_array($v,$mainFields)){
                    $fields[] = $v;
                }
            }
        }else{
            $fields = $mainFields;
        }
        if($status){
            $model = $model->where('status','=',$status);
        }

        $list = $model->offset(($page-1)*$page_length)->limit($page_length)->orderby('updated_at','desc')->get($fields);
        $count = $model->count();
        return ['list'=>$list,'count'=>$count];
    }
    /*
     * apply_id
     * client_id
     * apply_number
     * bind_bm_id
     * agent_bm_id
     * business_license
     * business_code
     * address_cn
     * address_en
     * business_name_cn
     * business_name_en
     * city
     * state
     * zip_code
     * contact_email
     * contact_name
     * website
     * promotable_urls
     * promotable_page_ids
     * promotable_app_ids
     * timezone_id
     * sub_vertical
     */
    public function openAccount(){
        $params = $this->getParams();
        FacebookService::openAccount($params);
    }
    public function openAccountAudit(){
        $params = $this->getParams();
        FacebookService::openAccountAudit($params);
    }
}