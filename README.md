Database Wrapper
================
This class handles connection to the database and can be used (by Models for example) to
simplify common database queries.

* Version: 0.1.0
* URI: https://github.com/gothbarbie/database-wrapper
* Author: Hanna Söderström
* E-mail: info@hannasoderstrom.com

## Usage ##
Example of usage (getting all rows from the table 'users' where id is greater than zero):
```php
$users = Database::getInstance()->get('users', array('id', '>', '0'))->results();
```

## Installation ##

### Pre-requisits ###
This class uses the [PDO class](http://php.net/manual/en/class.pdo.php) to handle connections. Make sure your server has this extension installed.

### Composer ###
    `composer install `


## Credit ##
The foundation of this class is based on the Codecourse PDO Wrapper.

Tutorial can be found (and highly recommended!) on [YouTube](https://www.youtube.com/watch?v=3_alwb6Twiw&list=PLfdtiltiRHWF5Rhuk7k4UAU1_yLAZzhWc&index=7).


### Methods ###
*  getInstance() - Instantiate database wrapper
*  query()       - Basic database query
*  action()      - Action (select, insert, delete, update)
*  insert()      - Insert row
*  delete()      - Delete row
*  update()      - Update row
