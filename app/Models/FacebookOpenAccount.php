<?php
namespace App\Models;


class FacebookOpenAccount extends Model{
    protected $table = 't_facebook_openaccount';
    public function notify(){
        if($this->status == 'oe_pending'){

        }
    }
    public function notifySave(){
        $this->setSubVertical($this->sub_vertical);
        $this->status = $this->convertStatus($this->remote_status);
        $this->promotable_urls = is_array($this->promotable_urls) ? implode(",",$this->promotable_urls) : $this->promotable_urls;
        $this->promotable_app_ids = is_array($this->promotable_app_ids) ? implode(",",$this->promotable_app_ids) : $this->promotable_app_ids;
        $this->promotable_page_ids = is_array($this->promotable_page_ids) ? implode(",",$this->promotable_page_ids) : $this->promotable_page_ids;
        $result = $this->save();
        $hookParams = $this->toArray();
        $hookParams['apply_id'] = $this->id;
        unset($hookParams['id']);
        unset($hookParams['remote_status']);
        unset($hookParams['oe_id']);
        unset($hookParams['request_id']);
        Task::add('WebHook.openAccountNotify',$hookParams);
        #print_r($this->toArray());
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
        foreach($status as $k=>$v){
            if($k == $v || in_array($remote_status,$v)){
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