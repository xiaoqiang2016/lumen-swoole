<?php
namespace App\Models;


class OpenaccountFacebook extends Model{
    protected $table = 't_openaccount_facebook';
    public function notify(){
        if($this->status == 'oe_pending'){

        }
    }
    public function notifySave(){
        $result = $this->save();
        return $result;
    }
}