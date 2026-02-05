<?php
$host = "sql202.infinityfree.com";
$name = "if0_40422239";
$password = "miguel122004";
$database = "if0_40422239_db_pims";

//Create connection
$connection = new mysqli($host, $name, $password, $database);


$name = "";
$email = "";
$password = "";
$role = ""; 

$errorMessage = "";
$successMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    do {
        if ( empty($name) || empty($email) || empty($password) || empty($role) ) {
            $errorMessage = "All the fields are required";
            break;
        }

       $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

          // add user to database
           $sql = "INSERT INTO users (name, email, password, role) 
                       VALUES ('$name', '$email', '$hashedPassword', '$role')";
                       $result = $connection->query($sql);
 
                          if (!$result) {
                             $errorMessage = "Invalid query: " . $connection->error;
                             break;
                            }
                  

        $name = "";
        $email = "";    
        $password = "";
        $role = "";

        $successMessage = "User Added Successfully!";

        header("location: ../Admin/user_management.php");
        exit;

    } while (false);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Add User</h2>
           
          <?php
          if ( !empty($errorMessage) ) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='role'>
            <strong>$errorMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </dive>
            ";
          }
          ?>



        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo $name;?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="email" value="<?php echo $email;?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="password" value="<?php echo $password;?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-6">
                    <select name="role" required>
                    <option value="">--Select Role--</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                  </select>

                </div>
            </div>

            <?php
            if ( !empty($successMessage) ) {
                echo "
                <div class='row mb-3'>
                     <div class='offset-sm-3 col-sm-6'>
                     <div class='alert alert-success alert-dismissible fade show' role='alert'>
                     <strong>$successMessage</strong>
                     <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                     </div>
                     </div>
                </div>
                
                ";
            }
            ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="../Admin/user_management.php" role="button">Cancel</a>
                </div>
            </div>
            
        </form>
    </div>
    <script>
  // Mag-auto hide ng alerts after 3 seconds
  setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.classList.remove('show');
      alert.classList.add('fade');
      alert.style.display = 'none';
    });
  }, 3000); // 3000ms = 3 seconds
</script>

</body>
</html>