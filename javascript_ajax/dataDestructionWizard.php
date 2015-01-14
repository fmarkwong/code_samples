<?php
/**
 * form to calculate and delete data from CRM 
 *
 *
 * @author Mark Wong
 */

session_start();
require_once('config.php');


$comp = $_GET['computersQty'];
$laptop = $_GET['laptopsQty'];
$printer = $_GET['printersQty'];
$copier = $_GET['copiersQty'];

$comp = is_numeric($comp) ? $comp : 0;
$laptop = is_numeric($laptop) ? $laptop : 0;
$printer = is_numeric($printer) ? $printer : 0;
$copier = is_numeric($copier) ? $copier : 0;

function printRow($quantity, $item, $quantityId, $typeId, $subTotalId) {
	$params = "'$quantityId','$typeId','$subTotalId'";

	echo "<tr> <!--============  $item Row =============-->
		<td style=\"text-align:left;\">&nbsp;$quantity&nbsp;&nbsp;$item</td>
		<td>
		<input value='0' size='6'type=\"textbox\" id=\"$quantityId\" onkeyup=\"calculateSubTotal($params);\">
		</td>
		<td><select id=\"$typeId\" onKeyDown=\"fnKeyDownHandler_A($params,this, event);\" onKeyUp=\"fnKeyUpHandler_A(this, event); return false;\" onKeyPress = \"return fnKeyPressHandler_A($params,this, event);\"  onChange=\"fnChangeHandler_A($params, this, event);\">\n";

  	echo '<option value="" style="font-family:Courier,monospace;color:#ff0000;background-color:#ffff00;">Enter custom rate</option>';

	echo "  <option value=\"one\">Data Destruction(No Cert)</option>
			<option value=\"two\">Data Destruction(Certified, No Serial)</option>
			<option value=\"three\">DOD Certified Wipe(With Serial)</option>
			<option value=\"four\">Physical HD Destruction(Certified)</option>
			</select>
		</td>
		<td>
	   $<input value='0' size='4'type=\"textbox\" id=\"$subTotalId\" onkeyup=\"calculateBottomSubTotals();\"> 	
		</td>
	</tr>";

}



?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="keywords" content="Computer, Recycling, Recycle, Electronics, Computer, Disposal, TV, Monitor, Cell, Phone, IT, Asset, Management" />

<title>Data Destruction Price Quoter<?php echo $row['first_name']; ?>- All Green Electronics Recycling</title>
<link rel="stylesheet" type="text/css" href="htmlTables.css" /> 
<style type="text/css">
</style>
<script type="text/javascript" src="dropdown.js"></script>

<script language="javascript" type="text/javascript">

function getXmlObject() {
    var xmlhttp;

    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    return xmlhttp;
}


