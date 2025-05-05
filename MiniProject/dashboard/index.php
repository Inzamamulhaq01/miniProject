<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: ../login/");
    exit();
}
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
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background-color: #f8f6f2;
      display: flex;
      height: 100vh;
      background-color: #212775;
      overflow: hidden;
    }

    #sidebar {
      width: 30%;
      /* background-color: #212775 ; */

      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    #sidebar h2 {
      color: white;
      font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
      animation: fadeout 1s;
      width: 100%;
      margin-left: 15px;

    }

    section {
      width: 80%;
      background: white;
      padding: 50px;
      border-radius: 70px 0 0 70px;
      margin-top: 6%;
      animation: fadeleft 2s alternate
        /* height: inherit; */
    }

    .wrapper {
      display: grid;
      grid-template-columns: 1fr 1.5fr;
      /* grid-template-rows: 1fr 1fr; */
      row-gap: 30px;
    }

    /* .wrapper div {
            /* height: 200px; */
    /* width: 90%; */
    /* background-color: white; */
    /* border: 1px solid #282c94; */
    /* display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            flex-direction: column; */
    /* }  */
    .donut-container {
      position: relative;
      width: 250px;
      /* increased size */
      height: 250px;
    }

    .donut {
      transform: rotate(-90deg);
    }

    .donut-track {
      fill: none;
      stroke: #3b4d8d;
      stroke-width: 20;
      /* thicker stroke for big circle */
    }

    .donut-indicator {
      fill: none;
      stroke: #fff;
      stroke-width: 20;
      stroke-linecap: round;
      stroke-dasharray: 628;
      /* 2πr ≈ 2 * 3.14 * 100 = ~628 */
      stroke-dashoffset: 628;
      transition: stroke-dashoffset 0.3s linear;
    }

    .donut-counter {
      position: absolute;
      top: 50%;
      left: 50%;
      font-style: italic;
      transform: translate(-50%, -50%);
      font-size: 36px;
      /* larger font */
      font-weight: bold;
      color: #a0c4e0;
    }

    .progress-bar-container {
      width: 100%;
      max-width: 300px;
      /* max width of the progress bar */
      position: relative;
      margin: 20px auto;
    }

    .barhead {
      color: #fff;
      font-size: 12px;
      font-style: italic;
      padding: 5px;
    }

    .progress-bar {
      width: 100%;
      height: 10px;
      background-color: #353d77;
      border-radius: 15px;
      overflow: hidden;
    }

    .progress-indicator {
      height: 100%;
      width: 0%;
      /* background-color: #00b894 ; */
      border-radius: 15px;
      transition: width 0.3s ease;
    }

    .color-1 {
      background-color: #f39c12;
      /* teal green */
    }

    .color-2 {
      background-color: #4b9eaf;
      /* soft red */
    }

    .color-3 {
      background-color: #00b894;
      /* yellow */
    }

    .color-4 {
      background-color: #4D96FF;
      /* blue */
    }

    .progress-counter {
      position: absolute;
      /* top: 10px; */
      left: - 50%;
      font-weight: 100;
      padding: 4px;
      right: 0;
      /* transform: translate(-50%, -50%); */
      font-size: 14px;
      font-weight: bold;
      color: #fff;
    }

    .donut-container {
      opacity: 0;
      animation: fadeIn 1500ms forwards;
    }

    .progress-bar-container {
      animation: fadeout 2s forwards;
    }

    #sidebar small {
      padding: 3px;
      color: grey;
    }

    #sidebar button {
      font-weight: 500;

      padding: 8px;
      background-color: #353d77;
      color: white;
      border-radius: 12px;
      border: none;
      cursor: pointer;
    }

    @keyframes fadeout {
      0% {
        /* opacity: 0/3; */
        transform: translateX(-10%);
      }

      100% {
        opacity: 1;
        /* transform: translateX(30%); */
      }
    }

    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(-50%);
      }

      100% {
        opacity: 1;
      }
    }

    @keyframes fadeleft {
      0% {
        opacity: 0;
        transform: translateX(5%);
      }

      100% {
        opacity: 1;
      }
    }

    #loan-details {
      width: 90%;
      /* height: 80%; */

      padding: 24px 24px 0 0;
      display: flex;
      flex-direction: column;

      /* box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px; */
    }

    .card-details {
      display: flex;
      flex-direction: column;
      justify-content: start;
      border-right: 2px solid rgb(128, 128, 128, 0.4);
      /* padding-right: 20px; */
      /* width: 80%; */
    }

    #card-header {

      padding: 8px;
      width: 60%;
      margin: 10px 0;
    }

    #card-header h3 {
      font-size: 16px;
      padding: 4px 0;
      color: #212775;
    }

    #card-header h5 {
      color: grey;
      font-weight: 300;
      font-size: 12px;
      padding: 4px 0;
    }

    #add-details {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 0;
    }

    #loan-details h6 {

      font-size: 12px;
      color: grey;
      font-weight: 700;
    }

    #loan-details h3 {
      padding: 8px;
      font-size: 16px;
      color: #000;
      /* font-weight: 800; */
    }

    #loan-details h4 {
      padding: 8px;
      font-size: 16px;
      color: #212775;
      /* font-weight: 800; */
    }

    #right-wrapper {
      display: flex;
      flex-direction: column;
      margin: 0 20px;
    }

    #detailedinsigts {
      /* display: flex;
      flex-direction: column; */

    }

    #detailedinsigts h3 {
      padding: 4px;
      font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    #insights {
      /* width: 70%; */
      /* margin: 24px; */
      /* padding: 12px; */
      display: flex;
      flex-direction: row;
      align-items: start;

    }

    #insights div {
      margin: 24px 30px;
    }

    #insights span {
      padding: 6px;
    }

    #insights h6 {
      padding: 8px 0 4px 0;
      font-size: 14px;
      color: rgb(0, 0, 0, 0.8);
      font-weight: 500;
      /* border-bottom: 1px solid grey; */
    }

    #insights h5 {
      margin: 3px 0;
      font-size: 18px;
      color: rgb(0, 0, 0);

      font-weight: 600;
    }

    #myChart {
      margin-top: 5%;
      max-width: 250px;
      max-height: 250px;
    }
    a{
      all: unset;
    }
  </style>


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
        const userResponse = await fetch('https://mocki.io/v1/1dbe8b9d-6775-43de-9ccd-777c4125750a');
        const userData = (await userResponse.json()).data;

        const loanResponse = await fetch('https://mocki.io/v1/1a4a79f6-e9b1-4e6c-b23e-1db04c88e5dd');
        const loanData = (await loanResponse.json()).data;

        const monthlyIncome = parseFloat(userData.salary);
        const monthlyExpenditure = monthlyIncome - parseFloat(userData.savings);
        const emi = parseFloat(loanData.emi);
        const loanAgeMonths = parseInt(loanData.months_elapsed);
        const totalRepayment = parseFloat(loanData.total_paid);
        const principalPaid = parseFloat(loanData.principal_paid);
        const expectedTotalRepayment = emi * loanAgeMonths;
        const eti = emi / monthlyIncome;
        const creditScore = parseInt(userData.credit_score);
        const missedEmiCount = parseInt(loanData.missed_emi);
        const emergencyFund = parseFloat(userData.extra_fund);
        const emergencyRatio = emergencyFund / monthlyExpenditure;
        console.log(totalRepayment)
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
        animateLinearProgressMultiple([100 - eti * 10, 100 - missedEmiCount * 100, (900 - creditScore) / 900 * 100], 2000);
        animateDonut(score * 10, 2000);

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