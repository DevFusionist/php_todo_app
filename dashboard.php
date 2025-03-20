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

    $todo_item_to_be_edited_index = $_GET['todo'];
    $todo_item_to_be_edited       = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_name']) && isset($_POST['description'])) {
        if (empty(trim(htmlspecialchars($_POST['todo_name']))) || empty(trim(htmlspecialchars($_POST['description'])))) {

            $messageText = "Name and Description fields are required";
            $messageType = "danger";
        } else {

            $todo_name   = htmlspecialchars(trim(($_POST['todo_name'])));
            $description = htmlspecialchars(trim(($_POST['description'])));

            if (! $_GET['todo']) {
                $id      = count($todos) > 0 ? count($todos) + 1 : 1;
                $todos[] = [
                    "id"          => $id,
                    "name"        => $todo_name,
                    "description" => $description,
                    "status"      => "not completed",
                ];
                $messageText = "Todo has been created and saved successfully";
            } else {
                foreach ($todos as &$todo) {
                    if ($todo['id'] === intval($_GET['todo'])) {
                        $todo_item_to_be_edited = $todo;
                        $todo['name']           = $todo_name;
                        $todo['description']    = $description;
                        break;
                    }
                }
                $messageText = "Todo has been edited and saved successfully";
            }
            file_put_contents($fileName, json_encode($todos, JSON_PRETTY_PRINT));
            $messageType = "success";
            header("Location: view_todo.php");
            exit;
        }
    }
    foreach ($todos as $todo) {
        if ($todo['id'] === intval($_GET['todo'])) {
            $todo_item_to_be_edited = $todo;
            break;
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
       <?php if (! empty($messageText) && ! empty($messageType)): ?>
          <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>" role="alert">
              <?php echo htmlspecialchars($messageText); ?>
          </div>
       <?php endif; ?>
        <div class='d-flex justify-content-evenly' style="margin-top:15px;">
         <h1 style="text-align:center;">Welcome to Dashboard</h1>
         <button type="submit" class="btn btn-primary" id='view_todo'>View Todo</button>
         <?php include './require.php'; ?>
        </div>
        <div class="card" style="width: 50%; height:400px; margin:5% auto;">
            <div class="card-body">
                <h5 class="card-title">Todo Form</h5>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . (isset($_GET['todo']) ? '?todo=' . $_GET['todo'] : ''); ?>">
                    <div class="mb-3">
                        <label for="todo_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="todo_name"
                        name="todo_name"
                        value="<?php $value = $_GET['todo'] ? $todo_item_to_be_edited['name'] : "";
                               echo $value;
                               ?>"
                        >
                    </div>
                    <div class="mb-3">
                        <label for="todo_description" class="form-label">Description</label>
                        <textarea rows="4" class="form-control" id="todo_description" name="description"><?php $value = $_GET['todo'] ? htmlspecialchars(trim($todo_item_to_be_edited['description'])) : "";
                                                                                                         echo $value;
                                                                                                         ?></textarea>

                    </div>
                    <button type="submit" class="btn btn-primary" id="create_btn" style="display:none;">Create Todo</button>
                    <button type="submit" class="btn btn-primary" id="edit_btn" style="display:none;">Edit Todo</button>
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
       document.addEventListener("DOMContentLoaded", function() {
        const buttonRef = document.getElementById('view_todo');
        buttonRef.addEventListener("click",function(){
            window.location.href = "view_todo.php";
        });
       });
       const url = new URLSearchParams(window.location.search);
       const editButtonRef = document.getElementById("edit_btn");
       const createButtonRef = document.getElementById("create_btn");
      if(+url.get("todo")>0){
        editButtonRef.style.display="block";
      }else{
        createButtonRef.style.display="block";
      }
    </script>
  </body>
</html>
