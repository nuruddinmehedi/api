<?php
 require_once('db.php');
 require_once('../model/response.php');
 require_once('../model/taskmodel.php');

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


 if($_SERVER['REQUEST_METHOD']==='POST'){
     
   try{
      if($_SERVER['CONTENT_LENGTH']==0 || ($_SERVER['CONTENT_TYPE'] !== 'application/json') ){
         $response = new response;
         $response->setHttpStatusCode(400);
         $response->setSuccess(false);
         $response->addMessage('content type is not supported');
         $response->send();
      exit;
      }
$postData= file_get_contents('php://input');
$hel=json_decode($postData);
print_r($hel);
      if(!$postData=json_decode($postData,true)){

         $response = new response;
         $response->setHttpStatusCode(400);
         $response->setSuccess(false);
         $response->addMessage('content type is not a valid json');
         $response->send();
         exit;

      }
      if(empty($postData['id']) || empty($postData['title']) || empty($postData['description']) || empty($postData['deadline']) || empty($postData['completed'])){
         $response= new response;
         $response->setHttpStatusCode(400);
         $response->setSuccess(false);
         $response->addMessage('formate is not valid');
         $response->send();
         exit;
      }
  
     
     
    try{
     $task= new Task($postData['id'],$postData['title'],$postData['description'],$postData['deadline'],$postData['completed']);
     $response= new response;
     $response->setHttpStatusCode(200);
     $response->setSuccess(true);
     $response->addMessage('success');
     $data= $task->taskArray();
     $query= $writeDbConnection->prepare("insert into tab_task (id, title, description,deadline,completed) values(:id,:title,:description,:deadline,:completed)");
     $query->bindParam(':id',$data['id']);
     $query->bindParam(':title',$data['title']);
     $query->bindParam(':description',$data['description']);
     $query->bindParam(':deadline',$data['deadline']);
     $query->bindParam(':completed',$data['completed']);
     $query->execute();
     $response->send();
    }catch(Exception $e){
     $response= new response;
     $response->setHttpStatusCode(400);
     $response->setSuccess(false);
     $response->addMessage($e->getMessage());
     $response->send();
  
    }
    exit;
   }catch(Exception $e){
  
      $response= new response;
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage($e->getMessage());
      $response->send();
      exit;
  
   }
  }

