<?php
namespace App\Http\Channel\Requests\Facebook;
use Illuminate\Validation\Rule;
class openAccount extends \App\Http\Requests\Base{
    public function rules(){
        $params = $this->getParams();
        $rules = [];
        $verticals = \App\Models\FacebookVertical::where("level","=",1)->where("status","=",1)->get(["key"]);
        $verticals = array_column($verticals->toArray(),"key");
        if($params['apply_id'] ?? 0 > 0){
            $rules['apply_id'] = [
                'required',
                function($attribute, $value, $fail){
                    $r = \App\Models\FacebookOpenAccount::where('id','=',(int)$value)->first(['id','status']);
                    if($r === null){
                        return $fail($attribute.' 无效.');
                    }
                    if(!in_array($r->status,['internal_changes_requested','internal_pending','changes_requested'])){
                        return $fail('当前状态不可修改.');
                    }
                }
            ];
        }else{
            $rules['client_id'] = [
                'required',
                function($attribute, $value, $fail){
                    $r = \App\Models\Client::where('id','=',(int)$value)->first(['id']);
                    if($r === null){
                        return $fail($attribute.' 无效.');
                    }
                }
            ];
            $rules['user_id'] = [
                'required',
                function($attribute, $value, $fail){
                    $r = \App\Models\User::where('id','=',(int)$value)->first(['id']);
                    if($r === null){
                        return $fail($attribute.' 无效.');
                    }
                }
            ];
        }
        $rules['apply_number'] = ['required'];
        $rules['business_license'] = ['required','active_url'];
        $rules['business_code'] = ['required'];
        $rules['address_cn'] = ['required'];
        $rules['business_name_cn'] = ['required'];
        $rules['city'] = ['required'];
        $rules['state'] = ['required'];
        $rules['zip_code'] = ['required'];
        $rules['contact_email'] = ['required','email'];
        $rules['contact_name'] = ['required'];
        $rules['promotable_urls'] = ['required','array'];
        $rules['promotable_page_ids'] = ['required_without:promotable_app_ids','array'];
        $rules['promotable_app_ids'] = ['required_without:promotable_page_ids','array'];
        #$rules['ad_account_names'] = ['required','array','size:'.$params['apply_number']];
        $rules['timezone_ids'] = ['required','array','size:'.$params['apply_number']];
        $rules['promotable_urls.*'] = ['active_url','distinct'];
        $rules['promotable_page_ids.*'] = ['numeric','distinct'];
        $rules['promotable_app_ids.*'] = ['numeric','distinct'];
        $rules['bind_bm_id'] = ['numeric'];
        $rules['agent_bm_id'] = ['numeric'];
        $rules['sub_vertical'] = [
            'required',
            Rule::in($verticals),
        ];
        return $rules;
    }
    public function messages(){
        return [];
    }
    public function attributes(){
        return [
            'client_id' => '客户',
            'loginName' => '登录名',
            'password' =>'登录密码',
            'business_license' => '营业执照',
            'business_code' => '营业执照编号',
            'address_cn' => '企业地址(中文)',
            'business_name_cn' => '企业名称(中文)',
            'city' => '城市',
            'state' => '省份',
            'zip_code' => '邮政编码',
            'contact_email' => '联系人邮箱',
            'contact_name' => '联系人姓名',
            'promotable_urls' => '推广url',
            'promotable_app_ids' => '推广app id',
            'promotable_page_ids' => '推广主页',
            'timezone_ids' => '时区',
            'bind_bm_id' => '广告主商务管理平台编号',
            'agent_bm_id' => '代理商商务管理平台编号',
        ];
    }
}