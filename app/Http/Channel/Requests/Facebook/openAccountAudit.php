<?php
namespace App\Http\Channel\Requests\Facebook;
use Illuminate\Validation\Rule;
class openAccountAudit extends \App\Http\Requests\Base{
    public function rules(){
        $rules['apply_id'] = [
            'required',
            function($attribute, $value, $fail){
                $r = \App\Models\FacebookOpenAccount::where('id','=',(int)$value)->first(['id','status']);
                if($r === null){
                    return $fail($attribute.' 无效.');
                }
                if(!in_array($r->status,['pending'])){
                    return $fail('当前状态['.$r->status.']不可审核.');
                }
            }
        ];
        $rules['status'] = [
            'required',
            'in:disapproved,approved,changes_requested'
        ];
        return $rules;
    }
    public function attributes(){
        return [
            'status' => '审核状态',
        ];
    }
}