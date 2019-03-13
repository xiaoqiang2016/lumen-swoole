<?php
namespace App\Http\Services;
use Swoole; 
class Permissions{

   	public function __construct() {

   	}

   public function getPermissions($param) {
   		die(print_r($param));
   }
}