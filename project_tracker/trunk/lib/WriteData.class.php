<?php
/**
 *  WirteData.class.php 
 *
 *  DB class for writing data
 *  @author Mark Wong
 *
 */ 

require_once('Data.class.php');

class WriteData extends Data {

	protected $title;
	protected $header_function_var;
	protected $date_range;
	protected $all_sets_data_header;
	protected $all_sets_data_footer;
	protected $indiv_data_set_title;
	protected $indiv_data_set_name_heading;
	protected $all_data_sets_display_row_format_string;
	protected $indiv_set_data_header;
	protected $indiv_set_data_footer;
	protected $indiv_data_set_name_data_function_var;
	protected $indiv_data_set_display_row_function_var;
	protected $indiv_data_set_title_function_var;
	protected $edit_button_col = "";
	protected $edit_button_header = "";

	protected function __construct() {
		parent::__construct();
		if (!$this->start_date && !$this->end_date) {
			$this->date_range = "All available dates";
		} else {
			$this->date_range = $this->start_date . ' to ' . $this->end_date;
		}
	}

	private function get_project_name_heading() {
		return 'Project: ' . $this->get_project_name($this->get_data_set_heading()) . '<br /><br />' ;
	}

	private function get_employee_name_heading() {
		return 'Employee: ' . $this->get_employee_name($this->get_data_set_heading()) . '<br /><br />' ;
	}


	protected static function excel_header() {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;Filename=Project_Tracking_Report.xls");
	}

	protected static function no_header() {
	}

	protected function indiv_data_set_display_row_html($row) {
		$name_data = $this->{$this->indiv_data_set_name_data_function_var}($row);
		$date = strtotime($row->date);
		$date = date('m-d-y',$date);
		return "<tr><td>$name_data</td><td>&nbsp$date&nbsp</td><td>$row->work_type_name</td><td>$row->hours_sum</td>$this->edit_button_col<tr>";
	}

	protected function indiv_data_set_display_row_excel($row) {
		$name_data = $this->{$this->indiv_data_set_name_data_function_var}($row);
		$date = strtotime($row->date);
		$date = date('m-d-y',$date);
		return "<tr><td style='border:0'>$name_data&nbsp&nbsp</td><td style='border:0'>$date&nbsp&nbsp</td><td style='border:0'>$row->work_type_name &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td><td style='border:0'>$row->hours_sum&nbsp&nbsp&nbsp</td></tr>";
	}

	protected function get_project_name($row) {
		return $row->name;
	}

	private function get_employee_name($row) {
		return $row->first_name . ' ' . $row->last_name;
	}


	public function write_data_html() {
			$this->header_function_var = "no_header";
			$this->meta_or_css = "<link type='text/css' rel='stylesheet' href='../css/tabular.css'>";
			$this->all_sets_data_header = '<div id="wrapper"><div class="tabular"> ';
			$this->all_sets_data_footer = "</div></div>";
			$this->indiv_data_set_display_row_function_var = "indiv_data_set_display_row_html";
			$this->indiv_set_data_header = "";
			$this->indiv_set_data_footer = "";
			$this->all_data_sets_display_row_format_string = "<div class='tabular-row'><div class='tabular-cell'>%s</div><div class='tabular-cell'>%s</div></div>";
			$this->write_data();
	}

	public function write_data_excel() {
			$this->header_function_var = "excel_header";
			$this->meta_or_css = "<meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>";
			$this->all_sets_data_header = "<table>";
			$this->all_sets_data_footer = "</table>";
			$this->indiv_data_set_display_row_function_var = "indiv_data_set_display_row_excel";
			$this->indiv_set_data_header = "<table>";
			$this->indiv_set_data_footer = "</table>";
			$this->all_data_sets_display_row_format_string = "<tr><td>%s</td><td> </td><td>%s</td><td> </td></tr><tr><td> </td></tr>";
			$this->write_data();
	}



	private function write_data() {
		if ($this->heading_id == 'all') {
			$data = $this->write_all_data_sets();
		} else {
			$data = $this->write_individual_data_set();
		}

		$this->{$this->header_function_var}();

		$display_data = "<!doctype html>
				<html lang='en'>
				<head>
				<title>$this->title</title>"  
				. $this->meta_or_css .
				"<script>
					function print_edit_row() {
						var html_text = '';
						
						document.getElementById('hey').innerHTML = '<td>hey</td>';  
						}
				</script>" .
				"</head>
				<body>				
<h1 style='padding-left:15%;font-family:Arial;color:red'>TCM Editorial Project Data : <span style='font-size:smaller'>$this->title</span></h1>
<h3 style='padding-left:15%;font-family:Arial;color:black'>Date Range: $this->date_range </h3>";

		$display_data .= $this->all_sets_data_header; 
		$display_data .= $data;
		$display_data .= $this->all_sets_data_footer;
		$display_data .= "</body></html>"; 

		echo $display_data;

	}

	private function write_individual_data_set() {
		$display_data = $this->{$this->indiv_data_set_title_function_var}();

		$display_data .="<table border='1'>";
		// to display date field
		$display_data .="<tr><th>$this->indiv_data_set_name_heading</th><th width='70px'>Date</th><th>Work Type</th><th>Hours</th>$this->edit_button_header</tr>";

		$stmt = $this->get_individual_data_set();
		while($row = $stmt->fetchObject()) {
			$display_data .= $this->{$this->indiv_data_set_display_row_function_var}($row);
		}
		$display_data .="</table>";
		return $display_data;
	}

	protected function write_all_data_sets() {
		$stmt = $this->get_all_data_sets();
		$display_data = $this->indiv_set_data_header;

		while($this->heading_id = $stmt->fetchColumn()) {
			$data_column1 = $this->write_individual_data_set();
			if ($this->heading_id = $stmt->fetchColumn())
				$data_column2 = $this->write_individual_data_set();
	

			$display_data .= sprintf($this->all_data_sets_display_row_format_string,$data_column1,$data_column2);
		}
		$display_data .= $this->indiv_set_data_footer;

		return $display_data;
	}

}
