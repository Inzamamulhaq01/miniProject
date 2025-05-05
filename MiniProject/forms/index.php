<?php
session_start(); // Start the session at the top


if (!isset($_SESSION['user_id'])) {
  header('Location: ../login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="stepperForm.css">
</head>

<body>

  <div class="container">
  <div id="loader" class="loader hidden"></div>

        <h2>Loan Optimization Steps</h2>

        <!-- Progress Bar -->
        <div class="progress-bar">
          <div id="progress" class="progress"></div>
        </div>

        <!-- Step 1: User Info -->
        <div class="step active" id="step1">
          <input type="hidden" id="id" value="<?php echo $_SESSION['user_id']; ?>">
        
          <div class="row">
            <div class="half">
              <label>Monthly Salary (₹)</label>
              <input type="number" id="salary" required placeholder="Enter salary" />
            </div>
            <div class="half">
              <label>Savings after Expenditure</label>
              <input type="number" id="savings" />
            </div>
          </div>
        
          <div class="row">
            <div class="half">
              <label>Credit Score</label>
              <input type="number" id="creditScore" required placeholder="Enter credit score" />
            </div>
            <div class="half">
              <label>Do you have extra funds how much?</label>
              <input type="number" id="extraFund" placeholder="e.g., ₹10000 or 0" />
            </div>
          </div>
        
          <!-- <div class="row">
            <div class="half">
              <label>Savings after Expenditure</label>
              <input type="number" id="savings" />
            </div>
          </div> -->
        </div>
    

          <!-- Step 2: Loan Info -->
          <div class="step" id="step2">
            <div class="row">
              <div class="half">
                <label>Principal Amount (₹)</label>
                <input type="number" id="principal" required placeholder="Enter loan amount" />
              </div>
              <div class="half">
                <label>Interest Rate (% per annum)</label>
                <input type="number" id="rate" step="0.01" required placeholder="e.g., 7.5" />
              </div>
            </div>
          
            <div class="row">
              <div class="half">
                <label>Tenure (Years)</label>
                <input type="number" id="tenure" required placeholder="Enter tenure in years" />
              </div>
              <div class="half">
                <label>Loan Start Date</label>
                <input type="date" id="startDate" />
              </div>
            </div>
          
            <div class="row">
              <div class="half">
                <label>Loan Availed Bank Name</label>
                <input type="text" id="bankName" />
              </div>
              <div class="half">
                <label>Any repayments done already?</label>
                <select id="repayment">
                  <option value="no">No</option>
                  <option value="yes">Yes</option>
                </select>
              </div>
            </div>
          
            <!-- <div class="row">
            <div class="half">
                <label>EMI Missed Count</label>
                <input type="number" id="missedEmi" />
              </div>
            </div> -->
 
    </div>
    

    <!-- Buttons -->
      <!-- Buttons -->
        <div class="buttons">
        <button id="prevBtn" onclick="prevStep()" class="btn btn-back hidden">Back</button>
        <button id="nextBtn" onclick="nextStep()" class="btn">Next</button>
        </div>

  </div>

  <script>
    let currentStep = 1;
    const totalSteps = 2;

    function showStep(step) {
      for (let i = 1; i <= totalSteps; i++) {
        document.getElementById(`step${i}`).classList.remove("active");
      }
      document.getElementById(`step${step}`).classList.add("active");

      // Progress bar
      const progress = document.getElementById("progress");
      progress.style.width = `${(step / totalSteps) * 100}%`;

      // Button visibility
      document.getElementById("prevBtn").classList.toggle("hidden", step === 1);
      document.getElementById("nextBtn").innerText = step === totalSteps ? "Finish" : "Next";
    }

    function nextStep() {
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
      } else {
        submitForm();
      }
    }

    function prevStep() {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    }

    function submitForm() {
      const data = {
        id: document.getElementById('id').value,
        salary: document.getElementById('salary').value,
        // expenditure: document.getElementById('expenditure').value,
        creditScore: document.getElementById('creditScore').value,
        extraFund: document.getElementById('extraFund').value,
        principal: document.getElementById('principal').value,
        rate: document.getElementById('rate').value,
        tenure: document.getElementById('tenure').value,
        startDate: document.getElementById('startDate').value,
        bankName: document.getElementById('bankName').value,
       // missedEmi: document.getElementById('missedEmi').value,
        repayment: document.getElementById('repayment').value,
        savings: document.getElementById('savings').value
      };

  

      fetch('submit_form.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      })
      .then(response => response.json())
      .then(responseData => {
        if (responseData.success) {
          alert("Form submitted successfully!");
          window.location.href = "../dashboard";
        } else {
          alert("Error: " + responseData.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while submitting the form.");
      });
    }

    showStep(currentStep);


    function showLoader() {
        document.getElementById("loader").classList.remove("hidden");
      }

      function hideLoader() {
        document.getElementById("loader").classList.add("hidden");
      }

      // Example: Show loader when form is submitted
      document.querySelector("form").addEventListener("submit", function (e) {
        showLoader(); // Show the spinner
      });



  </script>




</body>


</html>
