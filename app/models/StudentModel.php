<?php
	// id | username | password | email | userRole | orgId
	//TODO: get true/false if user_id is in org_id
class StudentModel{
			
	private $dbo;
	private $arr;
	
	 public function __construct() {
			$db = new DB_Connections();
			$arr = $db->getNewDBO();
			$this->dbo = $arr['DBO'];
			$this->arr = $arr;
	 }
	 
	 public function hello() {
		 return $this->arr;
	 }

	public function __destruct() {
		$this->dbo = null;
	}
	
	public function getAllStudents() {
		$arrResult = array();
		$success = false; 
		try {
			$STH = $this->dbo->prepare("SELECT * FROM students as S INNER JOIN location as L ON S.locationID = L.id");
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'][] = true;
		}
		$arrResult['success'] = $success;
		$arrResult['fetchStudentsArr'][] = $fetch;
		return $arrResult;
	}
	
	
	public function updateStudent($arrValues) {
		$arrResult = array();
		$arrResult['sql'] = array();
		$id = $arrValues['id']; // not changable
	/*	$fName = $arrValues['fName'];
		$lName = $arrValues['lName'];    ALL COULD THROW EXCEPTIONS
		$studentID = $arrValues['studentID'];    
		$town = $arrValues['town'];
		$state = $arrValues['state'];
		*/
		$success = false;
		$sql = "UPDATE students SET ";
		$index = 0;
		$data = array();
		$needToBeUpdated = false;

		if (isset($arrValues['fName'])) {
			 $fName = $arrValues['fName'];
			 $sql = $sql . "fName=?, ";
			 $data[$index] = $fName;
			 $index = $index + 1;
			 $needToBeUpdated = true;
		}
		if (isset($arrValues['lName'])) {
			 $lName = $arrValues['lName'];
			 $sql = $sql . "lName=?, ";
			 $data[$index] = $lName;
			 $index = $index + 1;		
			 $needToBeUpdated = true;
		}
		if (isset($arrValues['studentID'])) { // can you compare ints?
			 $studentID = $arrValues['studentID'];
			 $sql = $sql . "studentID=?, ";
			 $data[$index] = $studentID;
			 $index = $index + 1;
			 $needToBeUpdated = true;
		}
		
		if ($needToBeUpdated) {
			
			//get rid of last two chars in sql string
			$sql = substr($sql,0,-2);
			$sql = $sql . " WHERE id=?";
			$data[$index] = $id;
			$arrResult['sql'][] = $sql;
			try {
				 $STH = $this->dbo->prepare($sql);
				 $arrResult['db_result'] = $STH->execute($data); //?
				 $success = true;
			} catch (Exception $e) {
				$arrResult['error'] = $e->getMessage();
				$success = false;
			}	
			// have to do different stuff for town and state because in location table
			
		}
		
		if(isset($arrValues['town']) || isset($arrValues['state'])) {
			$sql = "SELECT locationID FROM students where id=:id";
			try {
				$STH = $this->dbo->prepare($sql);
				$STH->bindParam(":id", $id);
				$STH->execute();		
				$fetch = $STH->fetch(PDO::FETCH_ASSOC);   // fetch has location id
			} catch (Exception $e) {
				$arrResult['success'] = false;
				$arrResult['error'][] = $e->getMessage();
			}
			$arrResult['sql'][] = $sql;
			
			$locationID = $fetch['locationID'];
			$sql = "UPDATE location SET ";
			$index = 0;
			$data = array();
			
			if (isset($arrValues['town'])) {
				 $town = $arrValues['town'];
				 $sql = $sql . "town=?, ";
				 $data[$index] = $town;
				 $index = $index + 1;
			}
			if (isset($arrValues['state'])) {
				 $state = $arrValues['state'];
				 $sql = $sql . "state=?, ";
				 $data[$index] = $state;
				 $index = $index + 1;
			}
			
			//get rid of last two chars in sql string
			$sql = substr($sql,0,-2);
			$sql = $sql . " WHERE id=?";
			$data[$index] = $locationID;
			$arrResult['sql'][] = $sql;
			try {
				 $STH = $this->dbo->prepare($sql);
				 $arrResult['db_result'] = $STH->execute($data); 
				 $success = true;
			} catch (Exception $e) {
				$arrResult['error'] = $e->getMessage();
				$success = false;
			}	
		}	
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
	
	public function deleteStudent($id) {
		$arrResult = array();
		$success = false;
		try { //// do i need array right below this comment?
			$STH = $this->dbo->prepare("DELETE FROM students WHERE id =:id");
			$STH->bindParam(":id", $id);
			$STH->execute();
			$success = true;
		} catch (Exception $e) {
			$succes = false;
			$arrResult['error'][] = $e->getMessage();
		}
		$arrResult['succes'] = $succes;
		return $arrResult;
	}
	
	public function addStudent($arrValues) {
		$arrResult = array();
		$success = false;
//		$id = $arrValues['id']; // may not need thanks to AI
		$fName = $arrValues['fName'];
		$lName = $arrValues['lName'];
		$studentID = $arrValues['studentID'];
		//$locationID = $arValues['locationID']; must use town and state incase location is not in table
		// checking if user is in database
		$town = $arrValues['town'];
		$state = $arrValues['state'];
		$boolValidUsername = false;
		try {
			$STH = $this->dbo->prepare("SELECT * FROM students where studentID=:studentID"); // why not "$" dbo?
			$STH->bindParam(":studentID", $studentID);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC); // should do the same as fetch because max 1 user
			if(count($fetch) > 1) {
				// user exists and $fetch is non empty
				$boolValidUsername = false;
				$arrResult['error'][] = "the user is already in the database";
			} else {
				$boolValidUsername = true;
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$boolValidUsername = false;
		}
		// finished checking db for user
		if (!boolValidUsername) {
			$arrResult['success'] = false;
			return $arrResult;
		}
		// method would have returned if problems occured, so now time to add in
		// but first we need to get location id
		$arrLocation = $this->getLocationID($town, $state);
		$locationID = $arrLocation['id'];
		$arrResult['loc'] = $locationID;
		try {
			$data = array('fName' => $fName, 'lName' => $lName, 'studentID' => $studentID, 'locationID' => $locationID);
			$STH = $this->dbo->prepare("INSERT INTO students VALUES (NULL, :fName, :lName, :studentID, :locationID)"); // concatinating on a new line?
			$STH->execute($data);
			$success = true;
		} catch(Exception $e) {
			$success = false;
			$arrResult['error'][] = $e->getMessage() . " sdfgsdfgsd";
		}
		$arrResult['success'] = $success;
		// can add further values for debug
		return $arrResult;
	}
	
	private function getLocationID($town, $state) {
		$arrResult = array();
		$arrResult['success'] = true;
		try {
			$STH = $this->dbo->prepare("SELECT * FROM location where town=:town AND state=:state");
			$STH->bindParam(":town", $town);
			$STH->bindParam(":state", $state);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			$succes = true;
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$arrResult['success'] = false;
		}
		if (count($fetch) > 1) {
			$arrResult['id'] = $fetch['id']; // might need specific index of fetch
		} else {
			try {
				$data = array('town' => $town, 'state' => $state);
				$STH = $this->dbo->prepare("INSERT INTO location VALUES (NULL, :town, :state)");
				$STH->execute($data);
			} catch (Exception $e) {
				$arrResult['error'][] = $e->getMessage();
				$arrResult['success'] = false;
			} // now location has been inserted
			$table = "location";
			$mostRecent = $this->getMostRecent($table);
			$arrResult['id'] = $mostRecent['id'];
		}
		return $arrResult;
	}
	private function getMostRecent($table) {
		$arrResult = array();
		try {
			$STH = $this->dbo->prepare("SELECT * FROM ".$table." ORDER BY id Desc Limit 1"); 
			// ???? $STH->bindParam()
			$STH->execute();
		} catch (Exception $e) {
			$arrResult['success'] = false;
			$arrResult['error'][] = $e->getMessage();
		}
		$fetch = $STH->fetch(PDO::FETCH_ASSOC);
		$arrResult['id'] = $fetch['id'];
		return $arrResult;
	}
	
}




