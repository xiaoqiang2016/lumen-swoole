<?php
namespace App\Models;


class User extends Model{
    protected $table = 'sc_user';
    protected $connection = 'facebook';
    protected $primaryKey = 'id';
    public function getClients(){
        return Client::where('user_id',$this->id)->get();
    }
    public function getTokens(){
        $r = UserToken::where('user_id',$this->getID())->get();
        return $r;
    }
    public function getAdAccountBelongsByChannel(int $channel_id){
        #$adaccount_ids = AdAccount::orderBy('balance','desc')->limit(10)->get(['id']);
        #$adaccount_ids = array_column($adaccount_ids->toArray(),'id');
        $r = AdAccountBelong::where('user_id',$this->getID())->where('channel_id',$channel_id)->get();
        return $r;
    }
    public function clients(){
        return $this->hasMany('App\Models\Client','ClientID','client_id');
    }
    public function getID(){
        return $this->id;
    }
}