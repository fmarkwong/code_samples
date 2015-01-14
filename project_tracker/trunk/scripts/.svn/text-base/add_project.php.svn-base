<?php

require('../lib/functionlib.php');

$project_name = param_value('projectname');
$button = param_value('button');
$project_id = param_value('project_id');


if ($button == "Add") {
	add_project($pdo, $project_name);
} elseif ($button == "Delete") {
	delete_project($pdo, $project_id);
} else {
	echo "error in button value";
	die();
}


function add_project($pdo, $project_name) {
    try {
        $sql = "INSERT INTO projects (name) VALUES (:projectname)";
        $stmt = $pdo->prepare($sql);
        $params = array(':projectname' => "$project_name"); 
        $stmt->execute($params);
		$msg_type="add_project";
		$msg =  "Project $project_name has been added";
		header("Location: ../admin.php?message_type=$msg_type&message=$msg");

    }
    catch(PDOException $e) {
        echo "Error!: " . $e->getMessage() . "<br />";
        die();
    }
}

function delete_project($pdo, $project_id) {
	try {
		$sql = "DELETE FROM projects WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		$params = array(':id' => $project_id ); 
		$stmt->execute($params);
		$msg_type="delete_project";
		$msg =  "Project has been deleted";
		header("Location: ../admin.php?message_type=$msg_type&message=$msg");
	}
	catch(PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br />";
			die();
	}
}

?>
