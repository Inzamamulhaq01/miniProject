<?php

function calculateLoanPayments($params) {
    // Validate required fields
    if (!isset($params['loanAmount'], $params['interestRate'], $params['loanTenure'])) {
        return ["error" => "Missing required parameters."];
    }

    // Get inputs
    $loanAmount = floatval($params['loanAmount']);
    $outstandingBalanceInput = isset($params['outstandingBalance']) ? floatval($params['outstandingBalance']) : $loanAmount;
    $interestRate = floatval($params['interestRate']) / 100;
    $loanTenure = intval($params['loanTenure']);
    $stepUpRate = isset($params['stepUpRate']) ? floatval($params['stepUpRate']) / 100 : 0;
    $prepaymentAmount = isset($params['monthlyPrepayment']) ? floatval($params['monthlyPrepayment']) : 0;
    $prepaymentFrequency = isset($params['prepaymentFrequency']) ? $params['prepaymentFrequency'] : 'none';
    $lumpSumPrepayment = isset($params['lumpSumPrepayment']) ? floatval($params['lumpSumPrepayment']) : 0;
    $lumpSumMonth = isset($params['lumpSumMonth']) ? intval($params['lumpSumMonth']) : null;

    $monthlyInterestRate = $interestRate / 12;
    $outstandingBalance = $outstandingBalanceInput;

    // EMI calculation
    $emi = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -($loanTenure * 12)));

    $totalPaid = 0;
    $year = 1;
    $month = 1;
    $paymentDetails = [];
    $yearTotalPayment = 0;

    $originalTotalPayment = $emi * 12 * $loanTenure; // Original Total (no prepayment)

    while ($outstandingBalance > 0) {
        $monthlyInterest = $outstandingBalance * $monthlyInterestRate;
        $principalComponent = $emi - $monthlyInterest;

        // Determine extra prepayment
        $extraPrepayment = 0;
        if ($prepaymentAmount > 0) {
            if ($prepaymentFrequency === "monthly") {
                $extraPrepayment = $prepaymentAmount;
            } elseif ($prepaymentFrequency === "quarterly" && $month % 3 === 0) {
                $extraPrepayment = $prepaymentAmount;
            } elseif ($prepaymentFrequency === "yearly" && $month % 12 === 0) {
                $extraPrepayment = $prepaymentAmount;
            }
        }

        // Update outstanding balance
        $outstandingBalance -= ($principalComponent + $extraPrepayment);

        // Lump sum payment
        if ($lumpSumMonth !== null && $month === $lumpSumMonth && $lumpSumPrepayment > 0) {
            $outstandingBalance -= $lumpSumPrepayment;
            $totalPaid += $lumpSumPrepayment;
            $yearTotalPayment += $lumpSumPrepayment;
        }

        $yearTotalPayment += $emi + $extraPrepayment;
        $totalPaid += $emi + $extraPrepayment;

        if ($outstandingBalance <= 0) {
            break;
        }

        // After 12 months, complete a year
        if ($month % 12 === 0) {
            $paymentDetails[] = [
                "year" => $year,
                "emi" => round($emi, 2),
                "totalPaidInYear" => round($yearTotalPayment, 2)
            ];
            // Increase EMI based on Step-Up
            $emi += $emi * $stepUpRate;
            $year++;
            $yearTotalPayment = 0;
        }

        $month++;
    }

    // If loan closed in the middle of the year
    if ($yearTotalPayment > 0) {
        $paymentDetails[] = [
            "year" => $year,
            "emi" => round($emi, 2),
            "totalPaidInYear" => round($yearTotalPayment, 2)
        ];
    }

    // Final savings calculation
    $totalSavings = $originalTotalPayment - $totalPaid;
    $savingsPercentage = ($totalSavings / $originalTotalPayment) * 100;

    // Prepare final result
    return [
        "paymentSchedule" => $paymentDetails,
        "summary" => [
            "originalTotalPayment" => round($originalTotalPayment, 2),
            "actualTotalPayment" => round($totalPaid, 2),
            "totalSavings" => round($totalSavings, 2),
            "savingsPercentage" => round($savingsPercentage, 2)
        ]
    ];
}





$params = [
    "loanAmount" => 5000000,
    "outstandingBalance" => 5000000,
    "interestRate" => 8,
    "loanTenure" => 20,
    "stepUpRate" => 5,
    "monthlyPrepayment" => 5000,
    "prepaymentFrequency" => "monthly",
    "lumpSumPrepayment" => 100000,
    "lumpSumMonth" => 1
];

$result = calculateLoanPayments($params);

echo "<pre>";
print_r($result);
echo "</pre>";

?>
