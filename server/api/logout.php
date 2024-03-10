<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    echo json_encode(["status" => false, "message" => "No active session found. Please login first."]);
    exit();
}

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Prepare an array to return as JSON
$user_arr=array(
    "status" => true,
    "message" => "Successfully Logout!",
);

// Return the JSON response
print_r(json_encode($user_arr));
?>