<?php
include('../lib/functionlib.php');

session_start();
if(!is_logged_in()) {
	echo "You are not logged in. Please go to the <a href='../login.php'>login<a> to log in<br />";
	die();
}

$employee_username = $_SESSION['employee_username'];
$employee_id = $_SESSION['employee_id'];
$cur_password = param_value_post("current_password");
$new_password1 = param_value_post("new_password1");
$new_password2 = param_value_post("new_password2");

echo "You are logged in as: <strong>$employee_username</strong> <br /><br />";

if ($cur_password && $new_password1 && $new_password2) {


    try {

        $sql = "SELECT * FROM employees WHERE id = :id"; 
        $stmt = $pdo->prepare($sql);
        $params = array(':id' => $employee_id);
        $stmt->execute($params);
		$row = $stmt->fetchObject();
		$hash = hash('sha256', $row->salt . hash('sha256', $cur_password));

		if ($hash != $row->password) {
			echo "Wrong password <br />";
		} elseif ($new_password1 != $new_password2) {
			echo "new passwords do not match <br />";
		} else {
			$hash = hash('sha256',$new_password1);
			$salt = create_salt();
			$hash = hash('sha256', $salt . $hash);

			$sql = "UPDATE employees set password=:password, salt=:salt"; 
			$stmt = $pdo->prepare($sql);
			$params = array(':password' => $hash,
							':salt' => $salt);
			$stmt->execute($params);
			echo "password changed!<br />";
		}

	}	

    catch(PDOException $e) { 
		echo "Error!: " . $e->getMessage() . "<br />";
    }


} else {
	echo "please enter all information <br />";
}

?>


<form action="" method="POST">
	<fieldset style="width:450px">
		<legend>Change Password</legend>
		Current Password: <input id="current_password" type="text" name="current_password" /><br /><br />
		New Password: <input id="new_password1" type="text" name="new_password1" /><br />
		New Password: <input id="new_password2" type="text" name="new_password2" />
		<div style="clear:both;height:2px"></div>
		<input type="submit" name="button" value="Change" /><br /><br />


	</fieldset>
</form>

<p><a href="../employee.php">Go back</a>

