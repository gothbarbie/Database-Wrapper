<?php
class Database {
    private static $_instance = null;

    private $_credentials;
    private $_connection;
    private $_table;
    private $_query;
    private $_error = false;
    private $_results;
    private $_count = 0;

    public function __construct()
    {

    }

    private static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Database;
        }
        return self::$_instance;
    }

    private static function connect()
    {
        $this->_connection = new \PDO('mysql:host=' . $host . ';dbname=' . $database, $username, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

        /* Set PDO to use Exceptions for handling errors */
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

}
