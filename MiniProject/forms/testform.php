<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stepper Form - SHARC Style</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-color: #0D0D2B;
    }
    .glow {
      box-shadow: 0 0 20px rgba(165, 108, 255, 0.4);
    }
  </style>
</head>
<body class="text-white font-sans min-h-screen flex items-center justify-center px-4 py-10">

  <div class="bg-[#151527] rounded-2xl p-8 w-full max-w-xl glow shadow-lg">
    <h2 class="text-3xl font-bold text-center mb-6">Loan Optimization Steps</h2>

    <!-- Progress Bar -->
    <div class="relative w-full h-2 bg-[#33344A] rounded mb-8">
      <div id="progress" class="absolute h-2 bg-[#A56CFF] rounded transition-all duration-500" style="width: 33%;"></div>
    </div>

    <!-- Step 1: User Info -->
    <div class="step" id="step1">
      <input type="hidden" id="id" value="<?php echo $_SESSION['user_id']; ?>">

      <label class="block text-sm text-gray-400 mb-1">Monthly Salary (₹)</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="salary" required placeholder="Enter salary" />

      <label class="block text-sm text-gray-400 mb-1">Expenditure (₹)</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="expenditure" required placeholder="Enter expenditure" />

      <label class="block text-sm text-gray-400 mb-1">Credit Score</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="creditScore" required placeholder="Enter credit score" />

      <label class="block text-sm text-gray-400 mb-1">Do you have extra funds?</label>
      <input type="text" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="extraFund" placeholder="e.g., ₹10000 or 0" />

      <label class="block text-sm text-gray-400 mb-1">Savings after Expenditure</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="savings" />
    </div>

    <!-- Step 2: Loan Info -->
    <div class="step hidden" id="step2">
      <label class="block text-sm text-gray-400 mb-1">Principal Amount (₹)</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="principal" required placeholder="Enter loan amount" />

      <label class="block text-sm text-gray-400 mb-1">Interest Rate (% per annum)</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="rate" step="0.01" required placeholder="e.g., 7.5" />

      <label class="block text-sm text-gray-400 mb-1">Tenure (Years)</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="tenure" required placeholder="Enter tenure in years" />

      <label class="block text-sm text-gray-400 mb-1">Loan Start Date</label>
      <input type="date" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="startDate" />

      <label class="block text-sm text-gray-400 mb-1">Loan Availed Bank Name</label>
      <input type="text" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="bankName" />

      <label class="block text-sm text-gray-400 mb-1">EMI Missed Count</label>
      <input type="number" class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="missedEmi" />

      <label class="block text-sm text-gray-400 mb-1">Any repayments done already?</label>
      <select class="w-full bg-[#0D0D2B] text-white p-4 rounded-2xl border border-[#2A2A3B] mb-4 focus:outline-none focus:ring-2 focus:ring-[#A56CFF]" id="repayment">
        <option value="no">No</option>
        <option value="yes">Yes</option>
      </select>
    </div>

    <!-- Buttons -->
    <div class="flex justify-between mt-6">
      <button id="prevBtn" onclick="prevStep()" class="bg-[#2C2C3A] text-gray-300 px-5 py-2 rounded-full hover:bg-[#3a3a4d] hidden">Back</button>
      <button id="nextBtn" onclick="nextStep()" class="bg-[#A56CFF] text-white px-5 py-2 rounded-full hover:bg-[#925cff] ml-auto">Next</button>
    </div>
  </div>

  <script>
    let currentStep = 1;
    const totalSteps = 2;

    function showStep(step) {
      for (let i = 1; i <= totalSteps; i++) {
        document.getElementById(`step${i}`).classList.add("hidden");
      }
      document.getElementById(`step${step}`).classList.remove("hidden");

      // Progress bar
      const progress = document.getElementById("progress");
      progress.style.width = `${(step / totalSteps) * 100}%`;

      // Button visibility
      document.getElementById("prevBtn").style.display = step === 1 ? "none" : "inline-block";
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
        expenditure: document.getElementById('expenditure').value,
        creditScore: document.getElementById('creditScore').value,
        extraFund: document.getElementById('extraFund').value,
        principal: document.getElementById('principal').value,
        rate: document.getElementById('rate').value,
        tenure: document.getElementById('tenure').value,
        startDate: document.getElementById('startDate').value,
        bankName: document.getElementById('bankName').value,
        missedEmi: document.getElementById('missedEmi').value,
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
          window.location.href = "../dashboard.php";
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
  </script>

</body>


</html>
