<?php
namespace App\Models;


class OpenaccountFacebook extends Model{
    protected $table = 't_openaccount_facebook';
    public function notify(){
        if($this->status == 'oe_pending'){

        }
    }
    public function notifySave(){
        $this->status = $this->convertStatus($this->remote_status);
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
        $status['oe_disapproved'] = ['oe_disapproved'];
        $status['oe_changes_requested'] = ['oe_changes_requested'];
        $status['oe_pending'] = ['oe_pending'];
        $status['oe_approved'] = ['oe_approved'];
        $status['approved'] = ['approved'];
        $status['disapproved'] = ['disapproved'];
        $status['changes_requested'] = ['changes_requested','requested_change'];
        $status['pending'] = ['pending','under_review'];
        foreach($status as $k=>$v){
            if(in_array($remote_status,$v)){
                return $k;
            }
        }
        echo "转换在状态失败:{$remote_status}".PHP_EOL;
        return '';
    }
}