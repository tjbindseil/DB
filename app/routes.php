<?php
/*Test Functions*/
$routePrefix = 'DB/index.php/';
$router->get($routePrefix.'hello/{name}', function($name){
    return 'Hello ' . $name;
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'helloworld', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'student/add', function(){
	$con = new StudentController();
	return ($con->addStudent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


$router->post($routePrefix.'student/delete', function(){
	$con = new StudentController();
	return ($con->deleteStudent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


$router->post($routePrefix.'student/update', function(){
	$con = new StudentController();
	return ($con->updateStudent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


$router->post($routePrefix.'student/getAll', function(){
	$con = new StudentController();
	return ($con->getAllStudents());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
//INSERT/UPDATE and INSERT/UPDATE-like API calls
/*
Simple insert/update
	inserts or updates row in table with attributes objectArr
	if identifier in objectArr exists in table, update existing row with values in objectArr
	if not, insert new row with values in objectArr and auto-generated identifier
*/
$router->post($routePrefix.'post/{table}/{objectArr}', function($table, $objectArr){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
//DELETE and DELETE-like API calls
/*
Simple Delete
	deletes row in table by id
*/
$router->delete($routePrefix.'delete/{table}/{id}', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
?>