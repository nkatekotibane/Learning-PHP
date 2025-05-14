<?php
session_start();
main();

$is_logged_in = isset($_SESSION['user']);

// file information
function user_data() {
    $users = [];
    if (file_exists("users.dat")) {
        $userfile = fopen("users.dat", 'r');
        $headers = fgetcsv($userfile);

        while (($row = fgetcsv($userfile)) !== false) {
            $users[] = array_combine($headers, $row);
        }
        fclose($userfile);  
    }
    return $users;
}

function transaction_data() {
    $transactions = [];
    if (file_exists("transactions.dat")) {
        $transactionsfile = fopen("transactions.dat", 'r');
        $headers = fgetcsv($transactionsfile);

        while (($row = fgetcsv($transactionsfile)) !== false) {
            $transactions[] = array_combine($headers, $row);
        }
        fclose($transactionsfile);
    }
    return $transactions;
}

$page = $_GET['page'] ?? 'login';
?>
<!DOCTYPE html>
<html>
<head><title>Payment Processing System</title></head>
<body>
<?php if (!$is_logged_in): ?>
    <?php if ($page === 'login'): ?>
        <h2>Login</h2>
        <form method="POST">
            <label for="username">Username: </label><input type="text" name="username" required><br>
            <label for="password">Password: </label><input type="password" name="password" required><br>
            <button name="sign-in" value="login">sign in</button>
            <p>New user? <a href="?page=register">Sign up</a></p>
        </form>
    <?php elseif ($page === 'register'): ?>
        <h2>Sign Up</h2>
        <form method="GET">
            <label for="username">Username: </label><input type="text" name="username" required><br>
            <label for="password">Password: </label><input type="password" name="password" required><br>
            <button name="signup" value="register">sign in</button>
            <p>Aleardy have account user? <a href="?page=login">Sign In</a></p>
        </form>
    <?php endif; ?>
<?php else: ?>
    <h2>Welcome <?php echo $_SESSION['user']["username"]; ?> </h2>
    <p>Balance <?php echo $_SESSION['user']['balance'] ?> </p>
    <form method="POST">
        <button name="logout" value="logout">Logout</button>
    </form>

    <!-- create a form for making payment -->
    <h3>Payment</h3>
    <form method="post">
        <label for="amount">Amount: </label><input type="number" name="amount" required><br>
        <label for="recipient">Recipient: </label><input type="text" name="recipient" required><br>
        <select name="payment_method" required>
            <option value="credit_card">Credit Card (2%)</option>
            <option value="PayPal">Paypal (4%)</option>
            <option value="CryptoCurrency">cryptocurrency (5%)</option>
        </select>
        <button name="make_payment" value="pay">PAY</button>
    </form>

    <!-- to request refunds -->
    <h3>Refunds</h3>
    <form method="post">
        <label>Transaction ID: </label><input type="number" name="transaction_id" required><br>
        <button name="refund">Request Refund</button>
    </form>

    <!-- transaction history -->
    <h3>Transaction History</h3>
    <table border='1' cellpadding='5' cellspace='0'>
        <thead>
            <tr>
                <th>ID</th><th>User ID</th><th>Amount</th><th>Fee</th><th>Total Amount</th><th>Payment Method</th><th>Recipient</th><th>Refunded</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $transactions = transaction_data();
        foreach ($transactions as $trans): ?>
            <tr>
                <td><?php echo $trans["transaction_id"] ?></td>
                <td><?php echo $trans["user_id"]?></td>
                <td><?php echo $trans["amount"] ?></td>
                <td><?php echo $trans["fee"]  ?></td>
                <td><?php echo $trans["total_amount"]?></td>
                <td><?php echo $trans["payment_method"]?></td>
                <td><?php echo $trans["recipient"]?></td>
                <td><?php echo $trans["refunded"]?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>

<?php
function databases() {
    $userfile = fopen("users.dat", "w");
    $transactionfile = fopen("transactions.dat", "w");

    $user_headers = ["user_id", "username", "password_hash", "balance"];
    $transaction_headers = ["transaction_id", "user_id", "amount", "fee", 
                            "total_amount", "payment_method", "recipient", "refunded"];

    fputcsv($userfile, $user_headers);
    fputcsv($transactionfile, $transaction_headers);

    fclose($userfile);
    fclose($transactionfile);
}

function encode_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

class Authentication {
    function sign_up($username, $password) {
        $file = fopen("users.dat", 'a');
        $pass = encode_password($password);
        $balance = 1020;
        $user_id = $this->get_next_user_id();
        $data = [$user_id, $username, $pass, $balance];
        fputcsv($file, $data);
        fclose($file);
        return $user_id;
    }

    function sign_in($username, $password) {
        $file = fopen("users.dat", "r");
        $headers = fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $user = array_combine($headers, $row);
            if ($user["username"] === $username && password_verify($password, $user["password_hash"])) {
                fclose($file);
                return $user;
            }
        }
        fclose($file);
        return -1;
    }

    private function get_next_user_id() {
        $users = user_data(); // fixed function call
        if (count($users) > 0) {
            $max_id = max(array_column($users, 'user_id'));
            return $max_id + 1;
        } else {
            return 1;
        }
    }
}

