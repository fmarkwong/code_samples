<?php

require('../lib/functionlib.php');

$employee_id = param_value('employee_id');
$project_id = param_value('project_id');
$assign_button = param_value('assign_button');
$unassign_button = param_value('unassign_button');

if ($assign_button) {
	assign($pdo,$employee_id,$project_id);
} elseif ($unassign_button) {
	unassign($pdo,$employee_id,$project_id);
} else {
	echo("error!!!!");
	die();
}



function unassign($pdo,$employee_id,$project_id) {

    try {
		$sql = "DELETE FROM project_assignment WHERE employee_id = :employee_id AND project_id = :project_id";
        $stmt = $pdo->prepare($sql);
        $params = array(':employee_id' => $employee_id,
						':project_id'  => $project_id  ); 
        $stmt->execute($params);
		$msg_type="assign_project";
		$msg =  "Project has been unassigned";
		header("Location: ../admin.php?message_type=$msg_type&message=$msg");

    }
    catch(PDOException $e) {
        echo "Error!: " . $e->getMessage() . "<br />";
        die();
    }
}


function assign($pdo,$employee_id,$project_id) {

    try {
        $sql = "INSERT INTO project_assignment (employee_id,project_id) VALUES (:employee_id,:project_id)";
        $stmt = $pdo->prepare($sql);
        $params = array(':employee_id' => $employee_id,
						':project_id'  => $project_id  ); 
        $stmt->execute($params);
		$msg_type="assign_project";
		$msg =  "Project has been assigned";
		header("Location: ../admin.php?message_type=$msg_type&message=$msg");

    }
    catch(PDOException $e) {
		if ($e->getCode() == 23000) {
		$msg_type="assign_project";
		$msg =  "Warning, employee is already assigned to this project.";
		header("Location: ../admin.php?message_type=$msg_type&message=$msg");
		} else {
        echo "Error!: " . $e->getMessage() . "<br />";
        die();
		}
    }
}
?>
