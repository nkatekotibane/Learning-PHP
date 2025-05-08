<?php 
/*
Palindrome Checker
Write a PHP script to check whether a string is a palindrome (reads the same forward and backward).
*/

function palindrome_checker($word) {
    $word_in_list_form = str_split($word);
    $i = 0;
    $j = strlen($word) - 1;

    while ($i <= $j) {
        $char1 = $word_in_list_form[$i];
        $char2 = $word_in_list_form[$j];
        if ($char1 !== $char2) {
            return false;
        }
        $i++;
        $j--;
    }

    return true;

}


// list of words
$words = ["racecar", "madam", "hello", "level", "laptop", "civic", 
        "kayak", "chatbot", "machine", "pwn", "refer"];
foreach ($words as $word) {
    $check = palindrome_checker($word);
    if ($check) {
        echo "$word is a palindrome\n";
    } else {
        echo "$word is NOT a palindrome\n";
    }
}



?>