<?php

/**
 *  DataByEmployee.php 
 *
 *  Retrive data organized by employee
 *
 *  @author Mark Wong
 *
 */ 

include('WriteData.class.php');

class DataByEmployee extends WriteData {

	function __construct() {
		parent::__construct();
		$this->heading_id = $this->param_value('employee_id');
		$this->data_set_heading_table = "employees";
		$this->data_set_column_table = "projects";
		$this->data_set_heading_column_name = "employee_id";
		$this->data_set_column_column_name = "project_id";
		$this->data_set_heading_order_by = 'first_name'; // employee name
		$this->data_set_column_order_by = 'projects.name'; // this is project name

		$this->title = "By Employee";
		$this->indiv_data_set_title_function_var = "get_employee_name_heading";
		$this->indiv_data_set_name_heading = "Project";
		$this->indiv_data_set_name_data_function_var = "get_project_name";


	}


			

}
