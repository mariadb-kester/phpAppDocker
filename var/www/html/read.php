<?php
// Check existence of emp_no parameter before processing further
if(isset($_GET["emp_no"]) && !empty(trim($_GET["emp_no"]))){
    // Include config file
    require_once "../configs/config.php";

    // Prepare a select statement
    $sql = "SELECT * FROM employees WHERE emp_no = :emp_no";

    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":emp_no", $param_emp_no);

        // Set parameters
        $param_emp_no = trim($_GET["emp_no"]);

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Retrieve individual field value
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                $gender = $row["gender"];
            } else{
                // URL doesn't contain valid emp_no parameter. Redirect to error page
                header("location: error.php");
                exit();
            }

        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    unset($stmt);

    // Close connection
    unset($pdo);
} else{
    // URL doesn't contain emp_no parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mt-5 mb-3">View Record</h1>
                <div class="form-group">
                    <label>First Name</label>
                    <p><b><?php echo $row["first_name"]; ?></b></p>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <p><b><?php echo $row["last_name"]; ?></b></p>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <p><b><?php echo $row["gender"]; ?></b></p>
                </div>
                <p><a href="index.php" class="btn btn-primary">Back</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>