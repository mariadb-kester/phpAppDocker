<?php
// Include config file
require_once "../configs/config.php";

// Define variables and initialize with empty values
$first_name = $last_name = $gender = "";
$first_name_err = $last_name_err = $gender_err = "";

// Processing form data when form is submitted
if(isset($_POST["emp_no"]) && !empty($_POST["emp_no"])){
    // Get hidden input value
    $emp_no = $_POST["emp_no"];

    // Validate name
    $input_name = trim($_POST["first_name"]);
    if(empty($input_name)){
        $first_name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $first_name_err = "Please enter a valid name.";
    } else{
        $first_name = $input_name;
    }

    // Validate last_name last_name
    $input_last_name = trim($_POST["last_name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last_name.";
    } else{
        $last_name = $input_last_name;
    }

    $input_gender = trim($_POST["gender"]);
    if(empty($input_gender)){
        $gender_err = "Please enter a gender. (M/F)";
    } elseif ($input_gender == "M" || $input_gender == "F" ) {
        $gender = $input_gender;
    } else{
        $gender_err = "Gender can only be M or F.";
    }

    // Check input errors before inserting in database
    if(empty($first_name_err) && empty($last_name_err) && empty($gender_err)){
        // Prepare an update statement
        $sql = "UPDATE employees SET first_name=:first_name, last_name=:last_name, gender=:gender WHERE emp_no=:emp_no";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":first_name", $param_first_name);
            $stmt->bindParam(":last_name", $param_last_name);
            $stmt->bindParam(":gender", $param_gender);
            $stmt->bindParam(":emp_no", $param_emp_no);

            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_gender = $gender;
            $param_emp_no = $emp_no;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($pdo);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["emp_no"]) && !empty(trim($_GET["emp_no"]))){
        // Get URL parameter
        $emp_no =  trim($_GET["emp_no"]);

        // Prepare a select statement
        $sql = "SELECT * FROM employees WHERE emp_no = :emp_no";
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":emp_no", $param_emp_no);

            // Set parameters
            $param_emp_no = $emp_no;

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
                    // URL doesn't contain valid emp_no. Redirect to error page
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
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
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
                <h2 class="mt-5">Update Record</h2>
                <p>Please edit the input values and submit to update the employee record.</p>
                <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>">
                        <span class="invalid-feedback"><?php echo $first_name_err;?></span>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text"  name="last_name" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>">
                        <span class="invalid-feedback"><?php echo $last_name_err;?></span>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <input type="text" name="gender" class="form-control <?php echo (!empty($gender_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $gender; ?>">
                        <span class="invalid-feedback"><?php echo $gender_err;?></span>
                    </div>
                    <input type="hidden" name="emp_no" value="<?php echo $emp_no; ?>"/>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>