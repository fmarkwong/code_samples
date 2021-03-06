<?php

/**
 *  Data.class.php 
 *
 *  provides db calls to retreive employee/project data
 *  
 *  @author Mark Wong
 *
 */ 


abstract class Data {

	protected $pdo;
	protected $heading_id;
	protected $data_set_heading_table; 
	protected $data_set_column_table; 
	protected $data_set_heading_column_name; 
	protected $data_set_column_column_name; 
	protected $data_set_heading_order_by; 
	protected $data_set_column_order_by;
	protected $start_date;
	protected $end_date;



	protected function __construct() {
		$this->start_date = $this->param_value('start_date');
		$this->end_date = $this->param_value('end_date');

		try {
			$this->pdo = new PDO("mysql:host=localhost;dbname=" . DB_NAME,DB_USERNAME,DB_PASSWD);
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  

		}
		catch(PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br />";
			die();
		}
	}


	protected function get_individual_data_set() {

		try {
			$base_sql = "SELECT *,  SUM(hours) as hours_sum FROM $this->data_set_column_table, hours, work_types WHERE $this->data_set_column_table.id = hours.$this->data_set_column_column_name AND hours.work_type_id = work_types.id AND $this->data_set_heading_column_name = :data_set_heading_id";
			$order_group_by_sql = "GROUP BY $this->data_set_column_column_name, work_type_id ORDER BY date";
			$date_sql = "AND date >= :start_date AND date <= :end_date";


			if ($this->start_date && $this->end_date) { // if date range specified
				$sql = $base_sql . ' ' . $date_sql . ' ' . $order_group_by_sql;
				$params = array(":data_set_heading_id" => $this->heading_id,
								':start_date' => $this->start_date,
								':end_date'   => $this->end_date); 
			} else {  // no date range, show all
				$sql = $base_sql . ' ' . $order_group_by_sql;
				$params = array(":data_set_heading_id" => $this->heading_id);

			}

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($params);

			return $stmt;
		}
		catch(PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br />";
			$sql = $this->pdo_sql_debug($sql,$params);
			echo "sql is $sql<br/>";
			die();
		}
	}

	private function pdo_sql_debug($sql,$placeholders){
		foreach($placeholders as $k => $v){
			$sql = str_replace($k,$v,$sql);
		}
		return $sql;
	}


	protected function get_all_data_sets () {
		try {
			$sql = "SELECT id FROM $this->data_set_heading_table ORDER BY $this->data_set_heading_order_by";
			return $this->pdo->query($sql);
		} 
		catch(PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br />";
			die();
		}
	}

	protected function param_value($param) {
		return isset($_GET[$param]) ? $_GET[$param] : null;
	}


// should be able to get this from a union from somewhere else TODO:
	protected function get_data_set_heading () {
		try {
			$sql = "SELECT * FROM $this->data_set_heading_table WHERE id = :id";
			$stmt = $this->pdo->prepare($sql);
			$params = array(':id' => $this->heading_id);
			$stmt->execute($params);
			$row = $stmt->fetch(PDO::FETCH_OBJ);
			return $row; 
		}

		catch(PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br />";
			die();
		}
	}


}
