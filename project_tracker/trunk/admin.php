<?php
/**
 *  admin.php 
 *
 *  Admin panel for project tracker
 *
 *  @author Mark Wong
 */ 


include ('lib/functionlib.php');

session_start();
if(!is_logged_in()) {
	echo "You are not logged in. Please go to the <a href='login.php'>login<a> to log in<br />";
	die();
} 

$employee_username = $_SESSION['employee_username'];
$employee_id = $_SESSION['employee_id'];
$admin_bool = isset($_SESSION['admin']) ? $_SESSION['admin'] : null;


if(!$admin_bool) {
	echo "You are not authorized to view the admin page <br />";
	echo '<a href="employee.php">Employee page</a>';
	die();
}
?>


<?php
define("ALL_OPTION_TRUE",TRUE);
define("ALL_OPTION_FALSE",FALSE);

$message_type = param_value('message_type');
$message = param_value('message');
$message = "<span style='color:red'>$message</span>";

function employee_select_box($pdo, $all_option,$employee_id_type) {
	try {
		echo "Employee: <select name='$employee_id_type'>";
		if ($all_option)
			echo "<option value='all'>All Employees</option>";
		$sql = "SELECT * FROM employees ORDER BY first_name";
		$stmt = $pdo->query($sql);
		while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
			echo "<option value='$row->id'>$row->first_name $row->last_name</option>";
		}
		echo "</select>";
	}
	catch(PDOException $e) {
		echo "Error!: " . $e->getMessage() . "<br />";
		die();
	}
}

function project_select_box($pdo, $all_option) {
	try {
		echo "Project: <select name='project_id'>";
		if ($all_option)
			echo "<option value='all'>All Projects</option>";
		$sql = "SELECT * FROM projects ORDER BY name";
		$stmt = $pdo->query($sql);
		while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
			echo "<option value='$row->id'>$row->name</option>";
		}
		echo "</select>";
	}
	catch(PDOException $e) {
		echo "Error!: " . $e->getMessage() . "<br />";
		die();
	}
	
}

function employee_list_table($pdo) {
	try { 
		echo "<h2>Current Employees and Project Assignments:</h2>
		<table id='ver-minimalist' border='0'>
		<thead>
			<tr>
				<th scope='col'>Employee</th>
				<th scope='col'>Assigned Projects</th>
			</tr>
		</thead>
		<tbody>
		";


		// first join match employee to project ids
		// second join match project ids to project names
		$sql = "SELECT employees.first_name as first_name, employees.last_name as last_name, 
				IF (projects.name IS NOT NULL, GROUP_CONCAT(projects.name), 'NOT ASSIGNED') as assigned_projects
			FROM employees
			LEFT JOIN project_assignment  	
			ON employees.id = project_assignment.employee_id 
			LEFT JOIN projects 			
			ON project_assignment.project_id = projects.id
			GROUP BY employees.id
			ORDER BY employees.first_name";
		$stmt = $pdo->query($sql);
		while($row = $stmt->fetchObject()) {
			echo "<tr><td>$row->first_name $row->last_name</td></td><td>$row->assigned_projects</td><tr>";
		}
		echo "</tbody> </table>";
	}
	catch(PDOException $e) {
		echo "Error!: " . $e->getMessage() . "<br />";
		die();
	}
}



?>
<html>
<head>
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<link href="css/table-style.css" rel="stylesheet" type="text/css" />

<script src="js/calendar.js"
        language="javascript">

</script>
<script language="javascript" type="text/javascript">

function fillUsernameEmailAddress() {
	var firstName = document.getElementById('firstname').value;
	var lastName = document.getElementById('lastname').value;

	document.getElementById('username').value = firstName[0] + lastName;
	document.getElementById('emailaddress').value = firstName[0] + lastName + "@tcmpub.com";
}


</script>
		
</head>

<body>
<h1 style="font-family:Arial;text-align:center;color:red">TCM Editorial Project Tracker Admin Page</h1>
You are logged in as: <strong><?php echo $employee_username; ?></strong>
<br />
<a href="employee.php">Employee Page</a><br />
<a href="logout.php">Logout</a><br /><br />


<table border='0'>
<tr>
<td style="vertical-align:top">

