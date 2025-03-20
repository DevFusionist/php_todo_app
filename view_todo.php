<?php
    session_start();
    if (! $_SESSION['isLoggedIn']) {
        header("Location: login.php");
    }

    $fileName    = "todos.json";
    $messageText = "";
    $messageType = "";

    if (file_exists($fileName)) {
        $todos = json_decode(file_get_contents($fileName), true) ?? [];
    } else {
        $todos = [];
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (! empty($_POST['edit_item'])) {
            header("Location: dashboard.php?todo=" . urlencode($_POST['edit_item']));
            exit();
        } elseif ($_POST['delete_item']) {
            $delete_item = $_POST['delete_item'];
            $newTodos    = array_filter($todos, function ($todo) use ($delete_item) {
                return $todo['id'] !== intval($delete_item);
            });
            $todos       = array_values($newTodos);
            $messageText = "Todo deleted";
        } elseif ($_POST['mark_todo_as_done']) {
            foreach ($todos as &$todo) {
                if ($todo['id'] === intval($_POST['mark_todo_as_done'])) {
                    $todo['status'] = ($todo['status'] === "completed") ? "not completed" : "completed";
                    break;
                }
            }
            $messageText = ($todo['status'] === "not completed") ? "Todo has been marked as completed" : "Todo has been marked as not completed";

        }
        $messageType = "success";
        file_put_contents($fileName, json_encode($todos, JSON_PRETTY_PRINT));
        header("Location: " . $_SERVER['PHP_SELF']);
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
       <?php if (! empty($messageText) && ! empty($messageType)): ?>
          <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>" role="alert">
              <?php echo htmlspecialchars($messageText); ?>
          </div>
       <?php endif; ?>
       <div class='d-flex justify-content-evenly' style="margin-top:15px;">
         <h1 style="text-align:center;">Welcome to Dashboard</h1>
         <button type="submit" class="btn btn-primary" id='add_todo'>Add Todo</button>
        </div>
        <h5 class="card-title" style="text-align:center;margin-top:1rem;">Todo List</h5>
      <div class="row">
        <?php foreach ($todos as $todo): ?>
                <div key="<?php echo $todo["id"]; ?>" class="card col-md-4 mb-4" style="width: 30%; height:auto; margin:5% auto;">
                    <div class="card-body">
                        <h3>
                        <?php echo $todo['name']; ?>
                        </h3>
                        <?php if ($todo['status'] === "completed"): ?>
                            <p class='bg-success' style="border-radius:20px; padding:5px; text-align:center;">
                                🎉 Completed
                            </p>
                        <?php else: ?>
                            <p class='bg-danger' style="border-radius:20px; padding:5px; text-align:center;">
                                ❌ Not Completed
                            </p>
                        <?php endif; ?>

                        <h5>
                        <?php echo $todo['description']; ?>
                        </h5>
                        <div class="d-flex gap-2">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                <input type="hidden" value="<?php echo $todo['id'] ?>" name="edit_item"/>
                                <button class='btn btn-primary' id="edit_todo">Edit Todo</button>
                            </form>
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                <input type="hidden" value="<?php echo $todo['id'] ?>" name="delete_item"/>
                                <button class='btn btn-danger'>Delete Todo</button>
                            </form>
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                <input type="hidden" value="<?php echo $todo['id'] ?>" name="mark_todo_as_done"/>
                                <?php if ($todo['status'] === "not completed"): ?>
                                <button class='btn btn-success'>Mark todo as completed</button>
                                <?php elseif ($todo['status'] === "completed"): ?>
                                <button class='btn btn-warning'>Mark todo as incompleted</button>
                                <?php endif; ?>
                            </form>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
      </div>
   </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const btnRef = document.getElementById('add_todo');
            btnRef.addEventListener('click', function(){
                window.location.href = "dashboard.php";
            })
        })
    </script>
  </body>
</html>