<?php
include('DataByEmployee.class.php');

class DataByEmployeeEdit extends DataByEmployee {
	

	function __construct() {
		parent::__construct();
	 	$this->edit_button_col = "<td><button onclick='print_edit_row()'>Edit</button></td><span name='hey'></span>";
	 	$this->edit_button_header = "<th>Edit</th>";

	}

			

}
