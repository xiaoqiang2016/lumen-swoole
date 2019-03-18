<?php

namespace App\Http\Controllers;
use Swoole;
use App;
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
        #\App\Services\Channels\Facebook::syncOeRequest();
        \App\Services\Channels\Facebook::syncFbRequest();
    }
    public function getVerticalList(){

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
        #print_r($params);
        echo 123;
    }
}