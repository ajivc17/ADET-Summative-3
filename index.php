<?php
session_start();

// Initialize todoList if it's not set in the session
if (!isset($_SESSION["todoList"])) {
    $_SESSION["todoList"] = [];
}

// Function to add a new task to the list
function addTask($task, $dateTime, $completed, $todoList) {
    $todoList[] = array("task" => $task, "dateTime" => $dateTime, "completed" => $completed);
    return $todoList;
}

// Function to delete a task from the list
function deleteTask($index, $todoList) {
    if (array_key_exists($index, $todoList)) {
        unset($todoList[$index]);
        // Re-index the array
        $todoList = array_values($todoList);
    }
    return $todoList;
}

// Function to edit a task in the list
function editTask($index, $task, $dateTime, $completed, $todoList) {
    if (array_key_exists($index, $todoList)) {
        $todoList[$index]["task"] = $task;
        $todoList[$index]["dateTime"] = $dateTime;
        $todoList[$index]["completed"] = $completed;
    }
    return $todoList;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task"])) {
    $task = trim($_POST["task"]);
    $dateTime = isset($_POST["dateTime"]) ? $_POST["dateTime"] : '';
    $completed = isset($_POST["completed"]) ? 1 : 0;
    if (!empty($task)) {
        if (isset($_POST["index"]) && $_POST["index"] !== '') {
            // Editing an existing task
            $index = intval($_POST["index"]);
            $_SESSION["todoList"] = editTask($index, $task, $dateTime, $completed, $_SESSION["todoList"]);
        } else {
            // Adding a new task
            $_SESSION["todoList"] = addTask($task, $dateTime, $completed, $_SESSION["todoList"]);
        }
    }
}

// Process deletion of a task
if (isset($_GET['delete'])) {
    $indexToDelete = intval($_GET['delete']);
    $_SESSION["todoList"] = deleteTask($indexToDelete, $_SESSION["todoList"]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Process editing of a task
$taskToEdit = '';
$dateTimeToEdit = '';
$indexToEdit = '';
$completedToEdit = 0;
if (isset($_GET['edit'])) {
    $indexToEdit = intval($_GET['edit']);
    if (array_key_exists($indexToEdit, $_SESSION["todoList"])) {
        $taskToEdit = $_SESSION["todoList"][$indexToEdit]['task'];
        $dateTimeToEdit = $_SESSION["todoList"][$indexToEdit]['dateTime'];
        $completedToEdit = isset($_SESSION["todoList"][$indexToEdit]['completed']) ? $_SESSION["todoList"][$indexToEdit]['completed'] : 0;
    }
}

// Array of background image URLs
$backgroundImages = [
    'https://cdn.openart.ai/stable_diffusion/c174d48619a93fc73eec7c72c36bad1c18b2b54c_2000x2000.webp',
    'https://cdn.openart.ai/stable_diffusion/c174d48619a93fc73eec7c72c36bad1c18b2b54c_2000x2000.webp',
    'https://i.pinimg.com/564x/93/6c/05/936c054d461659d94c04723f8507ca5e.jpg',
    'https://as2.ftcdn.net/v2/jpg/04/41/38/41/1000_F_441384157_gOfxYsE6A12zRUizcWsdmBwOpSI8bQLM.jpg'
];

// Select a random background image URL
$randomImage = $backgroundImages[array_rand($backgroundImages)];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('<?php echo $randomImage; ?>');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">ToDo List</h1>
        <div class="card">
            <div class="card-header">Add a new Task</div>
            <div class="card-body">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <input type="text" class="form-control" name="task" placeholder="Enter task" value="<?php echo htmlspecialchars($taskToEdit); ?>">
                        <input type="hidden" name="index" value="<?php echo htmlspecialchars($indexToEdit); ?>">
                    </div>
                    <div class="form-group">
                        <input type="datetime-local" class="form-control" name="dateTime" value="<?php echo htmlspecialchars($dateTimeToEdit); ?>">
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="completed" value="1" <?php echo $completedToEdit ? 'checked' : ''; ?>> Completed
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $taskToEdit ? 'Edit Task' : 'Add Task'; ?></button>
                </form>
            </div>
        </div>
    <div class="card mt-4">
        <div class="card-header">Tasks</div>
        <ul class="list-group list-group-flush">
            <?php
            foreach ($_SESSION["todoList"] as $index => $taskArray) {
                $task = htmlspecialchars($taskArray["task"]);
                $dateTime = date('F j, Y, g:i A', strtotime($taskArray["dateTime"]));
                $completed = isset($taskArray["completed"]) && $taskArray["completed"] ? 'checked' : '';
                echo '<li class="list-group-item d-flex justify-content-between">' . 
                    '<div>' . $task . '</div>' . 
                    '<div>' . $dateTime . '</div>' .
                    '<div class="action">' .
                    '<input type="checkbox" disabled ' . $completed . '> Completed' .
                    '<a href="' . $_SERVER['PHP_SELF'] . '?edit=' . $index . '" class="btn btn-info btn-sm ml-2"><i class="fas fa-edit"></i> Edit</a>' .
                    '<a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $index . '" class="btn btn-danger btn-sm ml-2"><i class="fas fa-trash-alt"></i> Delete</a>' .
                    '</div>' .
                    '</li>';
            }
            ?>
        </ul>
    </div>
</div>

<a href="index.html" class="button1">Home</a>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>