<?php
session_start(); // Start the session at the top


if (!isset($_SESSION['user_id'])) {
  header('Location: ../login/auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Loan Stepper Form</title>


</head>
<body>

<div class="stepper-container">
  <div class="step-indicator" id="stepIndicator">
    <span class="active">1</span>
    <span>2</span>
  </div>

  <!-- Step 1: USERS -->
  <div class="step active">
    <h3>User Information</h3>
    
    <input type="hidden" id="id" value="<?php echo $_SESSION['user_id']; ?>">




    <label>Monthly Salary (₹):</label>
    <input type="number" id="salary" required>

    <label>Expenditure (₹):</label>
    <input type="number" id="expenditure" required>

    <label>Credit Score:</label>
    <input type="number" id="creditScore" required>

    <label>Do you have extra funds?</label>
    <input type="text" id="extraFund" placeholder="e.g., ₹10000 or 0">

    <label>Savings after Expenditure:</label>
    <input type="number" id="savings">
  </div>
  <!-- Step 2: LOAN -->
  <div class="step">
    <h3>Loan Details</h3>

    <label>Principal Amount (₹):</label>
    <input type="number" id="principal" required>

    <label>Interest Rate (% per annum):</label>
    <input type="number" id="rate" step="0.01" required>

    <label>Tenure (Years):</label>
    <input type="number" id="tenure" required>

    <label>Loan Start Date:</label>
    <input type="date" id="startDate">

    <label>EMI Missed Count:</label>
    <input type="number" id="missedEmi">

    <label>Any repayments done already?</label>
    <select id="repayment">
      <option value="yes">Yes</option>
      <option value="no">No</option>
    </select>

  </div>

  <!-- Navigation Buttons -->
  <div class="buttons">
    <button id="prevBtn" onclick="nextPrev(-1)">Back</button>
    <button id="nextBtn" onclick="nextPrev(1)">Next</button>
  </div>
</div>

<script>
  let currentStep = 0;
  const steps = document.querySelectorAll(".step");
  const indicators = document.querySelectorAll(".step-indicator span");

  function showStep(n) {
    steps.forEach((step, index) => {
      step.classList.remove("active");
      indicators[index].classList.remove("active");
      if (index === n) {
        step.classList.add("active");
        indicators[index].classList.add("active");
      }
    });

    document.getElementById("prevBtn").style.display = n === 0 ? "none" : "inline-block";
    document.getElementById("nextBtn").innerText = n === steps.length - 1 ? "Submit" : "Next";
  }

  function nextPrev(n) {
    if (n === 1 && !validateStep(currentStep)) return;

    currentStep += n;
    if (currentStep >= steps.length) {
      submitForm();
      return;
    }
    showStep(currentStep);
  }

  function validateStep(step) {
    const inputs = steps[step].querySelectorAll("input[required], select[required]");
    for (let input of inputs) {
      if (input.value.trim() === "") {
        alert("Please fill all required fields.");
        return false;
      }
    }
    return true;
  }

  function submitForm() {
    // First, get the session id
   
      
        const data = {
          id: document.getElementById('id').value,
          salary: document.getElementById('salary').value,
          expenditure: document.getElementById('expenditure').value,
          creditScore: document.getElementById('creditScore').value,
          extraFund: document.getElementById('extraFund').value,
          principal: document.getElementById('principal').value,
          rate: document.getElementById('rate').value,
          tenure: document.getElementById('tenure').value,
          startDate: document.getElementById('startDate').value,
          missedEmi: document.getElementById('missedEmi').value,
          repayment: document.getElementById('repayment').value,
          savings: document.getElementById('savings').value
        };
  
        // Log the data to the console
        console.log("Form Data: ", data);
  
        fetch('submit_form.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(responseData => {
          if (responseData.success) {
            alert("Form submitted successfully!");
            window.location.href = "../dashboard.php";

          } else {
            alert("Error: " + responseData.error);
          }
        })
        .catch(error => {
          console.error('Error:', error); // Log the error
          alert("An error occurred while submitting the form.");
        });
      }
  showStep(currentStep); // Initialize
</script>

</body>

<style>
  body {
    font-family: "Segoe UI", sans-serif;
    background: #f9f9f9;
    margin: 0;
    padding: 40px 20px;
  }

  .stepper-container {
    max-width: 650px;
    margin: auto;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 30px rgba(0,0,0,0.1);
  }

  .step-indicator {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 40px;
  }

  .step-indicator::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 10%;
    right: 10%;
    height: 4px;
    background: #ccc;
    z-index: 0;
    transform: translateY(-50%);
  }

  .step-indicator span {
    position: relative;
    background: #ddd;
    color: #555;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    z-index: 1;
    font-weight: bold;
    transition: background 0.3s ease;
  }

  .step-indicator span.active {
    background: #4CAF50;
    color: #fff;
  }

  .step {
    display: none;
  }

  .step.active {
    display: block;
  }

  label {
    display: block;
    margin-top: 20px;
    font-weight: 600;
  }

  input, select {
    width: 100%;
    padding: 10px 12px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
    transition: border-color 0.2s;
  }

  input:focus, select:focus {
    border-color: #4CAF50;
    outline: none;
  }

  .buttons {
    margin-top: 30px;
    display: flex;
    justify-content: space-between;
  }

  button {
    padding: 12px 25px;
    font-size: 16px;
    background-color: #4CAF50;
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  button:hover {
    background-color: #45a049;
  }

  button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  .stepper-container {
      transform: scale(0.95);
      opacity: 0;
      transition: transform 0.5s ease, opacity 0.5s ease;
    }
    
    body.loaded .stepper-container {
      transform: scale(1);
      opacity: 1;
    }
    
</style>

<script>
  window.addEventListener('load', () => {
      document.body.classList.add('loaded');
    });
    
</script>
</html>
