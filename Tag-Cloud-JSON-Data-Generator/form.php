<?php
	require_once('header.php');
	$pdo = create_pdo();

?>
<html>
<head>
<script type="text/javascript"> 
/*  form validation, not needed at this point because using database driven select boxes, but here just in case needed for future */
function validateForm() {
	var user_id = document.forms["myForm"]["user_id"].value;
	var website_id = document.forms["myForm"]["website_id"].value;
		if(/\D/.test(user_id) || (/\D/.test(website_id))) {
			alert("Please only enter numeric data"); 
			return false;
		} 

	return true;
}
</script>
</head>
<body>

<h1 style="text-align:center">Tag Cloud JSON Data Generator</h1>
<form name="myForm" action="generate_json_tag_cloud_data.php">

<fieldset style="width:350px">  
    <legend >Enter User Id and/or Website Id*</legend>  
	<label>User ID </label>
	<?php
		generate_select_box($pdo,'user_id');
	?>

	<label>Website ID </label>
	<?php
		generate_select_box($pdo,'website_id');
	?>
	
</fieldset>  
<input type="submit" value="Submit" />
</form>
<div style="clear:both;height:60px"></div>
*if neither user or website ID selected, data from all users and websites will be selected


</body>

</html>

