<?php
namespace App\Models;


class FacebookOpenAccount extends Model{
    protected $table = 't_facebook_openaccount';
    public function notify(){
        if($this->status == 'oe_pending'){

        }
    }
    private function array2implode($array){
        return is_array($array) ? implode(",",$array) : $array ?? '';
    }
    private function implode2array($array){
        return array_filter(explode(",",$array));
    }
    public function notifySave(){
        $this->setSubVertical($this->sub_vertical);
        $this->status = $this->convertStatus($this->remote_status);
        if(!$this->website &&  $this->promotable_urls[0]) $this->website = $this->promotable_urls[0];
        $this->promotable_urls = $this->array2implode($this->promotable_urls);
        $this->promotable_app_ids = $this->array2implode($this->promotable_app_ids);
        $this->promotable_page_ids = $this->array2implode($this->promotable_page_ids);
        $this->timezone_ids = $this->array2implode($this->timezone_ids);
        $this->account_names = $this->array2implode($this->account_names);
         
        if(!$this->account_names){
            $account_names = [];
            for($i=1;$i<=$this->apply_number;$i++){
                $account_names[] = $this->business_name_cn."_".date("Y-m-d",time())."_".$i;
            }
            $this->account_names = $this->array2implode($account_names);
        }
        if(!$this->business_name_en){
            $this->business_name_en = implode(" ",pinyin($this->business_name_cn));
        }
        
        
        $status_triger_count = json_decode($this->status_triger_count ?: '[]',true);
 
        $this->user_id = $this->user_id ?? 0;
        if(count($status_triger_count??[]) == 0){
            $status_triger_count = [];
            $status_triger_count['internal_disapproved'] = 0;
            $status_triger_count['internal_changes_requested'] = 0;
            $status_triger_count['internal_pending'] = 0;
            $status_triger_count['internal_approved'] = 0;
            $status_triger_count['approved'] = 0;
            $status_triger_count['disapproved'] = 0;
            $status_triger_count['changes_requested'] = 0;
            $status_triger_count['pending'] = 0;
            $status_triger_count['fail'] = 0;
        }
        $status_triger_count[$this->status]++;
        $this->status_triger_count = json_encode($status_triger_count);
        $result = $this->save();
        $hookParams = $this->toArray();
        $hookParams['apply_id'] = $this->id;
        unset($hookParams['id']);
        unset($hookParams['remote_status']);
        unset($hookParams['oe_id']);
        unset($hookParams['request_id']);

        $apply_approve_node = [];
        //审批节点
        if($this->status == 'internal_pending'){
            $internal_pending_count = $status_triger_count['internal_pending'] - 1;
            if($internal_pending_count == $status_triger_count['changes_requested']){
                $apply_approve_node = [1,2];
            }else{
                $apply_approve_node = [1];
            }
        }
        //来源 1:openaccount, 2:sinoclick 3:Facebook OE
        if($this->oe_id){
            $hookParams['application_source'] = 3;
        }
        if($this->source == 'OpenAccount') $hookParams['application_source'] = 1;
        if($this->source == 'SinoClick') $hookParams['application_source'] = 2;
        $application_source = 3;
        $hookParams = [];
        $hookParams['apply_id'] = $this->id; //申请标识唯一ID
        $hookParams['clientId'] = $this->client_id ?? 0; //客户ID
        $hookParams['status'] = $this->status; //当前审批的状态
        $hookParams['apply_approve_node'] = $apply_approve_node; //需要添加的审批节点
        $hookParams['apply_number'] = $this->apply_number;//申请广告账号数量
        $hookParams['planning_agency_business_id'] = $this->agent_bm_id;
        $hookParams['business_management_id'] = $this->bind_bm_id;
        $hookParams['application_source'] = $application_source;
        $hookParams['business_license'] = $this->business_license;
        $hookParams['business_code'] = $this->business_code;
        $hookParams['address_cn'] = $this->address_cn;
        $hookParams['address_en'] = $this->address_en;
        $hookParams['business_name_cn'] = $this->business_name_cn;
        $hookParams['business_name_en'] = $this->business_name_en;
        $hookParams['city'] = $this->city;
        $hookParams['state'] = $this->state;
        $hookParams['zip_code'] = $this->zip_code;
        $hookParams['contact_email'] = $this->contact_email;
        $hookParams['contact_name'] = $this->contact_name;
        $hookParams['website'] = $this->website;
        $hookParams['mobile'] = $this->mobile;
        $hookParams['promotable_urls'] = $this->implode2array($this->promotable_urls);
        $hookParams['promotable_page_ids'] = $this->implode2array($this->promotable_page_ids);
        $hookParams['promotable_app_ids'] = $this->implode2array($this->promotable_app_ids);
        $hookParams['timezones'] = $this->implode2array($this->timezone_ids);
        $hookParams['vertical'] = $this->vertical;
        $hookParams['sub_vertical'] = $this->sub_vertical;
        $hookParams['change_reasons'] = $this->change_reasons;
        $hookParams['facebook_change_reasons'] = $this->facebook_change_reasons;
        #$hookParams['status_triger_count'] = $this->status_triger_count;
        $hookParams['RepId'] = $this->user_id ?? 0;
        $hookParams['account_names'] = $this->implode2array($this->account_names);
        $hookParams['apply_approve_number'] = $this->status_triger_count['internal_pending'] ?? 0;
        $timezones = [];
        #Task::add('openAccountNotify',$hookParams);
        if($hookParams['RepId'] && $hookParams['clientId']) Task::add('openAccountNotify',$hookParams);
        return $result;
    }
    public function convertStatus($remote_status){
        $status = [];
        $status['internal_disapproved'] = ['oe_disapproved'];
        $status['internal_changes_requested'] = ['oe_changes_requested'];
        $status['internal_pending'] = ['oe_pending'];
        $status['internal_approved'] = ['oe_approved'];
        $status['approved'] = ['approved'];
        $status['disapproved'] = ['disapproved'];
        $status['changes_requested'] = ['changes_requested','requested_change'];
        $status['pending'] = ['pending','under_review'];
        $status['fail'] = ['fail'];
        foreach($status as $k=>$v){
            if($k == $remote_status || in_array($remote_status,$v)){
                return $k;
            }
        }
        echo "转换在状态失败:{$remote_status}".PHP_EOL;
        return '';
    }
    //根据二级分类同时设置一级分类
    public function setSubVertical($sub_vertical){
        if(!$sub_vertical) return false;
        $vertical = \App\Models\FacebookVertical::where("key","=",$sub_vertical)->where("status","=",1)->first();
        $this->vertical = $vertical->parent_key;
        $this->sub_vertical = $sub_vertical;
        return true;
    }
}