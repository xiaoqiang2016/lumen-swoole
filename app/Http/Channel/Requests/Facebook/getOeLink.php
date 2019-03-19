<?php
namespace App\Http\Channel\Requests\Facebook;
use Illuminate\Validation\Rule;
class getOeLink extends \App\Http\Requests\Base{
    public function rules(){
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
        return $rules;
    }
    public function messages(){
        return [];
    }
    public function attributes(){
        return [
            'client_id' => 'Client ID',
            'user_id' => '用户 ID',
        ];
    }
}