<?php

/**
 *  generate_json_tag_cloud_data.php
 *
 *  Retrieves comment text data from database,
 *  parses it into keywords and then sums frequency
 *  of words and encodes into JSON format and displays
 *
 *  @author Mark Wong
 *
 *  @param  {integer} user_id
 *  @param  {integer} website_id
 */ 

require('header.php');

//grab userinput and create database object
$user_id = htmlentities(get_param_value('user_id'));
$website_id = htmlentities(get_param_value('website_id'));
$pdo = create_pdo();

//display user input
echo "<h3>Parameters entered</h3>\n";
echo "User ID: $user_id <br />\n";
echo "Website ID: $website_id <br />\n";

//prepare sql statement where clause (and PDO params) based on user input
if($user_id != 'none' && $website_id != 'none') {
	$where_clause = 'WHERE user_id = :user_id AND website_id = :website_id';
	$pdo_params = array(':user_id' => $user_id,
					':website_id'  => $website_id);
} elseif($user_id != 'none') {
	$where_clause = 'WHERE user_id = :user_id';
	$pdo_params = array(':user_id' => $user_id);
} elseif($website_id != 'none') {
	$where_clause = 'WHERE website_id = :website_id';
	$pdo_params = array(':website_id' => $website_id);
} else { // both input fields none, will select all data 
	$where_clause = ''; 
	$pdo_params = array(); 
}

// retrieve comment data and process
try {
	echo "<h3>Keywords parsed from comments</h3>\n";
	$key_word_frequency = array();

	$sql = "SELECT comment FROM ". DB_TABLE_NAME . " $where_clause";
	$stmt = $pdo->prepare($sql);
	$stmt->execute($pdo_params);

	while($row = $stmt->fetchObject()) {
		//parse keyword tags and display
		$comment = strtolower($row->comment);
		$comment = remove_common_words($comment);
		$comment = remove_punctuation_numbers($comment);
		$comment = remove_extra_spaces($comment);
		$comment = htmlentities($comment);
		echo "$comment <br />\n";
		// calculate tag frequency, store in array
		$key_words = explode(' ',$comment);
		foreach ($key_words as $kw) {
			if(array_key_exists($kw,$key_word_frequency))
				$key_word_frequency[$kw]++;
			else
				$key_word_frequency[$kw] = 1;
		}
	}
}
catch (PDOException $e) {
	echo "Error!: " . $e->getMessage() . "<br />\n";
	die();
}

//if data set existed, encode to json format and display
if(!isset($comment)) {
	echo "No data for given parameters<br />\n"; 
} else {
	echo "<h3>JSON tag cloud data (tag name, frequency)</h3>\n";
	echo htmlentities(json_encode($key_word_frequency)) . "\n";
}
echo "<div style='clear:both;height:50px'></div>\n";
echo "<a href='javascript:history.back()'>Go back</a>\n";


?>
