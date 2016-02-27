<?php

namespace Gothbarbie\Database;

class Database {
    private static $_instance = null;
    private static $_credentials;

    private $_connection;
    private $_query;

    private $_queryFailed  = false;
    private $_results;
    private $_count        = 0;
    private $_charEncoding = 'utf8';

    public function __construct()
    {
        self::$_credentials = [
            'host'     => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => 'root'
        ];
        $this->connect();
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Database;
        }
        return self::$_instance;
    }

    private function connect()
    {
        $this->_connection = new \PDO('mysql:host=' . self::$_credentials['host'] . ';dbname=' . self::$_credentials['database'],
                                self::$_credentials['username'],
                                self::$_credentials['password'],
                                [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->_charEncoding]);
        // Set PDO to use Exceptions for handling errors
        $this->_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

   /**
    *  whereIsValid()
    *  Validates operators in select-query
    *  @param array $where
    *  @return bool
    */
    private function whereIsValid($where)
    {
        if (count($where) === 3)
        {
            $validOperators = ['=', '>', '<', '>=', '<=', '!='];
            return in_array($where[1], $validOperators);
        }
        return false;
    }

   /**
    *  query()
    *  @param   string  $sql
    *  @param   array   $params
    *  @return  $this
    */
    public function query( $sql, $params = [] )
    {
        // Reset _queryFailed
        $this->_queryFailed = false;

    	if( $this->_query = $this->_connection->prepare($sql) )
        {
            // Bind params
    		if ( count($params) )
            {
    			$x = 1;
    			foreach ( $params as $param ) {
    				$this->_query->bindValue($x, $param);
    				$x++;
    			}
    		}

            // Execute
    		if ( $this->_query->execute() ) {
                $this->_count = $this->_query->rowCount();
    		} else {
    			$this->_queryFailed = true;
                $this->_results = $this->_connection->errorInfo();
    		}
    	}
    	return $this;
    }

   /**
    *  results()
    *  Fetches result from last ran query
    *  @return array
    */
    public function results($fetchStyle = false)
    {
        if ($this->_queryFailed) die('Database: Query failed. Could not fetch results.');

        /* Read more on fetch styles here:
        http://php.net/manual/en/pdostatement.fetch.php */
        switch ($fetchStyle) {
            case 'assoc':
                $as = \PDO::FETCH_ASSOC;
                break;
            case 'both':
                $as = \PDO::FETCH_BOTH;
                break;
            case 'object':
                $as = \PDO::FETCH_OBJ;
                break;
            default:
                $as = \PDO::FETCH_ASSOC;
                break;
        }
        return $this->_query->fetchAll($as);
    }

   /**
    *  count()
    *  Getter for $this->_count
    *  @return int
    */
    public function count()
    {
        return $this->_count;
    }

   /**
    *  select()
    *  Query building helper
    *  @param string $selector
    *  @param string $table
    *  @param array  $whereConditions
    *  @return array
    */
    public function select($selector, $table, $whereConditions = false, $limit = false)
    {
        if ( $this->whereIsValid($whereConditions) ) {
            $limitBy = "";
            if ( is_int($limit) OR is_numeric($limit) ) { $limitBy = "LIMIT {$limit}"; }
            $where = "{$whereConditions[0]} {$whereConditions[1]} ?";
            $values[] = $whereConditions[2];
            $this->query("SELECT {$selector} FROM {$table} WHERE {$where} {$limitBy}", $values);
        } else {
            $limitBy = "";
            if ( is_int($limit) OR is_numeric($limit) ) { $limitBy = "LIMIT {$limit}"; }
            $this->query("SELECT {$selector} FROM {$table} {$limitBy}");
        }
        return $this->results();
    }

   /**
    *  latest()
    *  Query shortcut
    *  Get the last row from selected table (by ID)
    *  @param string @table
    *  @return array
    */
    public function latest($table)
    {
        return $this->query("SELECT * FROM {$table} WHERE id = (SELECT MAX(id) FROM {$table})", [])->results();
    }

   /**
    *  insert()
    *  Query shortcut
    *  Insert record into table
    *  @param string $table
    *  @param array  $fieldsAndValues
    *  @return bool
    */
    public function insert($table, $fieldsAndValues)
    {
        $fields            = "";
        $valuesPlaceholder = "";
        $values            = [];
        $i                 = 1;
        foreach ($fieldsAndValues as $field => $value) {
            $fields            .= $field;
            $valuesPlaceholder .= "?";
            $values[]           = $value;
            if ($i < count($fieldsAndValues)) {
                $fields            .= ", ";
                $valuesPlaceholder .= ", ";
            }
            $i++;
        }
        $this->query("INSERT INTO {$table} ({$fields}) VALUES ({$valuesPlaceholder})", $values);
        return !$this->_queryFailed;
    }

   /**
    *  update()
    *  Query shortcut
    *  @param string $table
    *  @param array  $fieldsAndValues
    *  @param array  $whereConditions
    *  @return bool
    */
    public function update($table, $fieldsAndValues, $whereConditions = false)
    {
        $set = "";
        $values = [];
    	$i = 1;

    	foreach ($fieldsAndValues as $field => $value) {
    		$set .= $field . " = ?";
    		if ($i < count($fieldsAndValues)) {
    			$set .= ", ";
    		}
            $values[] = $value;
    		$i++;
    	}

        $sql = "UPDATE {$table} SET {$set}";

        if ( $this->whereIsValid($whereConditions) ) {
            $sql .= " WHERE {$whereConditions[0]} {$whereConditions[1]} {$whereConditions[2]}";
        }
        $this->query($sql, $values);
        return !$this->_queryFailed;
    }

   /**
    *  delete()
    *  Query shortcut
    *  @param string $table
    *  @param array  $whereConditions
    *  @return bool
    *
    */
    public function delete($table, $whereConditions) {
        if (!$this->whereIsValid($whereConditions)) die("Database: Query failed.");
        $this->query("DELETE FROM {$table} WHERE {$whereConditions[0]} {$whereConditions[1]} {$whereConditions[2]}");
        return !$this->_queryFailed;
    }
}