function save() {
    if(!anyPickups()) {
        alert('You can not save data because a pickup has not been created or selected.  Go to Edit Electronics Information page to create a pickup');
        return;
    }

	var url;
    var theForm = document.getElementById('myForm');

	url = "saveInfo.php?" + theForm.elements[0].id + "=" + theForm.elements[theForm.elements[0].id].value;
	var limit = theForm.elements.length - 2; // minus two to skip the last two buttons	
	// get all form ids and values and put them in url as http GET parameters
	for(i=1; i < limit; i++) {
		key = theForm.elements[i].id;
		value = theForm.elements[key].value;
		url = url + "&" + key + "=" + value;
	}
		var id = "<?php echo $_GET['id']; ?>";
		url = url + "&id=" + id;
		url = url + "&totalDataDestructionPrice=" + document.getElementById('totalDataDestructionPrice').innerHTML;
		url = url + "&fee=" + document.getElementById('totalPrice').innerHTML;
    xmlhttp = getXmlObject();   
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        document.getElementById("saveMessage").innerHTML= xmlhttp.responseText;

        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function calculateInventorySubTotal() {
    var computerInvQty = <?php echo $comp ?>;
    var laptopInvQty = <?php echo $laptop ?>; 
    var printerInvQty = <?php echo $printer ?>;
    var copierInvQty = <?php echo $copier ?>; 
    document.getElementById('inventorySubtotal').innerHTML = computerInvQty + laptopInvQty + printerInvQty + copierInvQty;
}

function parseElementValue(element) {
	var elementValue = document.getElementById(element).value;
	if (elementValue == "one" || elementValue == "two" || elementValue == "three" || elementValue == "four") return elementValue;

	elementValue = (isNaN(elementValue) || elementValue == '') ? 0 : elementValue;
	if (!isInt(elementValue)) {
		alert("Please enter integer numbers only");
		document.getElementById(element).value = 0;
		elementValue = 0;
	} 
	return parseInt(elementValue);

}

function calculateBottomSubTotals() {
	// calculate quantity column subtotal
    var computerQty = parseElementValue("comp_qty");
    var laptopQty = parseElementValue("laptop_qty");
    var printerQty = parseElementValue("printer_qty");
    var copierQty = parseElementValue("copier_qty");
    var otherQty = parseElementValue("other_qty");

    document.getElementById('quantitySubtotal').innerHTML = computerQty + laptopQty + printerQty + copierQty + otherQty;

	// calculate subtotal of subtotals
	var computerSubTotal = parseElementValue('computerPrice');
	var laptopSubTotal = parseElementValue('laptopPrice');
	var printerSubTotal = parseElementValue('printerPrice');
	var copierSubTotal = parseElementValue('copierPrice');
	var otherSubTotal = parseElementValue('otherPrice');

    document.getElementById('subTotal').innerHTML = computerSubTotal + laptopSubTotal + printerSubTotal + copierSubTotal + otherSubTotal;

    applyDiscount();
}

function calculateSubTotal(quantityId,typeId,subTotalId) {
    var quantity = parseElementValue(quantityId);
    var type = parseElementValue(typeId);

	switch(type) {
		case "one": rate = 10; break;
		case "two": rate = 20; break; 
		case "three": rate = 50; break; 
		case "four": rate = 50; break; 
		default: rate = type; //inputted custom rate 
	}
	var subTotal = quantity * rate;
	document.getElementById(subTotalId).value = subTotal;
	calculateBottomSubTotals();
	//return subTotal; needed or not?
}

function isInt(input) {
    return  Math.round(input) == input;
}


function applyDiscount() {
	var discountString = document.getElementById('discountBox').value;
	if(discountString == "" ) return;
	var currentPrice = parseFloat(document.getElementById('subTotal').innerHTML);
	
	var discountRate = parseFloat(discountString) * .01;	
	var discountPrice = currentPrice * (1 - discountRate);

	document.getElementById('totalDataDestructionPrice').innerHTML = discountPrice.toFixed(2);
	var totalQIPrice = parseFloat(<?php echo getDbValue('totalQIPrice'); ?>);
	totalQIPrice = (isNaN(totalQIPrice)) ? 0 : totalQIPrice;
		document.getElementById('totalQIPrice').innerHTML = totalQIPrice.toFixed(2);
	var totalPrice = totalQIPrice + discountPrice;
	document.getElementById('totalPrice').innerHTML = totalPrice.toFixed(2);


}

function setForm() {
	var idnumber = "<?php echo $_GET['id']; ?>";

    var theForm = document.getElementById('myForm');
    var url = "getInfo.php?" + theForm.elements[0].id + "=" + theForm.elements[theForm.elements[0].id].value;
    var limit = theForm.elements.length - 2; // minus two to skip the last two buttons  
	var data;

    for(i=1; i < limit; i++) {
        key = theForm.elements[i].id;
        value = theForm.elements[key].value;
        url = url + "&" + key + "=" + value;
    }
		url = url + "&" + "id=" + idnumber;

    xmlhttp = getXmlObject();   
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
//		alert(xmlhttp.responseText);
		data = xmlhttp.responseText.split(" ");
		temp = '';
		theForm.elements[totalQIPrice].value = data[totalQIPrice];

        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}


function onLoad() {
    var totalQIPrice = parseFloat(<?php echo getDbValue('totalQIPrice'); ?>);
        document.getElementById('totalQIPrice').innerHTML = totalQIPrice.toFixed(2);
	if(anyPickups()) {
		//setForm();
		//calculateInventorySubTotal();
	} else {
alert('Warning,  you will not be able to save data until a pickup is created or selected');

	}
}

// returns true if new customer
function anyPickups() {
<?php
	echo ($_SESSION['pickupSelected']) ? 'return true;' : 'return false;';
?>
}

</script>

</head>

<body onload="onLoad();">
<form id="myForm" onSubmit="return false;">
<table name="myTable" class="pretty">
<tr> <!--=========  Title Heading Row ============--> 
	<td class= "title" colspan="5">DATA DESTRUCTION PRICE QUOTER</td> 
</tr> 
<tr> <!--===========  Column Names Row ============-->  
	<th>Current Inventory</th><th>Qty</th><th>Type</th><th>Subtotal</th>
</tr>

<?php
	// Data rows
	printRow($comp,'Computers','comp_qty','computerType','computerPrice');
	printRow($laptop,'Laptops','laptop_qty','laptopType','laptopPrice');
	printRow($printer,'Printers','printer_qty','printerType','printerPrice');
	printRow($copier,'Copiers','copier_qty','copierType','copierPrice');
	printRow('','Other','other_qty','otherType','otherPrice');

?>
<tr> <!--=========== Subtotals Row ============-->
	<td style="text-align:left;">Totals: <span id="inventorySubtotal"></span></td>
	<td><span id="quantitySubtotal">0</span></td>
	<td></td>
	<td>$ <span id="subTotal">0</span></td>
</tr>


</table>
<div style="text-indent: 23pt;">
Apply Discount: <input style="width: 2em;" type="textbox" id="discountBox" value="10" onkeyup="applyDiscount();">%
<br/></div> 

<div style="text-indent: 23pt;">
Total Data Detruction Cost: $<span id="totalDataDestructionPrice"></span><br\></div>
<div style="text-indent: 23pt;">
Total Qualified Items Cost: $<span id="totalQIPrice"></span><br\></div>  
<div style="text-indent: 23pt;">
<b>Total Cost: $<span id="totalPrice"></span><b><br\><div>
<center><input type="button" value="Save" name="savebutton" onClick="save()">&nbsp;&nbsp;&nbsp;
<input type="button" value="Close" onclick="window.close()"></center>
<br>
<center><b><span id="saveMessage"></span></b></center>

</form> 



</body>
</html>

