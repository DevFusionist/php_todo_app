<?php
    session_start();
    if (empty($_SESSION['email']) && empty($_SESSION['password'])) {
        header("Location: signup.php");
        exit;
    } elseif ($_SESSION['isLoggedIn']) {
        header("Location: dashboard.php");
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $email       = htmlspecialchars(trim($_POST['email']));
        $password    = htmlspecialchars(trim($_POST['password']));
        $messageText = "";
        $messageType = "";

        if (! empty($email) && ! empty($password)) {
            $storedPassword = $_SESSION['password'];
            if (password_verify($password, $storedPassword)) {
                $_SESSION['isLoggedIn'] = true;
                $messageText            = "Login successfull";
                $messageType            = "success";
                header("Location: dashboard.php");
                exit;
            } else {
                $messageText = "email or password is invalid!";
                $messageType = "danger";
            }
        } else {
            $messageText = "Email and Password fields are required";
            $messageType = "danger";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Todo App</title>
    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
    />
  </head>
  <body>
    <div class="container">
     <?php if (isset($email) && isset($password) && ! empty($messageText) && ! empty($messageType)): ?>
          <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>" role="alert">
              <?php echo htmlspecialchars($messageText); ?>
          </div>
      <?php endif; ?>
     <div class="card" style="width: 50%; margin:10% auto;">
       <img src="https://imgs.search.brave.com/mBIad3ZX2AbcTt7nW-h932b3G3m-Gmz_ZeHbVuxE8rc/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9zdG9y/aWVzLmZyZWVwaWts/YWJzLmNvbS9zdG9y/YWdlLzI4NTEwL0xv/Z2luLVBBTkEtMDEu/c3Zn" class="card-img-top h-25" alt="login">
       <div class="card-title" style="margin:auto;">
        <h1>
          Login
        </h1>
       </div>
       <div class="card-body">
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Email address</label>
        <input
        type="email"
        class="form-control"
        id="exampleInputEmail1"
        aria-describedby="emailHelp"
        name="email"
        />
        <div id="emailHelp" class="form-text">
          We'll never share your email with anyone else.
        </div>
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input
        type="password"
        class="form-control"
        id="exampleInputPassword1"
            name="password"
            />
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1"
            onchange="handleChange()"
            />
            <label class="form-check-label" for="exampleCheck1">Check me out</label>
          </div>
          <button type="submit" class="btn btn-primary" disabled>Submit</button>
        </form>
      </div>
    </div>

   </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script>
      const buttonRef = document.getElementsByClassName("btn");
      const handleChange = ()=>{
       const inputElement = document.getElementById("exampleCheck1");
       const isChecked = inputElement.checked;
       if(!isChecked){
          buttonRef[0].disabled = true;
        }else{
          buttonRef[0].disabled = false;
        }
     }
    </script>
  </body>
</html>
