<?php
// TODO: login and logout need work

class UserController{
		
	private $id;
	private $fName; // 0 = fratmember, 1 = chef, 2 = admin
	private $lName;
	private $studentID;
	private $locationID;
	
	public function __construct() {
		$this->studentModel = new studentModel();
	}
	
	public function __destruct() {
		// ensure that the UserModel destructor gets called to properly
		// close the database connection
		$this->studentModel = null;
	}
	/*
	public function login() {
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->login($arrValues['username'], $arrValues['password']);
		if($arrResult['success']) {
			$arrUser = $arrResult['userInfo'];
			$this->username = $arrUser['username'];
			$this->userRole = $arrUser['userRole'];
			$this->loggedIn = true;
		}
		else {
			$this->loggedIn = false;
			$this->username = "";
			$this->userRole = -1;
			print_r($arrResult);
		}
	}
	
	public function logout() {
		$this->loggedIn = false;
		$this->username = "";
		$this->userRole = -1;
	}
	
	public function addUser(){
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrValues['email'] = $_REQUEST['email'];
		$arrValues['userRole'] = $_REQUEST['userRole'];
		$arrResult = $this->userModel->addUser($arrValues);
		if($arrResult['success']) {
			//successfully added user
		}
		else {
			//there was an error
			print_r($arrResult);
		}
	}
	
	public function deleteUser() {
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->deleteUser($arrValues['username'], $arrValues['password']);
		if($arrResult['success']) {
			//successfully added user
		}
		else {
			//there was an error
			print_r($arrResult);
		}
	}
	*/
	
	public function getAllStudents() {
		$arrResult = $this->studentModel->getAllStudents();
		if($arrResult['success']) {
			// successfully added student
		}
		else {
			// there was an error
			print_r($arrResult);
		}
		return json_encode($arrResult);
	}
	
	public function updateStudent() {
		$arrValues = array();
		foreach ($_REQUEST as $key => $value) { /// can i do this?
			$arrValues[$key] = $value;
		}
		$arrResult = $this->studentModel->updateStudent($arrValues); // may need to specify specific indices of $arValues
		if($arrResult['success']) {
			// successfully added student
		}
		else {
			// there was an error
			print_r($arrResult);
		}
		return json_encode($arrResult);
	}
	
	public function deleteStudent() {
		$id = $_REQUEST['id'];
		$arrResult = $this->studentModel->deleteStudent($id); // may need to specify specific indices of $arValues
		if($arrResult['success']) {
			// successfully added student
		}
		else {
			// there was an error
			print_r($arrResult);
		}
		return json_encode($arrResult);
	}
	
	public function addStudent() {
		$arrValues = array();
//		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['fName'] = $_REQUEST['fName'];
		$arrValues['lName'] = $_REQUEST['lName'];
		$arrValues['studentID'] = $_REQUEST['studentID'];
		$arrValues['town'] = $_REQUEST['town'];
		$arrValues['state'] = $_REQUEST['state'];
		$arrResult = $this->studentModel->addStudent($arrValues); // may need to specify specific indices of $arValues
		if($arrResult['success']) {
			// successfully added student
		}
		else {
			// there was an error
			print_r($arrResult);
		}
		return json_encode($arrResult);
	}
	


?>
