<?php
namespace App\Http\Channel\Requests\Facebook;
use Illuminate\Validation\Rule;
class openAccountAudit extends \App\Http\Requests\Base{
    public function rules(){
        $params = $this->getParams();
        $verticals = \App\Models\FacebookVertical::where("level","=",1)->where("status","=",1)->get(["key"]);
        $verticals = array_column($verticals->toArray(),"key");
        $rules['apply_id'] = [
            'required',
            function($attribute, $value, $fail){
                $r = \App\Models\FacebookOpenAccount::where('id','=',(int)$value)->first(['id','status']);
                if($r === null){
                    return $fail($attribute.' 无效.');
                }
                if(!in_array($r->status,['internal_pending'])){
                    return $fail('当前状态['.$r->status.']不可审核.');
                }
            }
        ];
        $rules['status'] = [
            'required',
            'in:internal_approved,internal_disapproved,internal_changes_requested'
        ];
        $rules['reason'] = ['required_if:status,changes_requested'];
        $rules['sub_vertical'] = [
            'required',
            Rule::in($verticals),
        ];

        return $rules;
    }
    public function attributes(){
        return [
            'status' => '审核状态',
            'reason' => '修改建议',
            'sub_vertical' => '二级行业分类',
        ];
    }
}