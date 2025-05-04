<?php
session_start();  // Start the session

// Assuming the user is logged in and the user_id is set in session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Handle case where user_id is not set (e.g., not logged in)
    $user_id = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Smart Financial Chatbot</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <h1>Smart Financial Chatbot</h1>

  <div class="chatbox">
    <div id="chatlog"></div>
    <div class="input-container">
      <input type="text" id="userInput" placeholder="Type your message..." />
      <button onclick="sendMessage()">Send</button>
    </div>
  </div>

  <script>
    


    const chatlog = document.getElementById("chatlog");
    const userInput = document.getElementById("userInput");

    let step = 0;
    let responses = {};

    let userResponses = {
      prepaymentInterest: null,
      prepaymentAmount: null,
      stepUpInterest: null,
      stepUpPercent: null,
      prepaymentFrequency: null,
      lumpSumInterest: null,
      lumpSumAmount: null,
      lumpSumMonth:null,
    };

          const questions = [
        "Are you interested in prepayment?",
        "How much would you like to prepay? (in ₹)",
        "Would you like to step up your prepayment amount?",
        "By what percentage would you like to increase it?",
        "Which duration is comfortable for your prepayment?",
        "Would you like to make a lump sum prepayment?",
        "How much would the lump sum be? (in ₹)",
        "In which year and which month did you pay lump sum amount?" // new question (index 7)
      ];


    window.onload = () => {
      appendMessage("bot", "Hi! Let's plan your loan prepayment strategy.", () => {
        askNext();
      });
      userInput.focus();
    };

    function getResponses() {
      return responses; // Return the stored responses
    }
    function appendMessage(sender, text, callback = null, options = null) {
      const msg = document.createElement("div");
      msg.className = `message ${sender}`;
      chatlog.appendChild(msg);
      chatlog.scrollTop = chatlog.scrollHeight;

      if (sender === "bot") {
        let i = 0;
        let formattedText = "";
        const interval = setInterval(() => {
          formattedText += text.charAt(i);
          msg.innerText = formattedText;
          i++;
          chatlog.scrollTop = chatlog.scrollHeight;
          if (i >= text.length) {
            clearInterval(interval);

            if (options === "yesno" || Array.isArray(options)) {
              const btnContainer = document.createElement("div");
              btnContainer.className = "option-buttons";
              
              const labels = options === "yesno" ? ["Yes", "No"] : options;
            
              labels.forEach(label => {
                const btn = document.createElement("button");
                btn.textContent = label;
                btn.onclick = () => {
                  userInput.value = label.toLowerCase();
                  sendMessage();
                  btnContainer.remove();
                };
                btnContainer.appendChild(btn);
              });
            
              msg.appendChild(document.createElement("br"));
              msg.appendChild(btnContainer);
            }
            

            if (callback) callback();
          }
        }, 40);
      } else {
        msg.textContent = text;
        chatlog.scrollTop = chatlog.scrollHeight;
        if (callback) callback();
      }
    }

    function sendMessage() {
      const input = userInput.value.trim();
      if (!input) return;

      appendMessage("user", input);
      processInput(input.toLowerCase());
      userInput.value = "";
    }

    function askNext() {
      const needsYesNo = [0, 2, 5].includes(step);
      let options = null;
      if ([0, 2, 5].includes(step)) options = "yesno";
      else if (step === 4) options = ["Monthly", "Quarterly", "Yearly"];

      appendMessage("bot", questions[step], null, options);

    }

    function processInput(input) {
      switch (step) {
        case 0:
          if (input === "yes" || input === "no") {
            userResponses.prepaymentInterest = input;
            step = (input === "no") ? 5 : 1;
            askNext();
          } else {
            appendMessage("bot", "Please answer Yes or No.");
          }
          break;
    
        case 1:
          if (!isNaN(parseInt(input))) {
            userResponses.prepaymentAmount = input;
            step = 2;
            askNext();
          } else {
            appendMessage("bot", "Enter a valid amount in rupees.");
          }
          break;
    
        case 2:
          if (input === "yes" || input === "no") {
            userResponses.stepUpInterest = input;
            step = input === "yes" ? 3 : 4;
            if (input === "no") userResponses.stepUpPercent = "0";
            askNext();
          } else {
            appendMessage("bot", "Please answer Yes or No.");
          }
          break;
    
        case 3:
          if (!isNaN(parseFloat(input))) {
            userResponses.stepUpPercent = input;
            step = 4;
            askNext();
          } else {
            appendMessage("bot", "Enter a valid percentage.");
          }
          break;
    
        case 4:
          if (["monthly", "quarterly", "yearly"].includes(input)) {
            userResponses.prepaymentFrequency = input;
            step = 5;
            askNext();
          } else {
            appendMessage("bot", "Choose monthly, quarterly, or yearly.");
          }
          break;
    
          case 5:
          if (input === "yes" || input === "no") {
            userResponses.lumpSumInterest = input;
            if (input === "no") {
              userResponses.lumpSumAmount = "0";
              userResponses.lumpSumMonth = "1";
              showSummary();
            } else {
              step = 6;
              askNext();
            }
          } else {
            appendMessage("bot", "Please answer Yes or No.");
          }
          break;

    
          case 6:
            if (!isNaN(parseInt(input))) {
              userResponses.lumpSumAmount = input;
              step = 7;
              askNext();
            } else {
              appendMessage("bot", "Enter a valid amount in rupees.");
            }
            break;

          case 7:
            if (!isNaN(parseInt(input))) {
              userResponses.lumpSumMonth = input;
              showSummary();
            } else {
              appendMessage("bot", "Enter a valid year (e.g., 2025).");
            }
            break;

      }
    }
    
    // Function to show the summary without including the data in the final summary
    async function showSummary() {
        let strategy = "Unable to determine strategy. Please review inputs.";

        // Determine strategy
        if (userResponses.prepaymentInterest === "no" && userResponses.lumpSumInterest === "no") {
          strategy = "Standard Plan – No Prepayment";
        } else if (userResponses.prepaymentInterest === "yes" && userResponses.stepUpInterest === "no" && userResponses.lumpSumInterest === "no") {
          strategy = "Fixed Extra EMI Plan";
        } else if (userResponses.prepaymentInterest === "yes" && userResponses.stepUpInterest === "yes" && parseFloat(userResponses.stepUpPercent) > 0 && userResponses.lumpSumInterest === "no") {
          strategy = "Step-Up Prepayment Plan";
        } else if (userResponses.prepaymentInterest === "no" && userResponses.lumpSumInterest === "yes") {
          strategy = "Lump Sum at Flexible Intervals";
        } else if (userResponses.prepaymentInterest === "yes" && userResponses.stepUpInterest === "no" && userResponses.lumpSumInterest === "yes") {
          strategy = "Fixed EMI + Lump Sum";
        } else if (userResponses.prepaymentInterest === "yes" && userResponses.stepUpInterest === "yes" && userResponses.lumpSumInterest === "yes") {
          strategy = "Step-Up EMI + Lump Sum";
        }

        // Parse dynamic inputs from userResponses (or define defaults)
        const stepUpPercent = parseFloat(userResponses.stepUpPercent || 0);
        const prepaymentAmount = parseFloat(userResponses.prepaymentAmount || 0);
        const prepaymentFrequency = userResponses.prepaymentFrequency || "quarterly";
        const lumpSumAmount = parseFloat(userResponses.lumpSumAmount || 0);
        const lumpSumMonth = parseInt(userResponses.lumpSumMonth || 12);

        const userId = <?php echo json_encode($user_id); ?>;

        // Fetch loan data
        const loanDataResponse = await fetchLoanData(userId);
        if (!loanDataResponse || !loanDataResponse.success) {
          alert("Unable to fetch loan data.");
          return;
        }

        const data = loanDataResponse.data;

        const startDate = data.start_date; // keep it as string
        const dateParts = startDate.split("-"); // works if format is "YYYY-MM-DD"

        const year = parseInt(dateParts[0]);
        const month = parseInt(dateParts[1]);

        // console.log("Year:", year);
        // console.log("Month:", month);

        // Build URL
        const params = new URLSearchParams();
        params.set('loanAmount', parseFloat(data.principal));
        params.set('outstandingBalance', parseFloat(data.outstanding_balance));
        params.set('interestRate', parseFloat(data.rate));
        params.set('loanTenure', parseInt(data.tenure));
        params.set('startDate', data.start_date);
        params.set('startMonth', month);
        params.set('startYear', year);
        params.set('stepUpRate', stepUpPercent);
        params.set('monthlyPrepayment', prepaymentAmount);
        params.set('prepaymentFrequency', prepaymentFrequency);
        params.set('lumpSumPrepayment', lumpSumAmount);
        params.set('lumpSumMonth', lumpSumMonth);

        const chartUrl = `graph.html?${params.toString()}`;

        // Append summary message and link
        appendMessage("bot", `✅ Summary of your responses:\n💡 Recommended Strategy:\n${strategy}`, () => {
          const detailsLink = document.createElement("div");
          detailsLink.innerHTML = `<a href="${chartUrl}" target="_blank" style="color:#00f5ff; text-decoration: underline;">Analyze Your Loan</a>`;
          chatlog.appendChild(detailsLink);
          chatlog.scrollTop = chatlog.scrollHeight;
        });
      }

    
    // Function to access the stored user responses globally
    function getUserResponses() {
      return userResponses; // You can access this object anywhere in your app
    }
    


    async function fetchLoanData(userId) {
      try {
        const response = await fetch(`../apis/loans.php?user_id=${userId}`);
        const data = await response.json(); // Parse the JSON response
        return data; // Return the data from the API
      } catch (error) {
        console.error('Error fetching loan data:', error);
        return null;
      }
    }


    const userId = <?php echo json_encode($user_id); ?>;
  

    async function logLoanData(userId) {
        const loanData = await fetchLoanData(userId);
        console.log(loanData); // logs the actual fetched loan data
      }
      // logLoanData(userId);
  userInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") sendMessage();
    });

    
  </script>

</body>
</html>
