<?php 
/*
Basic Calculator
Create a web form that takes two numbers and an operator (+, -, *, /) and returns the result.
*/

?>
<!DOCYTPE html>
<html>
<head></head>
<body>
    <h2>Calculator</h2>
    <form method="get">
        <label>Number 1</label><input name='num1' type='number'><br>
        <label>Number 2</label><input name='num2' type='number'></br>
        <select name='sign'>
            <option value='add'>Addition</option>
            <option value='sub'>Subtraction</option>
            <option value='mul'>Multiplication</option>
            <option value='div'>Division</option>
        </select>
        <button type='submit'>Enter</button>


    </form>
</body>

</html>


<?php

$num1 = $_GET["num1"];
$num2 = $_GET['num2'];
$sign = $_GET['sign'];

$ans;
switch ($sign) {
    case 'add':
        $ans =  $num1 + $num2;
        break;
    case 'sub':
        $ans =  $num1 - $num2;
        break;
    case 'mul':
        $ans = $num1 * $num2;
        break;
    case 'div':
        $ans = $num1 / $num2;
}

echo "The Answer is $ans";

?>



