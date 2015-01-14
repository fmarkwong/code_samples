<?php

include ('../lib/functionlib.php');
include('../lib/DataByProject.class.php');

$display_button = param_value('display_button');
$download_button = param_value('download_button');

$data = new DataByProject(); 

if ($display_button) {
	echo $data->write_data_html();
} else {
	echo $data->write_data_excel();
}

?>



