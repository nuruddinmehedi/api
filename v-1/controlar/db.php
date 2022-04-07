<?php

require_once('config.php');

class Db{

    private static $_hostName;
    private static $_dbName;
    private static $_userName;
    private static $_password;

    public static $_writeDbConnection;
    public static $_readDbConnection;

    public static function setvar(){
        self::$_hostName= HOSTNAME;
        self::$_dbName= DBNAME;
        self::$_userName= USERNAME;
        self::$_password= PASSWORD;

    }

    public static function dbWriteConnection(){
        self::setvar();
        if(self::$_writeDbConnection==null){
            self::$_writeDbConnection= new PDO('mysql:host='.self::$_hostName.';dbname='.self::$_dbName.';charset=utf8',self::$_userName,self::$_password);
            self::$_writeDbConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            self::$_writeDbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);


            
        }

        return self::$_writeDbConnection;

    }

    public static function dbReadConnection(){
        self::setvar();
        if(self::$_readDbConnection==null){
            self::$_readDbConnection = new PDO('mysql:host='.self::$_hostName.';dbname='.self::$_dbName.';charset=utf8',self::$_userName,self::$_password);
            self::$_readDbConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            self::$_readDbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        }

        return self::$_readDbConnection;

    }


}


?>