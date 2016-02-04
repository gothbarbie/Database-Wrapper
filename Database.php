<?php

namespace Gothbarbie;

class Database {
    private static $_instance = null;
    private static $_credentials;

    private $_connection;

    private $_action = '';
    private $_table = '';
    private $_where = '';
    private $_limit = '';

    private $_sql;
    private $_values = [];

    private $_query;

    private $_queryFailed = false;
    private $_results;
    private $_count = 0;
    private $_charEncoding = 'utf8';

    public function __construct()
    {
        self::$_credentials = [
            'host'     => 'localhost',
            'database'   => 'studioKpi',
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

    private function whereIsValid($where)
    {
        $validOperators = ['=', '>', '<', '>=', '<=', '!='];
        if (count($where) === 3)
        {
            if (in_array($where[1], $validOperators)) {
                return true;
            }
        }
        return false;
    }

    // Basic query
    public function query( $sql, $params = [] )
    {
    	$this->_queryFailed = false; // Reset

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
    		}
    	}
    	return $this;
    }

    // If you run query manually, then it has to be executed manually afterwards
    public function run()
    {
        $this->query($this->_sql, $this->_values);
        return $this;
    }

    public function results($fetchStyle = 'assoc')
    {
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

    public function count()
    {
        return $this->_count;
    }

    public function action($action, $table)
    {
        $this->_action = $action;
        $this->_table  = ' FROM ' . $table;
        return $this;
    }

    // Select helper
    public function select($table, $what = '*')
    {
        $this->_action = 'SELECT ' . $what;
        $this->_table = ' FROM ' . $table;
        return $this;
    }

    public function where($where)
    {
        if ($this->whereIsValid($where)) {
            if (empty($this->_where)) {
                $this->_where = ' WHERE ' . $where[0] . ' ' . $where[1] . ' ?';
            } else {
                $this->_where .= ' AND ' . $where[0] . ' ' . $where[1] . ' ?';
            }
            $this->_values[] = $where[2];
            return $this;
        }
    }

    public function orWhere($where)
    {
        if ($this->whereIsValid($where) AND !empty($this->_where)) {
            $this->_where .= ' OR ' . $where[0] . ' ' . $where[1] . ' ?';
            $this->_values[] = $where[2];
            return $this;
        }
    }

    // Adds a limit to the results for the query
    public function limit($rows)
    {
        $this->_limit = ' LIMIT ' . $rows;
    }

    // End method for query builder
    public function get($config = [])
    {
        (!isset($config['sort']))       ? $config['sort']       = 'DESC' : null;
        (!isset($config['fetchStyle'])) ? $config['fetchStyle'] = 'assoc': null;

        // Build query
        $this->_sql = $this->_action . $this->_table . $this->_where . $this->_limit;
        // Run query
        $this->query($this->_sql, $this->_values);
        // Return results
        return $this->results($config['fetchStyle']);
    }

    // Delete helper TODO
    public function delete($table, $where) {
        return $this->action('DELETE', $table)->where($where)->run()->count();
    }
}
