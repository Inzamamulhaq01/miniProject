<?php
include '../conn.php';

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$user_id = (int) $_GET['user_id']; // Safe casting to prevent SQL injection

$sql = "SELECT principal, rate, tenure, missed_emi, repayment, start_date, bank_name
        FROM loan
        WHERE user_id = $user_id
        ORDER BY created_at DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $loanData = $result->fetch_assoc();
    $principal = floatval($loanData['principal']);
    $rate = floatval($loanData['rate']);
    $tenureYears = intval($loanData['tenure']);
    $tenureMonths = $tenureYears * 12;

    // Monthly interest rate
    $monthlyRate = $rate / (12 * 100);

    // EMI calculation
    $emi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $tenureMonths)) / (pow(1 + $monthlyRate, $tenureMonths) - 1);

    // Number of months passed
    $startDate = new DateTime($loanData['start_date']);
    $now = new DateTime();
    $monthsElapsed = ($now->format('Y') - $startDate->format('Y')) * 12 + ($now->format('m') - $startDate->format('m'));

    // Include this month if we're within last 2 days of the current month
    if ($now->format('d') > (intval($now->format('t')) - 2)) {
        $monthsElapsed++;
    } else {
        $monthsElapsed = max(0, $monthsElapsed); // avoid negative
    }

    $monthsElapsed = min($monthsElapsed, $tenureMonths); // cap at total tenure

    // Total paid so far
    $totalPaid = $emi * $monthsElapsed;

    // Principal paid (approximate with flat EMI method)
    $principalPaid = ($principal / $tenureMonths) * $monthsElapsed;

    // Interest paid
    $interestPaid = $totalPaid - $principalPaid;

    $loanData['emi'] = round($emi, 2);
    $loanData['months_elapsed'] = $monthsElapsed;
    $loanData['total_paid'] = round($totalPaid, 2);
    $loanData['principal_paid'] = round($principalPaid, 2);
    $loanData['interest_paid'] = round($interestPaid, 2);

    // Loan end date
    $endDate = new DateTime($loanData['start_date']);
    $endDate->modify('+' . $tenureYears . ' years');
    $loanData['end_date'] = $endDate->format('Y-m-d');

    // Remaining months
    $remainingMonths = $tenureMonths - $monthsElapsed;

    // Outstanding balance (present value of remaining EMIs)
    if ($remainingMonths > 0) {
        $outstandingBalance = $emi * ((1 - pow(1 + $monthlyRate, -$remainingMonths)) / $monthlyRate);
    } else {
        $outstandingBalance = 0;
    }

    $loanData['outstanding_balance'] = round($outstandingBalance, 2);

    echo json_encode(['success' => true, 'data' => $loanData]);

} else {
    echo json_encode(['error' => 'No loan data found']);
}

$conn->close();
?>
