<?php
namespace App\Repositories\Channel;
class Facebook{
    private $token = 'CAAUibl40bIcBAJkRAVZBTk8NkN4U36hrmRpUE4uyR3txrmzmKTybxRSSZBBMz3VNDZABKtXbbbZAqiGUFUz6pmJ0ZA2jbBLrioPEoz4sm1FYjmakfeOKfZCQOnzIZCOyqIZBTaXJaRWC8b0kb1v2lrVUHye9m7uq8F9dCOTJZBuz1Lq61HZCmhsypslk1ZA0aRqjT8ZD';
    private $version = '3.2';
    public function getCampaigns($params){
        $account_id = $params['account_id'];
        $refresh = @$params['refresh'] ?: 0;
        $refresh = 1;
        if($refresh == 1){
            \App\Models\AdCampaign::where(['account_id'=>$account_id])->delete();
            $data = $this->pullCampaigns($account_id);
            (new \App\Models\AdCampaign())->addAll($data);
        }
    }
    public function pullCampaigns($account_id){
        $account_id = $account_id;
        $query = "/{$account_id}?fields=campaigns.limit(9999)".$this->encode("{name,start_time,stop_time,objective,status,created_time,effective_status}");

        $result = $this->send($query);
        $result = $result['campaigns']['data'];
        $saveData = [];
        foreach($result as $r){
            $data = [];
            $data['id'] = $r['id'];
            $data['name'] = $r['name'];
            $data['objective'] = $r['objective'];
            $data['start_time'] = date("Y-m-d H:i:s",strtotime($r['start_time']));
            $data['created_time'] = date("Y-m-d H:i:s",strtotime($r['created_time']));
            $data['status'] = $r['effective_status'];
            $data['channel'] = 'Facebook';
            $saveData[] = $data;
        }

        return $saveData;
    }
    private function encode($str){
        return urlencode($str);
    }
    private function send($query){
        $query .= "&access_token=".$this->token;
        $host = "https://graph.facebook.com";
        $version = "/v".$this->version;
        $url = $host.$version.$query;
        echo $url."\n";
        $result = \App\Common\Curl::exec($url);
        return $result;
    }
}