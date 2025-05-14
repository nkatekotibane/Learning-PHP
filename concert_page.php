<?php
	session_start();
	// 
?>
	<!DOCTYPE html>
	<html>
		<head>
			<title>Booking Page</title>
		</head>
		<body>
			<h1>Beyonce Concert </h1>
			<form  method="POST">
			<label>Name:	 </label><input type="text" name="Name" required><br>
			<label>Gender: 	</label>
			<select name="Gender">
				<option value="Male">Male</option>
				<option value="Female">Female</option>
			</select> <br>
			<!-- get age and restrict users under the age of 16 -->
			<label>Age: 	</label><input type="number" name="Age" min="16" max="55" required><br>
			<label>Ticket Category: </label>
			<select name="Ticket_Category">
				<option value="VVIP">VVIP</option>
				<option value="VIP">VIP</option>
				<option value="General_Admission">General Admission</option>
			</select><br>
			<input type="submit" name="submit_button" value="Submit">
			</form>
		</body>
	</html>


<?php
//global variables


// tricket prices in R 
$ticket_prices = array(
	"VVIP" => 3000,
	"VIP" => 2000,
	"General_Admission" => 500
);

// Maximum Venue Capacity
$max_capacity = 60000;

// number of tickets sold
// array( #numberofsales, #total_sales_in_rands);
if (!isset($_SESSION["tickets_sold"])) {
	$_SESSION["tickets_sold"] = array(0, 0);
}
$tickets_sold = &$_SESSION["tickets_sold"];

function update_tickets_sold($ticket_category) {
	global $tickets_sold, $ticket_prices;
	$tickets_sold[0] += 1;
	$tickets_sold[1] += $ticket_prices[$ticket_category];
}	

// check for cacity



// track ticket sales for each Category
if (!isset($_SESSION["ticket_sales_for_each_category"])) {
	$_SESSION["ticket_sales_for_each_category"] = array(
		"VVIP" => [0 , 0],
		"VIP" => [0, 0],
		"General_Admission" => [0, 0]
	);
}
$ticket_sales_for_each_category = &$_SESSION["ticket_sales_for_each_category"];

function track_category_tickets($ticket_category) {
	global $ticket_sales_for_each_category, $ticket_prices;
	// add number of tickets
	$ticket_sales_for_each_category[$ticket_category][0] += 1;
	$ticket_sales_for_each_category[$ticket_category][1] += $ticket_prices[$ticket_category];
}


// capture stats about age groups and gender of buyers
$ageGroups = array(
	"age_group1" => "16 - 21",
	"age_group2" => "22 - 35",
	"age_group3" => "36 - 55",
);

//n
if (!isset($_SESSION["stats"])) {
	$_SESSION["stats"] = array(
		"16 - 21" => ["Male" => 0, "Female" => 0],
		"22 - 35" => ["Male" => 0, "Female" => 0],
		"36 - 55" => ["Male" => 0, "Female" => 0]
	);
}
$stats = &$_SESSION["stats"];

function update_stats($gender, $age) {
	global $stats, $ageGroups;
	// define age groups
	if ($age >= 16 && $age <= 21) {
		$stats[$ageGroups["age_group1"]][$gender] += 1;
	} elseif ($age >= 22 && $age <= 35) {
		$stats[$ageGroups["age_group2"]][$gender] += 1;
	} elseif ($age >= 36 && $age <= 55) {
		$stats[$ageGroups["age_group3"]][$gender] += 1;
	}
}


function show_data() {
	
	global $stats, $tickets_sold;
	
	// show 
	echo "<p>Total Tickets Sold: " . $tickets_sold[0] . "</p>";
	echo "<p>Revenue Tickets: " . $tickets_sold[1] . "</p>";
	
	// show age groups and gender data 
	echo "<hr>";
	echo '<table border="1" cellpadding="5" cellspace="0">';
	echo '<tr><th>Age Group</th><th>Gender</th><th>Tickets Sold</th></tr>';

	foreach ($stats as $group => $details) {
		foreach ($details as $gender => $count) {
			echo "<tr>";
			echo "<td>" . $group ."</td>";
			echo "<td>" . $gender . "</td>";
			echo "<td>" . $count . "</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
	
	
	//reset button
	
	echo '<form method="post" action="index.php">';
    echo '<input type="submit" name="reset_form"value="Reset Form">';
    echo '</form>';
}

function resetForm() {
	// Start the session and destroy it to reset form data
	session_start();
	session_unset();
	session_destroy();
	header("Location: index.php");
	exit();
}


function main() {
	//
	global $tickets_sold, $max_capacity;
	
	// get infomation from form
	$name = $_POST["Name"] ?? '';
	$gender = $_POST["Gender"] ?? '';
	$age = intval($_POST["Age"] ?? 0);
	$ticket_category = $_POST["Ticket_Category"] ?? '';

	// check venue Capacity
	if ($tickets_sold[0] >= $max_capacity) {
			// output that venue is at max
			echo "<h4>Sorry Tickets are sold out. </h4>";
			return;
	}
	// 
	update_tickets_sold($ticket_category);	
	//
	track_category_tickets($ticket_category);
	//
	update_stats($gender, $age);
	//
	
	show_data();
}


//check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['submit_button'])) {
			// start program
			main();
		} elseif (isset($_POST['reset_form'])) {
			// reset program
			resetForm();
		}
	}
?>
