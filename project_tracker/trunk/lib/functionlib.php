<?php

/**
 *  functionlib.php 
 *
 *
 *  helper classes for project management software
 *  @author Mark Wong
 *
 */ 


define('TEMP_PASSWORD','tcm1234$1');
define('DB_HOST','localhost');
define('DB_NAME','hrproj');
define('DB_USERNAME','root');
define('DB_PASSWD','root');

	try {
		$pdo = new PDO("mysql:host=localhost;dbname=" . DB_NAME,DB_USERNAME,DB_PASSWD);
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  

	}
	catch(PDOException $e) {
		echo "Error!: " . $e->getMessage() . "<br />";
		die();
	}


function param_value($param) {
	return isset($_GET[$param]) ? $_GET[$param] : null;
}

function param_value_post($param) {
	return isset($_POST[$param]) ? $_POST[$param] : null;
}

function is_logged_in() {
	if(isset($_SESSION['valid']) && $_SESSION['valid']) 
		return true;
	else
		return false;
}

function get_project_name_arr($pdo) {
	try {
		$sql = "SELECT * FROM projects";
		$stmt = $pdo->query($sql);

		while($row = $stmt->fetchObject()) {
			$project_name_arr[$row->id] = $row->name;
		}
	}
	catch(PDOException $e) {
		echo "Error!: " . $e->getMessage() . "<br />";
		die();
	}

	return $project_name_arr;

}

function create_salt() {
    $string = md5(uniqid(rand(), true));
    return substr($string, 0, 3);
}


?>


