<?php

namespace App\Services\AdDiagnose\Account;
use Swoole;
use App\Models;
class PageStatus extends Base
{
    public $name = "主页状态";
    public $description = "";
    public function handle(){
        $ad_account_id = $this->getParam('ad_account_id');
        $pages = Models\FacebookPage::where('account_id',$ad_account_id)->where('status','!=',1)->get(['page_id','name','status','account_id']);
        $result = [];
        if($pages) foreach($pages as $page){
            $_result = [];
            $_result['account_id'] = $page['account_id'];
            $_result['status'] = $page['status'] == 1 ? 'success' : 'no_publish';

            if($_result['status'] == 'success'){
                $_result['desc'] = "[{$page['name']}:{$page['page_id']}]主页状态正常。";
            }else{
                $_result['desc'] = "[{$page['name']}:{$page['page_id']}]主页未发布或被禁用。";
            }
            $_result['addno'] = ['page_id'=>$page['page_id']];
            $result[] = $_result;
        }
        return $result;
    }
    public function count(){
        $ad_account_id = $this->getParam('ad_account_id');
        return Models\FacebookPage::where('account_id',$ad_account_id)->count();
    }
    public function getDescription(){

    }
}