<?php

require_once('db.php');
require_once('../model/response.php');


try{
 $wrobj=db::dbReadConnection();
 $rdobj=db::dbWriteConnection();
}
catch(PDOException $e){
$obj= new response;
$obj->setHttpStatusCode(500);
$obj->setSuccess(false);
$obj->addMessage($e->getMessage());
$obj->addMessage('error from server');
$obj->send();
}


?>