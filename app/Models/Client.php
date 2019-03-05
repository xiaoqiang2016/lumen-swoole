<?php
namespace App\Models;

class Client extends Model{
    protected $table = 'sc_client';
    protected $connection = 'facebook';
    public function getUserID(){
        return 1006631;
    }
}