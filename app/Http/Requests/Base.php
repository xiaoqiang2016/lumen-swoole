<?php
namespace App\Http\Requests;
class Base{
    protected $params;
    public function __construct($params)
    {
        $this->params = $params;
    }
    public function getParams(){
        return $this->params ?? [];
    }
    public function rules(){
        return [];
    }
    public function messages(){
        return [];
    }
    public function attributes(){
        return [];
    }
}