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

 try{


    if($_SERVER['REQUEST_METHOD']==='POST'){

        if($_SERVER['CONTENT_LENGTH']==0 || ($_SERVER['CONTENT_TYPE']!=='application/json')){
            $response = new response;
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('content type is not supported');
            $response->send();
         exit;
        }
    
        $postData= file_get_contents('php://input');
    
        if(!$postData= json_decode($postData,true)){
            $response = new response;
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('content type is not a valid json');
            $response->send();
            exit;
        }
 
    
    if(empty($postData['fristname']) || empty($postData['username']) || empty($postData['password'])){
        $response = new response;
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);
        (empty($postData['fristname'])? $response->addMessage('fristname is empty'): false);
        (empty($postData['username'])? $response->addMessage('username is empty'): false);
        (empty($postData['password'])? $response->addMessage('password is empty'): false);
        $response->send();
        exit;
    }

    if(strlen($postData['fristname'])>255 || strlen($postData['username'])>255 ||strlen($postData['password'])>255){
        $response= new response;
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);
       (strlen($postData['fristname'])>255? $response->addMessage('fristname is too long'): false);
       (strlen($postData['fristname'])==0? $response->addMessage('fristname is too long'): false);
       (strlen($postData['username'])>255? $response->addMessage('username is too long'): false);
       (strlen($postData['username'])==0? $response->addMessage('username is too long'): false);
       (strlen($postData['password'])>255? $response->addMessage('password is too long'):false);
       (strlen($postData['password'])==0? $response->addMessage('password is too long'):false);

       $response->send();

    }


    try{
        $query= $readDbConnection->prepare('select id from tab_user where username=:username');
        $query->bindParam(':username',$postData['username']);
        $query->execute();
        $countRow=$query->rowCount();
        
        if($countRow>0){
            $response = new response;
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage('userName already exist');
            $response->send();
            exit;
        }
        $username= trim($postData['username']);
        $fristname= trim($postData['fristname']);
        $password= trim($postData['password']);
        $password= password_hash($password,PASSWORD_DEFAULT);
        $query= $writeDbConnection->prepare('insert into tab_user(fristname,username,password) values(:fristname,:username,:password)');

        $query->bindParam(':fristname',$fristname,PDO::PARAM_STR);
        $query->bindParam(':username',$username,PDO::PARAM_STR);
        $query->bindParam(':password',$password,PDO::PARAM_STR);
        $query->execute();

        $countRow= $query->rowCount();

        if($countRow==0){
            $response= new response;
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage('somthin went wrong with server');
        $response->send();
        exit;
        }
       $response= new response;
       $response->setHttpStatusCode(200);
       $response->setSuccess(true);
       $response->addMessage('success');
       $response->send();


    }catch(PDOException $e){
        $response= new response;
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit;
    }
        
    

    }

    
 }catch(Exception $e){
    $response= new response;
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage($e->getMessage());
    $response->send();
    exit;

 }