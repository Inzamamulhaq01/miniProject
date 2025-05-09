<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
  // Redirect to dashboard or home page if the user is already logged in
  header("Location: ../dashboard");  // Adjust this to your dashboard or homepage URL
  exit();
}


include '../conn.php';

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
  // Escape input
  $email = mysqli_real_escape_string($conn, $_POST['loginEmail']);
  $password = $_POST['loginPassword'];

  // Query to get user by email
  $sql = "SELECT id, name, password, newuser FROM users WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();

      if (password_verify($password, $row['password'])) {
          $_SESSION['user_id'] = $row['id'];
          $_SESSION['user_name'] = $row['name'];

          if ($row['newuser'] == 1) {
              header("Location: ../forms/"); // New user
          } else {
              header("Location: ../dashboard"); // Returning user
          }
          exit();
      } else {
          $message = "Invalid password.";
      }
  } else {
      $message = "No user found with that email.";
  }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login & Register</title>
</head>
<body>
  <div class="auth-container">
    <?php if (!empty($message)): ?>
      <div id="message" class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <div id="loginForm" style="<?= isset($_POST['register']) ? 'display: none;' : '' ?>">
      <form method="POST" action="">
        <h2>Login</h2>
        <label for="loginEmail">Email</label>
        <input type="email" id="loginEmail" name="loginEmail" required>
        
        <label for="loginPassword">Password</label>
        <input type="password" id="loginPassword" name="loginPassword" required>
        
        <button type="submit" name="login">Login</button> <!-- Using button instead of input -->
      </form>
      <div class="auth-toggle">
        <p>Don't have an account? <a href="javascript:void(0)" onclick="toggleAuthForms()">Register here</a></p>
      </div>
    </div>

    <!-- Register Form -->
    <div id="registerForm" style="display: none;">
      <form id="registerFormElement" onsubmit="registerUser(event)">
        <h2>Register</h2>
        <label for="registerName">Username</label>
        <input type="text" id="registerName" name="registerName" required>
        
        <label for="registerEmail">Email</label>
        <input type="email" id="registerEmail" name="registerEmail" required>
        
        <label for="registerPassword">Password</label>
        <input type="password" id="registerPassword" name="registerPassword" required>
        
        <button type="submit">Register</button> <!-- Using button instead of input -->
      </form>
      <div class="auth-toggle">
        <p>Already have an account? <a href="javascript:void(0)" onclick="toggleAuthForms()">Login here</a></p>
      </div>
    </div>
  </div>
</body>


<script>
    //  function toggleAuthForms() {
    //   const message = document.getElementById("message");
    //   console.log(message);
    //   const registerForm = document.getElementById("registerForm");
    //   const loginForm = document.getElementById("loginForm");
    //   if (registerForm.style.display === "none") {
    //     registerForm.style.display = "block";
    //     loginForm.style.display = "none";
    //   } else {
    //     registerForm.style.display = "none";
    //     loginForm.style.display = "block";
    //   }
    //   if(message)
    //   message.value = "";
    // }

            function toggleAuthForms() 
            {
                const message = document.getElementById("message");
                const registerForm = document.getElementById("registerForm");
                const loginForm = document.getElementById("loginForm");

                // Clear all input fields in the forms
                const registerInputs = registerForm.querySelectorAll('input');
                const loginInputs = loginForm.querySelectorAll('input');

                // Reset the values of all inputs
                registerInputs.forEach(input => input.value = "");
                loginInputs.forEach(input => input.value = "");

                // Hide/show forms
                if (registerForm.style.display === "none") {
                  registerForm.style.display = "block";
                  loginForm.style.display = "none";
                } else {
                  registerForm.style.display = "none";
                  loginForm.style.display = "block";
                }

                // Clear the message field if it exists
                if (message) {
                  message.textContent = ""; // Make sure to clear the text content instead of value
                }
            }



    async function registerUser(event) {
      event.preventDefault(); // Prevent form submission

      const name = document.getElementById("registerName").value;
      const email = document.getElementById("registerEmail").value;
      const password = document.getElementById("registerPassword").value;

      const formData = {
        name: name,
        email: email,
        password: password
      };

      // Send data to PHP using fetch
      const response = await fetch("register.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
      });

      const result = await response.json();
      
      if (result.success) {
        alert("Registration successful!");
        toggleAuthForms(); // Switch to Login form after successful registration
      } else {
        alert(result.error);
      }
    }

  </script>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background: #f4f4f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .auth-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    .auth-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .auth-container label {
      display: block;
      margin-top: 10px;
      font-weight: 600;
    }
    .auth-container input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }
    .auth-container input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 15px;
    }
    .auth-container input[type="submit"]:hover {
      background-color: #45a049;
    }
    .auth-toggle {
      text-align: center;
      margin-top: 20px;
    }
    .auth-toggle a {
      color: #4CAF50;
      text-decoration: none;
    }
    .auth-toggle a:hover {
      text-decoration: underline;
    }
    .message {
      text-align: center;
      color: red;
      font-weight: bold;
    }
    .auth-container button {
    width: 100%;
    padding: 10px;
    margin-top: 15px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
    border-radius: 8px;
    box-sizing: border-box;
  }

.auth-container button:hover {
  background-color: #45a049;
}

  </style>
</html>