<h2>Add Employees/Projects</h2>
<form action="scripts/add_employee.php" method="GET">
	<fieldset style="width:450px">
		<legend>Add/Delete Employee/Change Employee Password</legend>
		First Name: <input id="firstname" type="text" name="firstname" onBlur="fillUsernameEmailAddress()" />
		Last Name: <input id="lastname" type="text" name="lastname" onBlur="fillUsernameEmailAddress()" />
		Username: <input id="username" type="text" name="username" />
		Email Address: <input id="emailaddress" type="text" name="email" />	
		<div style="clear:both;height:2px"></div>
		<input type="submit" name="button" value="Add" /><br />
		
		<?php
		if($message_type=="add_employee") {
			echo $message . "<br /><br />";
		} else {
			echo "<br />";
		}
		employee_select_box($pdo,ALL_OPTION_FALSE,'delete_employee_id');
		?>
		<br />
		<input type="submit" name="button" value="Delete" /><br /><br />
		
		<?php
		if($message_type=="employee_deleted") {
			echo $message . "<br /><br />";
		} else {
			echo "<br />";
		}

		employee_select_box($pdo,ALL_OPTION_FALSE,'change_password_employee_id');
		?>
		<div style="clear:both;height:3px"></div>
		New Password: <input id="new_password1" type="password" name="new_password1" /><br />
		New Password: <input id="new_password2" type="password" name="new_password2" />
		<div style="clear:both;height:2px"></div>

		<input type="submit" name="button" value="Change Password" /><br /><br />
		<?php
		if($message_type == "password") {
			echo $message ; 
		}
		?>


	</fieldset>
</form>

<div style="clear:both;height:20px"></div>

<form action="scripts/add_project.php" method="GET">
	<fieldset style="width:450px">
		<legend>Add/Delete Project</legend>
		Project Name:<input type="text" name="projectname" />
		<div style="clear:both;height:2px"></div>
		<input type="submit" name="button" value="Add" /><br /><br />
		<?php
		if($message_type == "add_project") {
			echo $message . "<br />" ; 
		}
		project_select_box($pdo,ALL_OPTION_FALSE);
		?>
		<br />

		<input type="submit" name="button" value="Delete" /><br />
		<?php
		if($message_type == "delete_project") {
			echo $message . "<br />" ; 
		}
		?>
	</fieldset>
</form>

<div style="clear:both;height:20px"></div>

<form action="scripts/assign_project.php" method="GET">
	<fieldset style="width:450px">
		<legend>Assign/Unassign Project</legend>

		<?php
			employee_select_box($pdo,ALL_OPTION_FALSE,'employee_id');
			echo "<div style='clear:both;height:2px'></div>";
			project_select_box($pdo,ALL_OPTION_FALSE);
		?>

		<div style="clear:both;height:2px"></div>
		<input type="submit" name="assign_button" value="Assign" />
		<input type="submit" name="unassign_button" value="Unassign" /><br />
		<?php
		if($message_type == "assign_project") {
			echo $message; 
		}
		?>

	</fieldset>
</form>



<h2>Reports:</h2>

<form action="scripts/data_by_project.php" method="GET">
	<fieldset style="width:450px">
		<legend>Get Data by Project</legend>
		<?php
			project_select_box($pdo,ALL_OPTION_TRUE);
		?>
		<div style="clear:both;height:10px"></div>
		Date Start*:<input onfocus="showCalendarControl(this);" type="text" name="start_date" />
		Date End*:<input onfocus="showCalendarControl(this);" type="text" name="end_date" />
		<input type="submit" name="display_button" value="Display Report" />
		<input type="submit" name="download_button" value="Download Report" />
	</fieldset>
</form>

<div style="clear:both;height:10px"></div>

<form action="scripts/data_by_employee.php" method="GET">
	<fieldset style="width:450px">
		<legend>Get Data by Employee</legend>
		<?php
			employee_select_box($pdo,ALL_OPTION_TRUE,'employee_id');
		?>
		<div style="clear:both;height:10px"></div>

		Date Start*:<input onfocus="showCalendarControl(this);" type="text" name="start_date" />
		Date End*:<input onfocus="showCalendarControl(this);" type="text" name="end_date" />
		<input type="submit" name="display_button" value="Display Report" />
		<input type="submit" name="download_button" value="Download Report" />
	</fieldset>
</form>
		<div style="clear:both;height:14px"></div>
		<span style="font-size:small">*Leave date fields blank to display all dates</span>

</td>
<td style="vertical-align:top">
<?php employee_list_table($pdo); ?>


</td>
</tr>
</table>

</body>
</html>
