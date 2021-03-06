Database Wrapper
================
This class handles connection to the database and can be used (by Models for example) to
simplify common database queries.

* Version: 0.2.0
* URI: https://github.com/gothbarbie/database-wrapper
* Author: Hanna Söderström
* E-mail: info@hannasoderstrom.com

## Usage ##

### SELECT ###
*SELECT * FROM users WHERE id > 0*
```php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();
$results = $db->select('*', 'users', ['id', '>', '0']);
```

### SELECT (with LIMIT by 5)###
*SELECT * FROM users WHERE id > 0 LIMIT 5*
```php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();
$results = $db->select('*', 'users', ['id', '>', '0'], 5);
```

### LATEST ###
*SELECT * FROM users WHERE id = (SELECT MAX(id) FROM users)*
```php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();
$results = $db->latest('users');
```

### INSERT ###
*INSERT INTO users (username, email) VALUES ("user", "user@email.com")*
```php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();
$db->insert('users', ['username' => 'user', 'email' => 'user@email.com']);
```

### UPDATE ###
*UPDATE users SET username = "user2", email = "user2@email.com" WHERE username = "user"*
```php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();
$db->update('users', ['username' => 'user2', 'email' => 'user2@email.com'], ['username', '=', 'user']);
```

### DELETE ###
*DELETE FROM users WHERE username = "user2"*
```php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();
$db->delete('users', ['username', '=', 'user2']);
```

## Installation ##

### Pre-requisits ###
This class uses the [PDO class](http://php.net/manual/en/class.pdo.php) to handle connections. Make sure your server has this extension installed.

## Credit ##
The foundation of this class is based on the Codecourse PDO Wrapper.

Tutorial can be found (and highly recommended!) on [YouTube](https://www.youtube.com/watch?v=3_alwb6Twiw&list=PLfdtiltiRHWF5Rhuk7k4UAU1_yLAZzhWc&index=7).


### Methods ###
*  getInstance() - Instantiate database wrapper
*  query()       - Lets you run any database query
*  results()     - Returns result of last SELECT query
*  count()       - Returns number of affected rows from last query
*  select()      - Shortcut to SELECT from table
*  insert()      - Shortcut to INSERT into table
*  delete()      - Shortcut to DELETE from table
*  update()      - Shortcut to UPDATE on table
