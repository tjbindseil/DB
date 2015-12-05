<?php
/*Test Functions*/
$routePrefix = 'DB/index.php/';
$router->get($routePrefix.'hello', function(){
	$str = file_get_contents('http://graphical.weather.gov/xml/sample_products/browser_interface/ndfdXMLclient.php?whichClient=NDFDgen&lat=38.99&lon=-77.01&listLatLon=&lat1=&lon1=&lat2=&lon2=&resolutionSub=&listLat1=&listLon1=&listLat2=&listLon2=&resolutionList=&endPoint1Lat=&endPoint1Lon=&endPoint2Lat=&endPoint2Lon=&listEndPoint1Lat=&listEndPoint1Lon=&listEndPoint2Lat=&listEndPoint2Lon=&zipCodeList=&listZipCodeList=&centerPointLat=&centerPointLon=&distanceLat=&distanceLon=&resolutionSquare=&listCenterPointLat=&listCenterPointLon=&listDistanceLat=&listDistanceLon=&listResolutionSquare=&citiesLevel=&listCitiesLevel=&sector=&gmlListLatLon=&featureType=&requestedTime=&startTime=&endTime=&compType=&propertyName=&product=time-series&begin=2015-01-12T00%3A00%3A00&end=2019-12-04T00%3A00%3A00&Unit=e&maxt=maxt&wspd=wspd&Submit=Submit');
	$xml = simplexml_load_string($str);
    print_r ($xml);
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'hash/{password}', function($password){
	return password_hash($password, PASSWORD_BCRYPT);
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'hash/verify/{password}/{hashedPassword}', function($password, $hashedPassword){
	return password_verify($password, $hashedPassword);
}, array('before' => 'statsStart', 'after' => 'statsComplete'));



$router->post($routePrefix.'student/add', function(){
	$con = new StudentController();
	return json_encode($con->addStudent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


$router->post($routePrefix.'student/delete', function(){
	$con = new StudentController();
	return ($con->deleteStudent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


$router->post($routePrefix.'student/update', function(){
	$con = new StudentController();
	return json_encode(($con->updateStudent()));
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


$router->post($routePrefix.'student/getAllStudents', function(){
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
