<?php 
/*
Fibonacci Sequence Generator
Generate and display the first n Fibonacci numbers.



F_{0}=0 ,  F_{1}=1
and
 F_{n}= F_{n-1} + F_{n-2} ..
 for n > 1.
*/



function fib($n) {
    if ($n == 0 ) {
        return 0;
    } elseif ($n == 1) {
        return 1;
    }
    return fib($n - 1) + fib($n - 2);
}
$n = 19;

for ($i = 0; $i <= $n; $i++) {
    echo fib($i). " ";
}
echo "\n";



?>