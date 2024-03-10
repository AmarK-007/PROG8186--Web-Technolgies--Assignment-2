<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'dbconfig.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $rows = array();
      while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
      }
      echo json_encode($rows);
    } else {
      echo json_encode(array("message" => "No users found"));
    }
    break;

  case 'POST':

    $data = json_decode(file_get_contents("php://input"));

    /*** In the bind_param method, the first parameter is a string that contains one or more characters which specify the types for the corresponding bind variables.
      "s" corresponds to string.
      "i" corresponds to integer.
      "d" corresponds to double.
      "b" corresponds to blob and will send the data in packets.
     */

    // Check if email or username already exists
    $checkEmailUsername = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($checkEmailUsername);
    $stmt->bind_param("ss", $data->email, $data->username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo json_encode(array("message" => "Email or username already exists"));
    } else {
      $sql = "INSERT INTO users (name, email, password, username, purchase_history, shipping_address) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssssss", $data->name, $data->email, $data->password, $data->username, $data->purchase_history, $data->shipping_address);
      $stmt->execute();

      echo json_encode(array("message" => "User created"));
    }
    break;

  case 'PUT':

    $id = $_GET['user_id'];
    $data = json_decode(file_get_contents("php://input"));

    $sql = "UPDATE users SET name = ?, password = ?, purchase_history = ?, shipping_address = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $data->name, $data->password, $data->purchase_history, $data->shipping_address, $id);
    $stmt->execute();

    echo json_encode(array("message" => "User updated"));
    break;

  case 'DELETE':
    $id = $_GET['user_id'];

    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(array("message" => "User deleted"));
    break;

    default:
    echo json_encode(array("message" => "Invalid request method"));
    break;
}
