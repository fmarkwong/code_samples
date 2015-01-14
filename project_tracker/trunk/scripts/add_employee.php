<html>
<body>

<?php

require('../lib/functionlib.php');

$first_name = param_value('firstname'); 
$last_name = param_value('lastname');
$user_name = param_value('username');
$email = param_value('email');
$button = param_value('button');
$employee_id = param_value('employee_id');
$delete_employee_id = param_value('delete_employee_id');
$change_password_employee_id = param_value('change_password_employee_id');

$hash = hash('sha256',TEMP_PASSWORD);
$salt = create_salt();
$hash = hash('sha256', $salt . $hash);

if ($button == "Add") {
	add_employee($pdo, $first_name,$last_name,$user_name,$email,$hash,$salt);
} elseif ($button == "Delete") {
	delete_employee($pdo, $delete_employee_id);
} elseif ($button == "Change Password") {
	change_password($pdo, $change_password_employee_id);
} else {
	echo "error in button value";
	die();
}


function change_password($pdo, $change_password_employee_id) {
	$new_password1 = param_value("new_password1");
	$new_password2 = param_value("new_password2");
	
	try {
        $sql = "SELECT * FROM employees WHERE id = :id"; 
        $stmt = $pdo->prepare($sql);
        $params = array(':id' => $change_password_employee_id);
        $stmt->execute($params);
		$row = $stmt->fetchObject();

		if ($new_password1 != $new_password2) {
			$msg_type="password";
			$msg =  "New passwords do not match";
			header("Location: ../admin.php?message_type=$msg_type&message=$msg");
		} else {
			$hash = hash('sha256',$new_password1);
			$salt = create_salt();
			$hash = hash('sha256', $salt . $hash);

			$sql = "UPDATE employees set password=:password, salt=:salt"; 
			$stmt = $pdo->prepare($sql);
			$params = array(':password' => $hash,
							':salt' => $salt);
			$stmt->execute($params);
			$msg_type="password";
			$msg =  "Password changed!";
			header("Location: ../admin.php?message_type=$msg_type&message=$msg");
		}

	}	

    catch(PDOException $e) { 
		echo "Error!: " . $e->getMessage() . "<br />";
    }
}	


function add_employee($pdo, $first_name,$last_name,$user_name,$email,$hash,$salt) {
	try {
		$sql = "INSERT INTO employees (first_name, last_name,username,email,password,salt) VALUES (:firstname,:lastname,:username,:email,:password,:salt)";
		$stmt = $pdo->prepare($sql);
		$params = array(':firstname' => $first_name, 
						':lastname' => $last_name,
						':username' => $user_name,
						':email' => $email,
						':password' => $hash,
						':salt' => $salt,
						); 
		$stmt->execute($params);
			$msg_type="add_employee";
			$msg =  "$user_name has been added with password " . TEMP_PASSWORD;
			header("Location: ../admin.php?message_type=$msg_type&message=$msg");
		
	}
	catch(PDOException $e) {
		if ($e->getCode() == 23000) {
			$msg_type="add_employee";
			$msg =  "Username and or email address is already in use, please choose another";
			header("Location: ../admin.php?message_type=$msg_type&message=$msg");
		} else {
			echo "Error!: " . $e->getMessage() . "<br />";
			die();
			}
	}
}

function delete_employee($pdo, $delete_employee_id) {
	try {
		$sql = "DELETE FROM employees WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		$params = array(':id' => $delete_employee_id ); 
		$stmt->execute($params);
		$msg =  "Employee has been deleted";
		header("Location: ../admin.php?message_type=employee_deleted&message=$msg");
		
	}
	catch(PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br />";
			die();
	}
}

?>

<button onclick="history.go(-1);">Go back</button>
</body>
</html>
