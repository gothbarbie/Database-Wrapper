<?php
require_once 'Database.php';
$db = new Gothbarbie\Database();
$db->getInstance();

//echo '<pre>' , print_r( $db->select('item')->where(['id', '=', '1'])->orWhere(['id', '=', '2'])->get() ) , '</pre>';
echo '<pre>' , print_r( $db->delete('item', ['id', '=', '1']) ) , '</pre>';
