<?php

/**
 *  login.php 
 *
 * login page. provides aunthetication
 */ 

include('lib/functionlib.php');

session_start();
$username = param_value_post('username');
$password = param_value_post('password');

if ($username) {
    try {

        $sql = "SELECT * FROM employees WHERE username = :username"; 
        $stmt = $pdo->prepare($sql);
        $params = array(':username' => $username);
        $stmt->execute($params);
		$row = $stmt->fetchObject();
		if (!$row) {
			echo "No such user<br />";
			die();
		} else {
			$hash = hash('sha256', $row->salt . hash('sha256', $password));
			if ($hash != $row->password) {
				echo "Wrong password <br /><br />";
				echo '<FORM><INPUT TYPE=button VALUE="Go Back" onclick=window.history.back()></FORM>';
				die();
			} else {
				validate_user($pdo,$row->username,$row->id);
				if ($admin_page){
					header('Location: admin.php');
				}
				else {
					header('Location: employee.php');
				}
			}
		}

    }
    catch(PDOException $e) { echo "Error!: " . $e->getMessage() . "<br />";
    }
}


function validate_user($pdo,$username, $id) {
    session_regenerate_id (); //this is a security measure
    $_SESSION['valid'] = TRUE;
    $_SESSION['employee_id'] = $id;
    $_SESSION['employee_username'] = $username;

    try {
        $sql = "SELECT type FROM employees WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $params = array(':id' => $id);
        $stmt->execute($params);
		$employee_type = $stmt->fetchColumn();

		if($employee_type == 'admin') {
			$_SESSION['admin'] = TRUE;
		}

	} 
    catch(PDOException $e) {
        echo "Error!: " . $e->getMessage() . "<br />";
        die();
    }

}


?>

<h1 style="font-family:Arial;text-align:center;color:red">Welcome to the TCM Editorial Project Tracker</h1>
<br />

<form name="login" action="login.php" method="post">
	<fieldset style="width:450px">
	<legend>Login</legend>
	
    Username: <input type="text" name="username" /><br />
    Password: &nbsp<input type="password" name="password" /><br />
    <input type="submit" value="Login" />
	</fieldset>
</form>
