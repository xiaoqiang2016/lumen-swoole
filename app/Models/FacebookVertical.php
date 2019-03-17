<?php
namespace App\Models;

class FacebookVertical extends Model
{
    protected $table = 't_facebook_vertical';
    public function getKeyByNameEN($name){
        if(!$name) return '';
        $r = $this->where('name_en','=',$name)->first(['key']);
        if($r === null){
            $result = '';
            echo "FacebookVertical:{$name}未找到";
        }else{
            $result = $r->key;
        }
        return $result;
    }
}