<?php 

/*

Write a program to calculate the monthly bank charges for Capitec Bank savings account holder based on
their withdrawal details. The program should consider various factors specific to Capitec Bank, such as the
monthly admin fee, withdrawal charges, and overdraft penalties. The program should prompt the user to input
the account holder's name, current balance, number of withdrawals made, and withdrawal amounts for the
month.

Calculate the total charges using the formula:
total_charges = monthly_admin_fee + (withdrawals * withdrawal_charge) + overdraft_penalty, where
overdraft_penalty is R375 if the balance falls below R0.00.

For Capitec savings accounts, the withdrawal charge is R22.50 per withdrawal after the first four free withdrawals,
and the monthly admin fee is R5.00.
Display the calculated charges for the account holder in a detailed format showing
• Account holder's name
• Initial balance
• Number of withdrawals
• Breakdown of charges (admin fee, withdrawal charges, penalties)
• Final balance after deducting all charges
NB: The program should also validate that withdrawal amounts do not exceed the available balance unless the
account holder has overdraft facility.

*/
Class Monthly_Charges {
    // every private property and method
    private $withdrawal_charge = 22.50; // first 4 free
    private $montly_admin_fee = 5; // in Rands
    private $account_holder = NULL; // string
    private $current_balance = 0.0; // float
    private $number_of_withdrawals_made = 0; // int
    private $withdrawals_amount = 0.0; // float
    private $overdraft_penalty = 375;

    public function setter($name, $balance, $num, $amount) {
        $this->account_holder = $name;
        $this->current_balance = $balance;
        $this->number_of_withdrawals_made = $num;
        $this->withdrawals_amount = $amount; 
    }

    private function withdrawal_charges() {
        if ($this->number_of_withdrawals_made <= 4) {
            return FALSE;
        } elseif ($this->number_of_withdrawals_made > 4) {
            $num = $this->number_of_withdrawals_made - 4; // number of charges

            return $this->number_of_withdrawals_made * $num;
        } 
    }

    private function total_charges($Overdraft) {
        // overdratf ; BOOL true/false
        if (!$Overdraft) {
            $this->overdraft_penalty = 0; 
        }

        // calculate withdrawal charges
        if ($this->withdrawal_charges() === FALSE) {
            $withdrawal_charges = 0;
        } else {
            $withdrawal_charges = $this->withdrawal_charges();
        }

        return $this->montly_admin_fee + $withdrawal_charges + $this->overdraft_penalty;
    }

    public function displayInfo() {
        /*
        Display the calculated charges for the account holder in a detailed format showing
        • Account holder's name
        • Initial balance
        • Number of withdrawals
        • Breakdown of charges (admin fee, withdrawal charges, penalties)
        • Final balance after deducting all charges
        NB: The program should also validate that withdrawal amounts do not exceed the available balance unless the
        account holder has overdraft facility.
        */
        $final_balance = $this->current_balance - $this->withdrawals_amount;
        if ($final_balance < 0.0) {
            $final_balance = $final_balance - $this->total_charges(FALSE);
        } else {
            $final_balance = $final_balance - $this->total_charges(TRUE);
        }
        
        $with = $this->withdrawal_charges();

        echo "<p>Account holder:  {$this->account_holder}</p>";
        echo "<p>Initial balance: {$this->current_balance}</p>";
        echo "<p>Number of withdrawals:  {$this->number_of_withdrawals_made}</p>";
        echo "<h3>Charges</h3>";
        echo "<p>Admin fee: R{$this->montly_admin_fee}</p><p>Withdrawal Charges: R{$with}</p>";
        echo "<p></p>";
        echo "<p>Final Balance: R{$final_balance}</p>";
        
    }

}
?>

<!doctype html>
<html>
<head></head>
<body>
    <h1>Calculate bank charges</h1>
    <form method='post'>
        <label>Enter Account Holder: </label><input name='acc_holder' type='text'><br>
        <label>Enter Current Balance: </label><input name='balance' type='number'><br>
        <label>Enter Number of withdrawals Made: </label><input name='num' type='number'><br>
        <label>Enter Withdrawals Amount: </label><input name='amount' type='number'><br>
        <button type='submit' name='getinfo'>Submit</button>
    </form>
</body>
</html>

<?php

function main() {
    if (isset($_SERVER['REQUEST_METHOD']) == "POST" && isset($_POST['getinfo'])) {
        $name = $_POST['acc_holder'];
        $balance = $_POST['balance'];
        $num = $_POST['num'];
        $amount = $_POST['amount'];
    
        $bank = new Monthly_Charges();
        $bank->setter($name, $balance, $num, $amount);
        $bank->displayInfo();
    } 
}

main();
?>