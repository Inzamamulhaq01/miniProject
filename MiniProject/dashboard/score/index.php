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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Credit Report Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">
  <!-- You can use Font Awesome or similar for icons -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>

  <div class="main">
    <div class="left-panel">
      <div class="credit-score">

        <h1>loan Health Score</h1>
        <span>
          <h6>May 04,2025</h6>
          <i class="fa fa-download"></i>
        </span>
      </div>
      <div class="wrapper">
        <!-- <div class="donut-chart"></div>
                  -->
        <div id="chart-wrapper">
          <canvas id="gauge" width="450" height="400"></canvas>
          <div class="circle-overlay">
            <h2 id="score">0</h2>
            <h4 id="score-desc">Low</h4>
          </div>
        </div>
        <div class="tips">



          <h3> <i class="fa fa-lightbulb-o "></i> Tips</h3>
          <br>
          <hr>
          <br>
          <img src="images/tips.avif" alt="">
          <div>
            <p> Check your loan Health score at least a month <br> to aware about <br> your loan <br> optimization.</p>

            <br><br>
            <h5>See How?</h5>

          </div>
        </div>
      </div>
   

      <h4>Score Factors <i class="fa fa-info-circle" style="color: grey;"></i></h4>
  
      <div class="credit-factors">
        <div class="factor">
          <strong id="eti-text">24%</strong><br>
          <p> Income to EMI ratio</p>
        </div>
        <div class="factor">
          <strong id="creditscore-text">800</strong><br>
          <p>your Credit Score</p>
        </div>
        <div class="factor">
          <strong id="prepayment-text">6Y 7M</strong><br>
          <p>No of late emis</p>
        </div>
      </div>
    </div>
    <div class="right-panel">
      <h3>Detailed summary</h3>
      <br>
      <div id="insight-wrapper">
        <h4 class="shimmer-text">Generating</h4>
        <small id="insight-logger">analyzing credit score</small>
      </div>
      <div id="insightList">
      </div>
      <div id="goto">
        <p><i class="fa fa-info-circle" style="color: grey;"></i> To get more insight and  optimization techiniques  </p>
        <button ><a style="text-decoration: none;color:white" href="../../chatbot/"">Get Started </a></button>
      </div>
    </div>
  </div>