if($_SERVER['REQUEST_METHOD']==='GET'){
   if(array_key_exists('completed',$_GET)){
   $completed= $_GET['completed'];
  
   try{
      $query= $readDbConnection->prepare('select * from tab_task where completed=:completed');
      $query->bindParam('completed',$completed);
      $query->execute();
      $rowCount= $query->rowCount();
   
      if($rowCount===0){
         $response= new response ;
         $response->setHttpStatusCode(404);
         $response->setSuccess(false);
         $response->addMessage('No data found');
         $response->send();
         exit;
      }
   
      while($row=$query->fetch(PDO::FETCH_ASSOC)){

         $task= new Task($row['id'],$row['title'],$row['description'],$row['deadline'],$row['completed']);
         $data[]= $task->taskArray();

      }

      $responsData['rows']= $rowCount;
      $responsData['tasks']=$data;

      $response= new response;
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->addMessage('success');
      $response->setData($responsData);
      $response->send();

   }catch(Exception $e){
      $response= new response ;
         $response->setHttpStatusCode(500);
         $response->setSuccess(false);
         $response->addMessage($e->getMessage());
         $response->send();
   }

   exit;  
   }

   if(empty($_GET)){
      try{
         $query= $readDbConnection->prepare('select * from tab_task order by id asc');
         $query->execute();
         $rowCount= $query->rowCount();
         if($rowCount===0){
            $response = new response ;
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage('No Data Found');
            $response->send();
            exit;
         }
         while($row= $query->fetch(PDO::FETCH_ASSOC)){

            $task= new Task($row['id'],$row['title'],$row['description'],$row['deadline'],$row['completed']);
            $tasks[]= $task->taskArray();

         }

         $returnData['rows']= $rowCount;
         $returnData['tasks']= $tasks;
         $response= new response;
         $response->setHttpStatusCode(200);
         $response->setSuccess(true);
         $response->setData($returnData);
         $response->addMessage('success');
         $response->send();
         
         exit;
      }catch(Exception $e){
         $response= new response;
         $response->setHttpStatusCode(501);
         $response->setSuccess(false);
         $response->addMessage('server is not responding');
         $response->send();
      }
   }
   
  
   if(array_key_exists('pagenumber',$_GET)){

      try{

         $query= $readDbConnection->prepare('select count(id) as totaltasks from tab_task');
         $query->execute();
         $row= $query->fetch(PDO::FETCH_ASSOC);
         $pageContent= 5;
         $page= $_GET['pagenumber'];
         $totalPageNumber= ceil($row['totaltasks']/$pageContent);

         if($totalPageNumber==0){
            $totalPageNumber=1;
         }

         if($totalPageNumber<$page || $page<=0){

            $response = new response;
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage('No Data Found');
            $response->send();
            exit;

         }

         $offset= ($page==1? 0 : $pageContent*($page-1));
         $query= $readDbConnection->prepare('select * from tab_task limit :pageContent offset :offset');
         $query->bindParam(':pageContent',$pageContent);
         $query->bindParam(':offset', $offset);
         $query->execute();

         $rowCount= $query->rowCount();

         if($rowCount==0){
            $response = new response;
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage('No Data Found');
            $response->send();
            exit;
         }

         while($row=$query->fetch(PDO::FETCH_ASSOC)){
            $task= new Task($row['id'],$row['title'],$row['description'],$row['deadline'],$row['completed']);
            $tasks[]= $task->taskArray();
         }

         $returnData['Return_rows'] = $rowCount;
         $returnData['Total_page']= $totalPageNumber;
         $returnData['page_number']= $page;
         ($page<$totalPageNumber? $returnData['has_next_page']=true: $returnData['has_next_page']=false);
         $returnData['tasks']= $tasks;
       

         $response= new response;
         $response->setHttpStatusCode(200);
         $response->setSuccess(false);
         $response->addMessage('success');
         $response->setData($returnData);
         $response->send();
         exit;


      }catch(Exception $e){
         $response= new response;
         $response->setHttpStatusCode(501);
         $response->setSuccess(false);
         $response->addMessage('server is not responding');
         $response->send();

      }
      exit;
   }


}
 if(array_key_exists('taskid',$_GET)){
     $taskid= $_GET['taskid'];
     if($taskid=='' || !is_numeric($taskid)){
        $response= new response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage('task can not be empty and must be numaric');
        $response->send();
        exit();
     }

    if($_SERVER['REQUEST_METHOD']==='GET'){
      try{
         
         $query= $readDbConnection->prepare("select id, title, description, deadline, completed from tab_task where id=:taskid");
         $query->bindParam(':taskid',$taskid,PDO::PARAM_INT);
         $query->execute();
         $rowCount= $query->rowCount();

         if($rowCount===0){
        $response= new response();
        $response->setHttpStatusCode(404);
        $response->setSuccess(false);
        $response->addMessage('no task found');
        $response->send();
        exit;
         }

         while($row= $query->fetch(PDO::FETCH_ASSOC)){
            $task= new Task($row['id'],$row['title'],$row['description'],$row['deadline'], $row['completed']);
            $taskArray[]= $task->taskArray();
         }

         $returnData['row']= $rowCount;
         $returnData['task']= $taskArray;
         $response= new response;
         $response->setHttpStatusCode(200);
         $response->setSuccess(true);
         $response->setData($returnData);
         $response->addMessage('success');
         $response->send();
      }
      catch(PDOException $e){
         $response= new response;
         $response->setHttpStatusCode(501);
         $response->setSuccess(false);
         $response->addMessage('server is not responding');
         $response->send();
      }


    }
    elseif($_SERVER['REQUEST_METHOD']==='DELETE'){

       try{

         $query= $writeDbConnection->prepare('delete from tab_task where id=:taskid');
         $query->bindParam(':taskid',$taskid,PDO::PARAM_INT);
         $query->execute();
         $rowCount= $query->rowCount();

         if($rowCount===0){
            $response= new response;
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage('Data not Found');
            $response->send();

         }else{
            $response= new response;
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage('successfully delete the task');
            $response->send();

         }
       }catch(Exception $e){
         $response= new response;
         $response->setHttpStatusCode(500);
         $response->setSuccess(false);
         $response->addMessage($e->getMessage());
         $response->send();

       }

    }
    elseif($_SERVER['REQUEST_METHOD']==='PATCH'){

    }else{

      $response= new response;
      $response->setHttpStatusCode(501);
      $response->setSuccess(false);
      $response->addMessage('requist method is not valid');
      $response->send();
    }
 }

?>