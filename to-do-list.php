<?php 
//session_start();

$id = 0;

function init() {
    $file = fopen("list.dat", 'w');
    $headers = ["id", "item","description", "due_date"];
    fputcsv($file, $headers);
    fclose($file);
}


function store() {
    global $id;
    #$id = $id + 1;
    $id++;
    $item = $_POST['Item'];
    $description = $_POST['Description'];
    $date = $_POST['Due_date'];

    $data = array($id, $item, $description, $date);

    $file = fopen("list.dat", 'w');
    $headers = ["id", "item","description", "due_date"];
    fputcsv($file, $headers);
    
    fputcsv($file, $data);
    fclose($file);
}

function view() {
    $transactions = [];
    if (file_exists("list.dat")) {
        $file = fopen("list.dat", 'r');
        $headers = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $transactions[] = array_combine($headers, $row);
        }
        fclose($file);
    }
    return $transactions;
}


function main() {
    if (!file_exists("list.dat")) {
        init();
    }
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['to-do'])) {
            store();
        }
    }
}

main();

?>

<!DOCYTYPE html>
<html>
<head></head>
<body>
    <h1> To - do list </h1>
    <form method='post'>
    <label>Item</label><input type='text' name='Item'><br>
    <label>Description</label><input type='textbox' name='Description'><br>
    <label>Due Date</label><input type='date' name='Due_date'></br>
    <button type='submit' name='to-do' value='submi'>ENTER</button>
    </form>


    <table border='1' cellpadding='5' cellspace='0'>
        <thead>
            <tr>
                <th>ID</th><th>ITEM</th><th>Description</th><th>Due date</th>
            </tr>
        </thead>
        <tbody>
    <?php 
            $list = view();
            foreach ($list as $trans): ?>
                <tr>
                    <td><?php echo $trans["id"] ?></td>
                    <td><?php echo $trans["item"]?></td>
                    <td><?php echo $trans["description"] ?></td>
                    <td><?php echo $trans["due_date"]  ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
    </table>
    

</body>
</html>