<?php

require_once('db.php');
require_once('../model/response.php');

try{
    $writeDbConnection=  Db::dbWriteConnection();
    $readDbConnection= Db::dbReadConnection();

}
catch(PDOException $e){
   error_log('connection error'.$e->getMessage(),0);
   $response= new response();
   $response->setHttpStatusCode(500);
   $response->setSuccess(false);
   $response->addMessage('db connection error');
   $response->addMessage($e->getMessage());
   $response->send();
   exit();
}


if(array_key_exists('sessionid',$_GET)){

    if($_SERVER['REQUEST_METHOD']!=="POST"){
        

    }

}elseif(empty($_GET)){

}else{
    $response= new response;
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage('Endpint not found');
    $response->send();

}



?>