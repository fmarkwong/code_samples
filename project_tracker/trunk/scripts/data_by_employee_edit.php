<?php

include ('../lib/functionlib.php');
include('../lib/dataByEmployeeEdit.class.php');

$display_button = param_value('display_button');
$download_button = param_value('download_button');

$data = new DataByEmployeeEdit(); 

if ($display_button) {
	echo $data->write_data_html();
} else {
	echo $data->write_data_excel();
}

?>




