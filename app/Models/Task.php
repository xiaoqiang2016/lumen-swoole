<?php
namespace App\Models;


class Task extends Model{
    protected $table = 't_task';
    public static function add($action,$params){
        $t = (new self());
        $t->action = $action;
        $t->params = json_encode($params,JSON_UNESCAPED_UNICODE);
        $t->exec_at = date("Y-m-d H:i:s",time());
        $t->status = 'wait';
        $t->retry = 0;
        $t->log = json_encode([],JSON_UNESCAPED_UNICODE);
        $t->save();
    }
}