class PaymentManagement {
    public $amount, $balance, $user_id, $recipient, $transaction_id, $total_amount, $payment_method;
    public $crypto_fee = 0.05, $paypal_fee = 0.04, $credit_card_fee = 0.02;

    function __construct($amount, $recipient, $balance, $user_id, $payment_method) {
        $this->amount = (float)$amount;
        $this->balance = (float)$balance;
        $this->user_id = $user_id;
        $this->recipient = $recipient;
        $this->payment_method = $payment_method;
        $this->transaction_id = $this->get_next_transaction_id();
    }

    function make_payments() {
        if ($this->fraud_detection() === -1) {
            echo "Fraud Detected";
            return;
        }

        $fee = 0;
        switch ($this->payment_method) {
            case 'credit_card': $fee = $this->credit_card_fee; break;
            case 'PayPal': $fee = $this->paypal_fee; break;
            case 'CryptoCurrency': $fee = $this->crypto_fee; break;
        }

        $this->total_amount = $this->amount + ($this->amount * $fee);
        if ($this->balance < $this->total_amount) {
            echo "Insufficient balance.";
            return;
        }

        $this->deduct_store($fee);
        echo "Payment successful!";
    }

    function fraud_detection() {
        return $this->amount >= 100000 ? -1 : 0;
    }

    function deduct_store($fee) {
        $this->balance -= $this->total_amount;
        $refunded = "no";

        $data = [$this->transaction_id, $this->user_id, $this->amount, $fee,
                 $this->total_amount, $this->payment_method, $this->recipient, $refunded];

        $file = fopen("transactions.dat", "a");
        fputcsv($file, $data);
        fclose($file);

        $this->update_user_balance($this->user_id, $this->balance);
    }

    private function get_next_transaction_id() {
        $transactions = transaction_data();
        return count($transactions) > 0 ? max(array_column($transactions, 'transaction_id')) + 1 : 1;
    }

    private function update_user_balance($user_id, $new_balance) {
        $users = user_data();
        $userfile = fopen("users.dat", "w");
        $headers = ["user_id", "username", "password_hash", "balance"];
        fputcsv($userfile, $headers);
        foreach ($users as $user) {
            if ($user["user_id"] == $user_id) {
                $user["balance"] = $new_balance;
            }
            fputcsv($userfile, $user);
        }
        fclose($userfile);
        $_SESSION['user']['balance'] = $new_balance;
    }
}

function refund() {
    $transaction_id = $_POST['transaction_id'];
    $user = $_SESSION['user'];
    $updated_transactions = [];
    $refunded = false;

    $file = fopen("transactions.dat", "r");
    $headers = fgetcsv($file);
    while (($row = fgetcsv($file)) !== false) {
        $trans = array_combine($headers, $row);
        if ($trans["transaction_id"] == $transaction_id && $trans["user_id"] == $user["user_id"]) {
            if ($trans["refunded"] === "yes") {
                echo "Transaction already refunded.";
                fclose($file);
                return;
            } else {
                $trans["refunded"] = "yes";
                $refunded = true;
                $user["balance"] += (float)$trans["total_amount"]; // fixed from total
            }
        }
        $updated_transactions[] = $trans;
    }
    fclose($file);

    $file = fopen("transactions.dat", "w");
    fputcsv($file, $headers);
    foreach ($updated_transactions as $trans) {
        fputcsv($file, $trans);
    }
    fclose($file);

    $users = [];
    $userfile = fopen("users.dat", "r");
    $user_headers = fgetcsv($userfile);
    while (($row = fgetcsv($userfile)) !== false) {
        $usr = array_combine($user_headers, $row);
        if ($usr["user_id"] == $user["user_id"]) {
            $usr["balance"] = $user["balance"];
        }
        $users[] = $usr;
    }
    fclose($userfile);

    $userfile = fopen("users.dat", "w");
    fputcsv($userfile, $user_headers);
    foreach ($users as $usr) {
        fputcsv($userfile, $usr);
    }
    fclose($userfile);

    $_SESSION['user'] = $user;
    echo $refunded ? "Refund processed successfully." : "Transaction not found or unauthorized.";
}

function main() {
    if (!file_exists("users.dat") || !file_exists("transactions.dat")) {
        databases();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['sign-in'])) {
            $auth = new Authentication();
            $user = $auth->sign_in($_POST['username'], $_POST['password']);
            if ($user !== -1) {
                $_SESSION['user'] = $user;
            } else {
                echo "Invalid login credentials.";
            }
        }

        if (isset($_POST['make_payment'])) {
            $user = $_SESSION['user'];
            $payment = new PaymentManagement($_POST['amount'], $_POST['recipient'], $user['balance'], $user['user_id'], $_POST['payment_method']);
            $payment->make_payments();
        }

        if (isset($_POST['refund'])) {
            refund();
        }

        if (isset($_POST['logout'])) {
            session_destroy();
            header("Location: index.php");
            exit;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['signup'])) {
        $auth = new Authentication();
        $new_user_id = $auth->sign_up($_GET['username'], $_GET['password']);
        echo "New user created with ID: " . $new_user_id . ".  Please sign in.";
    }
}
?>