</body>
<script>
  let currentValue = 0;  // Start from 0
  let targetValue = 0;
  const duration = 1500; 
  const listElem = document.getElementById("insightList");
  
  
     async function fetchData() {
    try {
      const userData = await fetchUserData();
      console.log(userData);
      const loanData = await fetchLoanData();
      const metrics = calculateFinancialMetrics(userData, loanData);
  
      updateUIText(metrics);
      const insights = generateInsights(metrics);
      renderInsights(insights);
      // console.log(metrics.score)
  
      targetValue=metrics.score;
  setGaugeValue(targetValue);
      // document.querySelector("#score").textContent=targetValue;
      document.querySelector("#score-desc").textContent=getscoredesc(targetValue);
      document.querySelector("#score-desc").style.color=getscoredescolor();
  
      // renderGaugeChart(metrics.score);
      drawGauge();
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  }
  function getscoredesc(){
    if(targetValue>8) return "Good"
    if(targetValue>6) return "medium"
    if(targetValue>4) return "Low"
    return "Bad";
  }
  function getscoredescolor(){
    if(targetValue>8) return "Green"
    if(targetValue>6) return "blue"
    if(targetValue>4) return "Purple"
    return "Red";
  }
  const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
  // Fetch user data from API
  async function fetchUserData() {
    const res = await fetch(`../../apis/users.php?user_id=${userId}`);
    return (await res.json()).data;
  }
  
  // Fetch loan data from API
  async function fetchLoanData() {
    const res = await fetch(`../../apis/loans.php?user_id=${userId}`);
    return (await res.json()).data;
  }
  
  // Calculate metrics from the raw data
  function calculateFinancialMetrics(user, loan) {
    const income = parseFloat(user.salary);
    const savings = parseFloat(user.savings);
    const expenditure = income - savings;
    const emi = parseFloat(loan.emi);
    const tenure = parseInt(loan.tenure);
    const monthsElapsed = parseInt(loan.months_elapsed);
    const totalPaid = parseFloat(loan.total_paid);
    const principalPaid = parseFloat(loan.principal_paid);
    const principal = parseFloat(loan.principal);
    const creditScore = parseInt(user.credit_score);
    const missedEmi = parseInt(loan.missed_emi);
    const emergencyFund = parseFloat(user.extra_fund);
    const eti = emi / income;
    const emergencyRatio = emergencyFund / expenditure;
    const prepayment = 100000; // set default
  
    let score = 0;
  
    return {
      income,
      savings,
      expenditure,
      emi,
      tenure,
      monthsElapsed,
      totalPaid,
      principalPaid,
      principal,
      creditScore,
      missedEmi,
      emergencyFund,
      emergencyRatio,
      eti,
      prepayment,
      score
    };
  }
  
  // Update UI text elements
  function updateUIText(metrics) {
    document.getElementById("eti-text").textContent = Math.round(metrics.eti * 100) + " %";
    document.getElementById("creditscore-text").textContent = metrics.creditScore;
    document.getElementById("prepayment-text").textContent = metrics.missedEmi;
  }
  
  // Generate loan insights and calculate score
  function generateInsights(m) {
    const insights = [];
    let score = 0;
  
    // Credit Score
    if (m.creditScore >= 750) {
      score += 2; insights.push(`Credit Score is excellent (${m.creditScore}).`);
    } else if (m.creditScore >= 700) {
      score += 1; insights.push(`Credit Score is good (${m.creditScore}).`);
    } else if (m.creditScore >= 600) {
      insights.push(`Credit Score is fair (${m.creditScore}). May limit refinancing options.`);
    } else {
      score -= 2; insights.push("Credit Score is extremely low (" + m.creditScore + "). This negatively impacts your loan profile.");
    }
  
    // EMI-to-Income
    if (m.eti === 0) {
      score += 2; insights.push(`No EMIs currently, EMI-to-Income ratio healthy (0%).`);
    } else if (m.eti < 0.4) {
      score += 2; insights.push(`EMI-to-Income ratio is healthy (${(m.eti * 100).toFixed(2)}%).`);
    } else if (m.eti < 0.5) {
      score += 1; insights.push(`EMI-to-Income ratio is moderate (${(m.eti * 100).toFixed(2)}%).`);
    } else {
      score -= 1; insights.push(`High EMI-to-Income ratio (${(m.eti * 100).toFixed(2)}%).. May affect savings`);
    }
  
    // Missed EMIs
    if (m.missedEmi === 0) {
      score += 2; insights.push("You've never missed an EMI.I, which helps your reliability.");
    } else if (m.missedEmi === 1) {
      score += 1; insights.push("You missed one EMI. Try to avoid this in future");
    } else {
      score -= 2; insights.push(`Multiple missed EMIs (${m.missedEmi}),is a red flag for lenders.`);
    }
  
    // Prepayment
    if (m.prepayment > m.emi * 5) {
      score += 2; insights.push("You're actively reducing your loan burden via prepayment.");
    } else {
      insights.push("No prepayments made, so you're not actively reducing the loan burden.");
    }
  
    // Additional Conditions
    if (score >= 5 && m.creditScore > 700 && m.missedEmi <= 1)
    insights.push("💡 You may qualify for refinancing at lower interest — check with other banks.");
    if (m.prepayment > 0 && score >= 7)
    insights.push("🎯 You're on track to close your loan early. Maintain prepayments for faster closure.");
    if (m.income - m.emi > m.emi)
    insights.push("✅ You have surplus income — consider saving for emergencies or additional prepayments.");
    if (m.tenure > 20)
    insights.push("⏳ Your loan tenure is quite long — consider increasing EMI or prepayments to reduce interest burden.");
    if (m.creditScore < 650)
    insights.push("⚠️ Your credit score is below optimal. Improve it by paying EMIs on time and reducing debts.");
    if (m.prepayment === 0 && score >= 6)
    insights.push("📌 You have a stable profile — consider starting prepayments to reduce total interest.");
    if (m.missedEmi >= 1 && m.prepayment > 0)
    insights.push("🧩 Good recovery! You've missed some EMIs but have made efforts to catch up with prepayments.");
    if (m.creditScore > 720 && m.emi > 15000 && m.missedEmi === 0)
    insights.push("💱 You might be eligible for a balance transfer with better interest rates — compare offers.");
    if (m.totalPaid >= m.principal / 2)
    insights.push("🟢 You've crossed 50% repayment — maintain pace for early closure or refinancing advantage.")
    if (m.income - m.savings < m.emi * 0.5)
    insights.push("💸 Your emergency buffer seems tight — build a reserve to avoid risk during financial stress.");
    if (m.prepayment > m.totalPaid * 0.75)
    insights.push("📉 You've prepaid a large portion — ensure liquidity isn't compromised in emergencies.");
  
    m.score = score;
    return insights;
  }
  
  // Animate and show the insights list
  async function renderInsights(insights) {
    const logger = document.getElementById("insight-logger");
    const loghead = document.querySelector("#insight-wrapper > h4");
    const logs = ["Analyzed credit score", "Analyzed ETI ratio", "Analyzed late emis", "Analyzed other parameters"];
  
  
    for (const [index, text] of insights.entries()) {
      await new Promise(resolve => setTimeout(resolve, 300 * index));
    
      const li = document.createElement("li");
      li.textContent = text;
    
  
    
      li.style.animationDelay = `${index * 0.1}s`;
      listElem.appendChild(li);
      if (index < 4)  logger.textContent = logs[index];
    }
    
    loghead.textContent = ""; // or remove/replace as needed
  }
  
  // Draw the score gauge using Chart.js
  
  function drawGauge(value) {
       
    const canvas = document.getElementById('gauge');
        const ctx = canvas.getContext('2d');
          const centerX = canvas.width / 2;
          const centerY = canvas.height / 1.5;
          const radius = canvas.width / 2 - 10;
          const startAngle = Math.PI;
          const totalAngle = Math.PI;
          const segmentCount = 4;
          const gap = 0.01;
  
          // Clear canvas
          ctx.clearRect(0, 0, canvas.width, canvas.height);
  
          // Draw 4 gradient segments with gaps
          for (let i = 0; i < segmentCount; i++) {
            const segmentStart =
              startAngle + (i * totalAngle) / segmentCount + gap / 2;
            const segmentEnd =
              startAngle + ((i + 1) * totalAngle) / segmentCount - gap / 2;
  
            const gradient = ctx.createLinearGradient(
              centerX + radius * Math.cos(segmentStart),
              centerY + radius * Math.sin(segmentStart),
              centerX + radius * Math.cos(segmentEnd),
              centerY + radius * Math.sin(segmentEnd)
            );
  
            if (i === 0)
              gradient.addColorStop(0, 'red'), gradient.addColorStop(1, 'orange');
            if (i === 1)
              gradient.addColorStop(0, 'purple'), gradient.addColorStop(1, 'violet');
            if (i === 2)
              gradient.addColorStop(0, 'blue'), gradient.addColorStop(1, 'skyblue');
            if (i === 3)
              gradient.addColorStop(0, 'green'), gradient.addColorStop(1, 'lime');
  
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius - 20, segmentStart, segmentEnd);
            ctx.lineWidth = 10;
            ctx.strokeStyle = gradient;
            ctx.stroke();
          }
  
          // Calculate needle angle
          const valueAngle = (value / 10) * totalAngle + startAngle;
  
  
          
  
          const needleLength = radius - 60; // Length of the needle
          const angle = (value / 10) * Math.PI + Math.PI; // Needle angle based on value
  
          // Create an equilateral triangle sitting on the center circle (smaller triangle)
          const triangleSize = 80; // Size of the small equilateral triangle
  
          const needleX1 =
            centerX + Math.cos(angle - Math.PI / 2) * triangleSize; // Left side of the triangle
          const needleY1 =
            centerY + Math.sin(angle - Math.PI / 3) * triangleSize;
  
          const needleX2 =
            centerX + Math.cos(angle + Math.PI) * triangleSize; // Right side of the triangle
          const needleY2 =
            centerY + Math.sin(angle + Math.PI / 2) * triangleSize;
  
          const needleX3 =
            centerX + Math.cos(angle) * needleLength; // Tip of the triangle (needle point)
          const needleY3 =
            centerY + Math.sin(angle) * needleLength;
  
          // Create an equilateral triangle shape for the needle
          ctx.beginPath();
          ctx.moveTo(needleX1, needleY1);
          ctx.lineTo(needleX2, needleY2);
          ctx.lineTo(needleX3, needleY3);
          ctx.closePath();
  
          // Fill the triangle with a needle color
          ctx.fillStyle = '#333'; // Dark color for the needle
          ctx.fill();
  
  
  
        }
    
        
  
        function setGaugeValue(value) {
    targetValue = Math.max(0, Math.min(10, value)); // clamp between 0–10
    const startValue = currentValue;
    const startTime = performance.now();
    const scoreElement = document.getElementById('score');
    function animate() {
      const elapsedTime = performance.now() - startTime;
      const progress = Math.min(elapsedTime / duration, 1);
      currentValue = startValue + (targetValue - startValue) * progress;
      scoreElement.textContent = `${Math.round(currentValue)}`;
      drawGauge(currentValue);
      if (progress < 1) {
        requestAnimationFrame(animate);
      }
    }
  
    animate();
  }
  const today = new Date();
  const options = { year: 'numeric', month: 'long', day: '2-digit' };
  const formattedToday = today.toLocaleDateString('en-US', options);
  document.querySelector(".credit-score span h6").innerHTML=formattedToday;
      fetchData();
  </script>
</html>