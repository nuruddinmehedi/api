<?php

class TaskModelException extends Exception{}

class Task {

private $_id;
private $_title;
private $_description;
private $_deadline;
private $_completed;
private $dataArray= array();

public function __construct($id,$title,$description,$deadline,$completed){
    $this->setId($id);
    $this->setTitle($title);
    $this->setDescription($description);
    $this->setCompleted($completed);
    $this->setDeadline($deadline);
}

public function getId(){
    return $this->_id;
}
public function getTitle(){
    return $this->_title;
}
public function getDiscription(){
    return $this->_descriptiuon;
}

public function getDeadline(){
    $this->_deadline;
}

public function getCompleted(){
    $this->_completed;
}

public function setId($id){

    if(($id == null) && (is_numeric($id) || 0<$id || $id>9223372036854775807 || $this->_id !==null)){
       throw new TaskModelException("Id formate is not valid");
    }
     $this->_id = $id;
}

  public function setTitle($title){
    if($title == null || strlen($title)<=0 || strlen($title)>255 || $this->_title !== null ){
        throw new TaskModelException("Title formate is not valid");
    }

    $this->_title= $title;
}

public function setDescription($description){
    if($description==null || strlen($description)<=0 || strlen($description)>16777215 || $this->_description !== null){
        throw new TaskModelException('Description formate is not valid');
    }

    $this->_description= $description;


}
public function setDeadline($deadline){

    if($deadline==null){
        throw new TaskModelException('deadline formate is not valid');
    }
    $this->_deadline = $deadline;

}
public function setCompleted($completed){
    if(strtoupper($completed) !== "Y" && strtoupper($completed) !== "N" ){

        throw new TaskmodelException('completed formate is not valid');
    }
    $this->_completed= strtoupper($completed);

}


public function taskArray(){

 $this->_dataArray['id']= $this->_id;
 $this->_dataArray['title']= $this->_title;
 $this->_dataArray['description']= $this->_description;
 $this->_dataArray['deadline']= $this->_deadline;
 $this->_dataArray['completed']= $this->_completed;
 
 return $this->_dataArray;

}


}


?>