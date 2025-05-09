<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: ../login/");
    exit();
}

include '../conn.php';

$user_id = (int) $_SESSION['user_id'];

$sql = "SELECT newuser FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ((int)$row['newuser'] == 1) {
        header("Location: ../forms/"); // Redirect new users to the forms page
        exit();
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="css/card.css">
  <link rel="stylesheet" href="style.css">

</head>

<body>

  <div id="sidebar">

    <div class="donut-container">
      <svg class="donut" width="250" height="250">
        <circle class="donut-track" cx="125" cy="125" r="100" />
        <circle class="donut-indicator" cx="125" cy="125" r="100" />
      </svg>
      <div class="donut-counter">0%</div>
    </div>
    <h2>Loan health status</h2>

    <div class="progress-bar-container">
      <h4 class="barhead">ETI</h4>
      <div class="progress-bar">
        <div class="progress-indicator color-1"></div>
      </div>
      <div class="progress-counter">0%</div>

    </div>
    <div class="progress-bar-container">
      <h4 class="barhead">Emi</h4>
      <div class="progress-bar">
        <div class="progress-indicator color-2"></div>
      </div>
      <div class="progress-counter">0%</div>
    </div>
    <div class="progress-bar-container">
      <h4 class="barhead">Creditscore</h4>
      <div class="progress-bar">
        <div class="progress-indicator color-3"></div>
      </div>
      <div class="progress-counter">0%</div>
    </div>
    <br>
    <small><i class="fa fa-info-circle" style="color: white;"></i>
      To get more deatils insigths
      <button><a href="score">get my loan health</a></button>
    </small>


  </div>
  <section>
    <div class="wrapper">


      <div class="card-details">
        <div id="card-header">
          <h3>Overview</h3>

          <h5> your loan deatils</h5>
          <hr>
        </div>
        <div class="card shimmer">

          <div class="card-inner">
            <div class="front">
              <img
                src="https://media.istockphoto.com/id/1411978680/vector/world-map-of-6-continents-atlantic-ocean.jpg?s=612x612&w=0&k=20&c=i_Mp_nKTnh1STzbe-kHe9VL1Vxr9vs3XUKzFgDZ730Y="
                class="map-img">
              <div class="row">
                <img
                  src="https://png.pngtree.com/png-vector/20231223/ourmid/pngtree-credit-card-chip-shopping-png-image_11198229.png"
                  width="40px">
                <img src="https://companieslogo.com/img/orig/INDIANB.NS_BIG-f675f730.png?t=1720244492" width="90px">
              </div>
              <div class="row card-no">
                <i>Your Loan, Your Dreams
                </i>

              </div>
              <div class="row card-holder">
                <p>LOAN HOLDER</p>
                <p>Start Date</p>
                <p>End Date</p>
              </div>
              <div class="row name">
                <p id="username">JOE ALISON</p>
                <p id="startdate">10 / 25</p>
                <p id="enddate">10 / 25</p>
              </div>
            </div>

          </div>

        </div>
        <div id="loan-details">
          <h4>Loan details</h4>
          <hr>
          <div id="add-details">
            <div class="row">
              <h5>Amount sanctioned</h5>
              <h3>100000</h3>
            </div>
            <div class="row">
              <h5>Rate of Interest</h5>
              <h3>10%</h3>
            </div>
            <div class="row">
              <h5>tenure</h5>
              <h3>10 yrs</h3>
            </div>
            <div class="row">
              <h5>startdate</h5>
              <h3>jun 2025</h3>
            </div>
          </div>


        </div>
      </div>
      <!-- <div id="notifications">notifications</div> -->
      <div id="right-wrapper">
        <div id="detailedinsigts"><span>
            <h3>Detailed insigths</h3>
            <hr>
          </span>
          <div id="insights">
            <div>
              <span>
                <h6>Total paid</h6>
                <h5>$123456</h5>
              </span>
              <span>
                <h6>Outstanding</h6>
                <h5>$1233</h5>
              </span>
              <span>
                <h6>Total Interest</h6>
                <h5>$12387</h5>
              </span>

            </div>
            <div>
              <span>
                <h6>No of months</h6>
                <h5>$12</h5>
              </span>
              <span>
                <h6>prepayment</h6>
                <h5>$0</h5>
              </span>
              <span>
                <h6>Principal paid</h6>
                <h5>$12334</h5>
              </span>

            </div>

            <canvas id="myChart" width="250px"></canvas>
          </div>
        </div>
        <hr>
        <div id="chartContainer">
          <canvas id="paymentChart"></canvas>
        </div>
      </div>



    </div>
    </div>
  </section>



  <script>
    async function fetchData() {
      try {
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;

        const userResponse = await fetch(`../apis/users.php?user_id=${userId}`);
        const userData = (await userResponse.json()).data;

        const loanResponse = await fetch(`../apis/loans.php?user_id=${userId}`);
        const loanData = (await loanResponse.json()).data;
       console.log(loanData);
        const monthlyIncome = parseFloat(userData.salary);
        const monthlyExpenditure = monthlyIncome - parseFloat(userData.savings);
        const emi = parseFloat(loanData.emi);
        const loanAgeMonths = parseInt(loanData.months_elapsed);
        const total_paid = parseInt(loanData.total_paid);
        const outstanding_balance = parseInt(loanData.outstanding_balance);
        const interest_paid = parseInt(loanData.interest_paid);
  
        const totalRepayment = parseFloat(loanData.total_paid);
        const principalPaid = parseFloat(loanData.principal_paid);
        const expectedTotalRepayment = emi * loanAgeMonths;
        const eti = emi / monthlyIncome;
        const creditScore = parseInt(userData.credit_score);
        const missedEmiCount = parseInt(loanData.missed_emi);
        const emergencyFund = parseFloat(userData.extra_fund);
        const emergencyRatio = emergencyFund / monthlyExpenditure;
        //console.log(totalRepayment)
        let score = 0;
        if (eti <= 0.4) score += 2;
        else if (eti <= 0.5) score += 1;
        if (creditScore >= 750) score += 2;
        else if (creditScore >= 650) score += 1;
        if (missedEmiCount === 0) score += 2;
        else if (missedEmiCount <= 2) score += 1;
        if (emergencyRatio >= 3) score += 2;
        else if (emergencyRatio >= 1) score += 1;
        if (totalRepayment > expectedTotalRepayment * 1.05) score += 1;
        document.getElementById('username').textContent = userData.name;
        document.getElementById('startdate').textContent = formatDate(loanData.start_date);
        document.getElementById('enddate').textContent = formatDate(loanData.end_date);
        animateLinearProgressMultiple([100 - eti * 10, 100 - missedEmiCount * 100, ( creditScore) / 900 * 100], 2000);
        animateDonut(score * 10, 2000);
        document.getElementById('insights').innerHTML=`
        <div>
              <span>
                <h6>Total paid</h6>
                <h5>${total_paid}</h5>
              </span>
              <span>
                <h6>Outstanding</h6>
                <h5>${outstanding_balance}</h5>
              </span>
              <span>
                <h6>Total Interest</h6>
                <h5>${interest_paid}</h5>
              </span>

            </div>
            <div>
              <span>
                <h6>No of months</h6>
                <h5>$12</h5>
              </span>
              <span>
                <h6>prepayment</h6>
                <h5>$0</h5>
              </span>
              <span>
                <h6>Principal paid</h6>
                <h5>${principalPaid}</h5>
              </span>

            </div>
             <canvas id="myChart" width="250px"></canvas>
        `;
        document.getElementById('add-details').innerHTML= `
            <div class="row">
              <h5>Amount sanctioned</h5>
              <h3>${loanData.principal}</h3>
            </div>
            <div class="row">
              <h5>Rate of Interest</h5>
              <h3>${loanData.rate}%</h3>
            </div>
            <div class="row">
              <h5>tenure</h5>
              <h3>${loanData.tenure} yrs</h3>
            </div>
            <div class="row">
              <h5>startdate</h5>
              <h3>${formatDate(loanData.start_date)}</h3>
            </div>`;
        // document.getElementById('loan-details').innerHTML = `
        //         <strong>Principal Paid:</strong> ₹${loanData.principal_paid}<br>
        //         <strong>Tenure:</strong> ${loanData.tenure} years<br>
        //         <strong>ROI:</strong> ${loanData.rate}%<br>
        //         <strong>EMI:</strong> ₹${loanData.emi}<br>
        //         <strong>Total Paid:</strong> ₹${loanData.total_paid}
        //     `;

        //     document.getElementById('notifications').innerHTML = `
        //     <strong>Financial Health Score:</strong> ${score}/9<br>
        //     <strong>Summary:</strong> ${score >= 7 ? 'Excellent' : score >= 5 ? 'Good' : 'Needs Improvement'}
        // `;


        const ctx1 = document.getElementById('paymentChart').getContext('2d');
        const barChart = new Chart(ctx1, {
          type: 'bar',
          data: {
            labels: ['Amount Paid', 'Principal Paid'],
            datasets: [{
              label: 'Amount Paid vs Principal Paid',
              data: [totalRepayment, principalPaid],
              backgroundColor: ['#34D399', '#D1D5DB'],
              borderColor: ['#2F855A', '#6B7280'],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Amount in ₹'
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'Categories'
                }
              }
            },
            plugins: {
              tooltip: {
                callbacks: {
                  label: function (tooltipItem) {
                    return `${tooltipItem.label}: ₹${tooltipItem.raw.toFixed(2)}`;
                  }
                }
              }
            }
          }
        });

        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['Principal Paid', 'Interest Paid', 'Remaining Balance'],
            datasets: [{
              label: 'Loan Progress',
              data: [totalRepayment, principalPaid, 30],  // example percentages
              backgroundColor: ['#212775', '#353d77', '#3b4d8d'],
              borderWidth: 1,
              cutout: '50%',
              circumference: 360,
              rotation: -90
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  boxWidth: 20,   // controls size of color box
                  padding: 15     // spacing between items
                }
              },
              tooltip: {
                callbacks: {
                  label: function (context) {
                    const label = context.label || '';
                    const value = context.raw || '';
                    return `${label}: ${value}%`;
                  }
                }
              }
            }
          }
        });

      } catch (error) {
        console.error('Error fetching data:', error);
      }
    }
    function formatDate(dateString) {
      const [year, month] = dateString.split('-');
      const shortYear = year.slice(-2);
      const monthNoLeadingZero = parseInt(month, 10);
      return `${monthNoLeadingZero}/${shortYear}`;
    }

    function animateDonut(targetValue, duration) {
      const counter = document.querySelector('.donut-counter');
      const indicator = document.querySelector('.donut-indicator');
      let current = 0;
      const interval = 10;
      const step = targetValue / (duration / interval);
      const circumference = 2 * Math.PI * 100;  // updated radius

      const timer = setInterval(() => {
        current += step;
        if (current >= targetValue) {
          current = targetValue;
          clearInterval(timer);
        }
        counter.textContent = `${Math.round(current)}%`;
        const offset = circumference * (1 - current / 100);
        indicator.style.strokeDashoffset = offset;
      }, interval);
    }

    // Example usage:

    function animateLinearProgressMultiple(targetValues, duration) {
      const counters = document.querySelectorAll('.progress-counter');
      const indicators = document.querySelectorAll('.progress-indicator');

      const currentValues = new Array(targetValues.length).fill(0);
      const interval = 10;
      const steps = targetValues.map(target => target / (duration / interval));

      const timer = setInterval(() => {
        let allDone = true;

        currentValues.forEach((current, index) => {
          if (current < targetValues[index]) {
            currentValues[index] += steps[index];
            if (currentValues[index] >= targetValues[index]) {
              currentValues[index] = targetValues[index];
            } else {
              allDone = false;
            }
            if (counters[index]) {
              counters[index].textContent = `${Math.round(currentValues[index])}%`;
            }
            if (indicators[index]) {
              indicators[index].style.width = `${currentValues[index]}%`;
            }
          }
        });

        if (allDone) clearInterval(timer);
      }, interval);
    }

    // Example: animate to 75% over 2 seconds



    fetchData();
  </script>
</body>

</html>