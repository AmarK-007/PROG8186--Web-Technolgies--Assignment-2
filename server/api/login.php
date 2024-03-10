<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'dbconfig.php';

// Assuming you have a connection to your database in $conn
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || empty($data->username)) {
    echo json_encode(["status" => false, "message" => "Please enter username."]);
    exit();
}

if (!isset($data->password) || empty($data->password)) {
    echo json_encode(["status" => false, "message" => "Please enter password."]);
    exit();
}

$username = $data->username;
$password = $data->password;


// Prepare a select statement
$sql = "SELECT user_id, username, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

// Bind variables to the prepared statement as parameters
$stmt->bind_param("s", $param_username);

// Set parameters
$param_username = $username;

// Attempt to execute the prepared statement
if ($stmt->execute()) {
    // Store result
    $stmt->store_result();

    // Check if username exists, if yes then verify password
    if ($stmt->num_rows == 1) {
        // Bind result variables
        /*  hashed password and passwordverify used here
        
        $stmt->bind_result($user_id, $username, $hashed_password);
        if($stmt->fetch()){
            if(password_verify($password, $hashed_password)){ */
        $stmt->bind_result($user_id, $username, $db_password);
        if ($stmt->fetch()) {
            if ($password == $db_password) {
                // Password is correct, so start a new session
                session_start();

                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;

                // Prepare an array to return as JSON
                $user_arr = array(
                    "status" => true,
                    "message" => "Successfully Login!",
                    "user_id" => $user_id,
                    "username" => $username
                );
            } else {
                // Prepare an array to return as JSON
                $user_arr = array(
                    "status" => false,
                    "message" => "Invalid password",
                );
            }
        }
    } else {
        // Prepare an array to return as JSON
        $user_arr = array(
            "status" => false,
            "message" => "Invalid username",
        );
    }
} else {
    $user_arr = array(
        "status" => false,
        "message" => "Oops! Something went wrong. Please try again later.",
    );
}

// Close statement
$stmt->close();

// Close connection
$conn->close();

// Return the JSON response
print_r(json_encode($user_arr));
?>