	/**
		expected input: 
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'error' => array of errors that occurred
		'success' => true if user was successfuly added, false otherwise
		);
	
	public function addUser($arrValues) {
		// first we check if username already exists
		$arrResult = array();
		$success = false;
		$username = $arrValues['username'];
		$hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
		$email = $arrValues['email'];
		$userRole = $arrValues['userRole'];
		$orgId = $arrValues['orgId'];
		$arrResult['error'] = array();
		// see if username has been used already
		$boolValidUsername = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				// username exists in the db
				$boolValidUsername = false;
				$arrResult['error'][] = "the username already exists";
			}
			else {
				// username is available
				$boolValidUsername = true;
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		if(!$boolValidUsername) {
			$arrResult['success'] = false;
			return $arrResult;
		}
		// we have a valid username. So lets add it to the db
		 try {
			$data = array( 'username' => $username, 'password' => $hashedPassword, 'email' => $email, 'orgId' => $orgId, 'userRole' => $userRole);
			$STH = $this->dbo->prepare("INSERT INTO user VALUES (NULL, :username, :password, :email, :userRole, :orgId)");
			$STH->execute($data);
			$success = true;
			// TODO: now, based on the userRole, insert a new record into: member_info, chef_info, or admin_info
				//use same error checks as with the above insert query
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'][] = $e->getMessage();
		}
		// just send some stuff back to caller for debug
		$arrResult['success'] = $success;
		// below is for debug
		$arrResult['username'] = $username;
		$arrResult['hashed_password'] = $hashedPassword;
		$arrResult['email'] = $email;
		$arrResult['userRole'] = $userRole;
		return $arrResult;	
	}
	
	/**
		expected input: username and password pair
		
		output:
		$arrResult = array (
		'error' => exception object error message
		'success' => true if user was successfuly removed from db, false otherwise
		);
	
	public function deleteUser($username,$password) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(password_verify($password,$fetch['password'])){ //TODO: or if admin is deleting a user
				$STH = $this->dbo->prepare("DELETE FROM user WHERE username=:username");
				$STH->bindParam(":username", $username);
				$STH->execute();	
				$success = true;
			} else {
				$success = false;
				$arrResult['error'] = "not authorized to delete this acct";
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	// id | username | password | email | userRole | orgId
	/**
		expected input: => values not being changed must be set to empty string
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'error' => exception object for query attempt
		'success' => true if successfully eddited, false otherwise
		);
	
	public function editUser($arrValues) {
			 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $username = $arrValues['username'];
	 $hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
	 $email = $arrValues['email'];
	 $userRole = $arrValues['userRole'];
	 $orgId = $arrValues['orgId'];
	 $sql = "UPDATE user SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($username, "") != 0) {
		 $sql = $sql . "username=?, ";
		 $data[$index] = $username;
		 $index = $index + 1;
	 }
	 if(strcmp($hashedPassword, "") != 0) {
		 $sql = $sql . "password=?, ";
		 $data[$index] = $hashedPassword;
		 $index = $index + 1;
	 }
	 if(strcmp($email, "") != 0) {
		 $sql = $sql . "email=?, ";
		 $data[$index] = $email;
		 $index = $index + 1;
	 }
	 if(strcmp($userRole, "") != 0) {
		 $sql = $sql . "userRole=?, ";
		 $data[$index] = $userRole;
		 $index = $index + 1;
	 }
	  if(strcmp($orgId, "") != 0) {
		 $sql = $sql . "orgId=?, ";
		 $data[$index] = $orgId;
		 $index = $index + 1;
	 }
	 // get rid of the last two characters
	 $sql = substr($sql,0,-2);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
	/**
		expected input: username and password pair
		
		output:
		$arrResult = array (
		'error_message' => invalid username and password pair
		'error' => exception object for first query attempt
		'userInfo' => the assoc array representing the users record in the db
		'success' => true if user was successfuly added, false otherwise
		);
	
	public function login($username, $password) {
		$success = false;
		$arrResult = array();	
		$arrResult['error_message'] = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
		//	print_r($fetch);
			if(is_array($fetch)) {
				$hashedPassword = $fetch[0]['password'];
//				echo $hashedPassword;
				if(password_verify($password, $hashedPassword)) {
				// username exists in the database and pw hash compare returned true
				$arrResult['userInfo'] = $fetch[0]; // not sure what to return. just putting this here for now
				// find info specific to this type of user
				switch($fetch[0]['userRole']){
					case 0: //member
						//query user_info table and assign to ['member_info']
						break;
					case 1: //chef
						//query chef_info table and assign to ['chef_info']
						break;
					case 2: //admin
						//query admin_info table and assign to ['admin_info']
						break;
					default: 
						//throw error, somehow userRole isn't a number
						break;
				}
				$success = true;
			}
			else {
					$arrResult['error_message'][] = "invalid password";
					$success = false;
				}
			}
			else {
				// invalid username
				$arrResult['error_message'][] = "invalid username";
				$success = false;
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		if(!$success) {
			$arrResult['success'] = $success;
			return $arrResult;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
		/**
		expected input: => the id of the org to get users for
		
		output:
		$arrResult = array (
		'error' => exception object for query attempt
		'success' => true if successfully eddited, false otherwise
		'data' => array containing all users that are in the org
		);
	
	public function getUsersByOrgId($orgId) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE orgId=:orgId");
			$STH->bindParam(":orgId", $orgId);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
	    return $arrResult;
	}
	
	INNERJOIN STUFF
	
	SELECT * FROM students as S INNER JOIN location as L ON S.locationID = L.id
	
	
	
	*/
	
?>
