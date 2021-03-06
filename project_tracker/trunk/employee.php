<?php

/**
 *  employee.php 
 *
 *  employee dashboard
 *  displays, projects, work type and hours worked
 *
 *  @author Mark Wong
 */ 


include('lib/functionlib.php');

session_start();
if(!is_logged_in()) {
	echo "You are not logged in. Please go to the <a href='login.php'>login<a> to log in<br />";
	die();
}

$employee_username = $_SESSION['employee_username'];
$employee_id = $_SESSION['employee_id'];
$admin_bool = isset($_SESSION['admin']) ? $_SESSION['admin'] : null;
$project_id = param_value("project");
$work_type_id = param_value("work_type");
$hours = param_value("hours");
$date = param_value("date");
$department = param_value("department");
$project_type = param_value("project_type");

if (empty($department)) $department = "creative";

if ($hours) {
    try {

        $sql = "INSERT INTO hours (project_id,work_type_id, employee_id,hours,date ) VALUES (?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $params = array($project_id,$work_type_id,$employee_id,$hours,$date);
        $stmt->execute($params);
		echo "Hours have been entered<br />";

    }
    catch(PDOException $e) { echo "Error!: " . $e->getMessage() . "<br />";
    }
}

?>

<html>
<head>
<link href="css/calendar.css"
      rel="stylesheet" type="text/css" />
<script src="js/calendar.js"
        language="javascript">
</script>
</head>
<body>
<h1 style="font-family:Arial;text-align:center;color:red">TCM Editorial Project Tracker Employee Page</h1>


You are logged in as: <strong><?php echo $employee_username; ?></strong>
<br /><br />

<?php
	if($admin_bool) {
		echo '<a href="admin.php">Admin Page</a><br />';
	}
?>
<a href="scripts/change_password.php">Change your password</a><br />
<a href="logout.php">Logout</a><br /><br /><br />
<form action="" method="GET">
	<fieldset style="width:450px">
	<legend>Enter project / work type / hours worked for this session</legend>

<?php

    try {
        $sql = "SELECT COUNT(*) FROM project_assignment WHERE employee_id = $employee_id";
		$stmt = $pdo->query($sql);

		if (!$stmt->fetchColumn()) {
			echo "You are have not been assigned to any projects and can not enter hours until you are assigned to a project <br/>";
			die();
		}
	} 
    catch(PDOException $e) {
        echo "Error!: " . $e->getMessage() . "<br />";
        die();
    }
?>

Department:

<select name="department" onchange="var url = [location.protocol, '//', location.host, location.pathname].join('');location.href= url + '?department=' + this.options[this.selectedIndex].value">
<?php 
	if($department == "creative") {
		echo "<option value='creative' selected='selected'>Creative</option>
	<option value='editorial'>Editorial</option>";
	} else {
		echo "<option value='creative'>Creative</option>
	<option value='editorial' selected='selected'>Editorial</option>";
	}
	
?>

</select>
<br />

Project Type:  
<select name="project_type" onchange="var url = [location.protocol, '//', location.host, location.pathname].join('');location.href= url + '?project_type=' + this.options[this.selectedIndex].value">
<?php 
	if($project_type == "product") {
		$product_selected = "selected='selected'";
	} elseif ($project_type == "project") {
		$project_selected = "selected='selected'";
	} else {
		$product_selected = "";
		$project_selected = "selected='selected'";
	}

	echo "<option value='product' $product_selected>Product</option>";
	echo "<option value='project' $project_selected>Project</option>";

?>
</select>
<br />
	

<?php 
	if ($project_type == 'product') {
		// drop down for product #
		echo "Product ID: ";
		echo "<input name = 'product_id' type='text' />";
				
	} else {
		// drop down for projects
		echo "Project: ";
	 	echo '<select name="project">';
	
		$project_name_arr = get_project_name_arr($pdo);
	    try {
	        $sql = "SELECT project_id FROM project_assignment WHERE employee_id = :employee_id GROUP BY project_id";
			$stmt = $pdo->prepare($sql);
			$params = array(':employee_id' => $employee_id);
			$stmt->execute($params);
	
			while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
				$project_name = $project_name_arr[$row->project_id];
				echo "<option value='$row->project_id'>$project_name</option>";
			}
	    }
	    catch(PDOException $e) {
	        echo "Error!: " . $e->getMessage() . "<br />";
	        die();
	    }
	}
?>

</select> 
<br />

Work Type: 
<select name="work_type">
<?php
	
    try {
        $sql = "SELECT * FROM work_types WHERE department = :department";
		$stmt = $pdo->prepare($sql);
		$params = array(':department' => $department);
		$stmt->execute($params);
		while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
			echo "<option value='$row->id'>$row->work_type_name</option>";

		}
    }
    catch(PDOException $e) {
        echo "Error!: " . $e->getMessage() . "<br />";
        die();
    }
?>

</select> 
<br />


Date: <input onfocus="showCalendarControl(this);" type="date" name="date" value="<?php echo date('Y-m-d');?>"/><br />
Hours Worked: <input type="text" name="hours" /><br />
<div style="clear:both;height:4px"></div>

<input type="submit" value="Enter Hours" />
</fieldset>
</form>


<br /><br />

<form action="scripts/data_by_employee_edit.php" method="GET">
	<fieldset style="width:450px">
		<legend>Display summary report</legend>

		Date Start*:<input onfocus="showCalendarControl(this);" type="text" name="start_date" />
		Date End*:<input onfocus="showCalendarControl(this);" type="text" name="end_date" /><br />
		<div style="clear:both;height:4px"></div>
		<input type="hidden" name="employee_id" value="<?php echo $employee_id ?>" />
		<input type="submit" name="display_button" value="Display Report" />
		<input type="submit" name="download_button" value="Download Report" />
	</fieldset>
</form>

		<div style="clear:both;height:14px"></div>
		<span style="font-size:small">*Leave date fields blank to display all dates</span>
</body>
</html>



