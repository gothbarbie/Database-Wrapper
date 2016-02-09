<?php
require_once 'Database.php';
$db = new Gothbarbie\Database\Database();
$db->getInstance();

//echo '<pre>' , print_r( $db->delete('item', ['id', '=', '1']) ) , '</pre>';
echo '<pre>' , print_r( $db->latest('item')) , '</pre>';
