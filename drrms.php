<?php 
/*
Write a program that simulates a Disaster Relief Resource Management System for tracking essential resources
such as food, water, and medical kits. The system should store resource information (resourceID, resourceName,
quantity, unitPrice, location) in a text file and perform the following operations:
- Adding new resources
- Updating existing resources
- Deleting resources
- Searching for specific resources
The program must read the data from the text file and store it in an array. Users should be able to input valid
data, which will then be saved back to the file after each operation.
*/
session_start();

function init() {
    $file = fopen('drrms.dat', 'w');
    $headers = ['resourceId', 'resourceName', 'quantity', 'unitPrice', 'location'];
    fputcsv($file, $headers);
    fclose($file);
}




function add() {
    echo "<br><br>";
    echo "<form method='post'>";
    echo "<label>ResourceId: <label><input name='id' type='number' required><br>";
    echo "<label>Resource Name: <label><input name='name' type='text'><br>";
    echo "<label>Quantity: <label><input type='number' name='quantity'><br>";
    echo "<label>Unit Price: <label><input type='number' name='price'><br>";
    echo "<label>Location: <label><input type='text' name='location'><br>";
    echo "<button name='add_button' type=submit'>Enter</button>";
    echo "</form>";



    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_button'])) {
        // information
        $resourcdId = $_POST['id'];
        $resourceName = $_POST['name'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $location = $_POST['location'];
        
        $resources = array($resourcdId, $resourceName, $quantity, $price, $location);
        
        
        $file = fopen('drrms.dat', 'w');
        $headers = ['resourceId', 'resourceName', 'quantity', 'unitPrice', 'location'];
        fputcsv($file, $headers);
        fputcsv($file, $resources);
        fclose($file);
    } else {
        echo "<h3>Failed to add</h3>";
        return;
    }

    echo "<h3>Added resources successfully</h3>";
}

function update() {
    echo "<br><br>";
    echo "<form method='post'>";
    echo "<label>ResourceId: <label><input name='id' type='number' required><br>";
    echo "<button name='update' type=submit'>Enter</button>";
    echo "</form>";

}

function delete() {
    echo "<br><br>";
    echo "<form method='post'>";
    echo "<label>ResourceId: <label><input name='id' type='number' required><br>";
    echo "<button name='delete' type=submit'>Enter</button>";
    echo "</form>";

}

function retrieve() {
    echo "<br><br>";
    echo "<form method='post'>";
    echo "<label>ResourceId: <label><input name='id' type='number' required><br>";
    echo "<button name='retrieve' type=submit'>Enter</button>";
    echo "</form>";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Disaster Relief Resource Management System</title>
    </head>
    <body>
        <h1> Disaster Relief Resource Management System</h1>
        <form method='get'>
            <button name='operation' value='add' type='submit'>Add</button><br>
            <button name='operation' value='update' type='submit'>update</button><br>
            <button name='operation' value='delete' type='submit'>Delete</button><br>
            <button name='operation' value='retrieve' type='submit'>Search</button><br>
        </form>



    </body>
</html>


<?php 
function main() {
    init();
    if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['operation'])) {
        switch ($_GET["operation"]) {
            case "add":
                add();
                break;
            case "update":
                update();
                break;
            case "delete":
                delete();
                break;
            case "retrieve":
                retrieve();
                break;
            default:
                break;
        }
    }    
}
main();

?>


