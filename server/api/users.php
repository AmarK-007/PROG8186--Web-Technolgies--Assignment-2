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
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $username = isset($_GET['username']) ? $_GET['username'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;

    if (!empty($id)) {
      $sql = "SELECT * FROM users WHERE user_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id);
    } elseif (!empty($username)) {
      $sql = "SELECT * FROM users WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $username);
    } else {
      $sql = "SELECT * FROM users";
      $stmt = $conn->prepare($sql);
    }

    if (!empty($limit) && is_numeric($limit)) {
      $sql .= " LIMIT ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $limit);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $rows = array();
      while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
      }
      echo json_encode($rows);
    } else {
      echo json_encode(array("message" => "No user found"));
    }
    break;


  case 'POST':

    $data = json_decode(file_get_contents("php://input"));
    // Check if all required fields are present
    if (!isset($data->name) || !isset($data->email) || !isset($data->password) || !isset($data->username) || !isset($data->purchase_history) || !isset($data->shipping_address)) {
      http_response_code(400);
      echo json_encode(array("message" => "Missing required field"));
      break;
    }
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
      /* // Hash the password before storing it
      $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

      $sql = "INSERT INTO users (name, email, password, username, purchase_history, shipping_address) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssssss", $data->name, $data->email, $hashed_password, $data->username, $data->purchase_history, $data->shipping_address);
      $stmt->execute(); */

      $sql = "INSERT INTO users (name, email, password, username, purchase_history, shipping_address) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssssss", $data->name, $data->email, $data->password, $data->username, $data->purchase_history, $data->shipping_address);
      $stmt->execute();

      echo json_encode(array("message" => "User created"));
    }
    break;

  case 'PUT':
    $id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $username = isset($_GET['username']) ? $_GET['username'] : null;
    $data = json_decode(file_get_contents("php://input"));

    // Check if all required fields are present
    if (!isset($data->name) || !isset($data->password) || !isset($data->purchase_history) || !isset($data->shipping_address)) {
      http_response_code(400);
      echo json_encode(array("message" => "Missing required field"));
      break;
    }

    // Check if user exists before trying to update
    $checkUser = "SELECT * FROM users WHERE user_id = ? OR username = ?";
    $stmt = $conn->prepare($checkUser);
    $stmt->bind_param("is", $id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
      http_response_code(404);
      echo json_encode(array("message" => "User not found"));
      break;
    }

    /* // Hash the new password before storing it
      $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
  
      $sql = "UPDATE users SET name = ?, password = ?, purchase_history = ?, shipping_address = ? WHERE user_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssssi", $data->name, $hashed_password, $data->purchase_history, $data->shipping_address, $id);
      $stmt->execute(); */
    $sql = "UPDATE users SET name = ?, password = ?, purchase_history = ?, shipping_address = ? WHERE user_id = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $data->name, $data->password, $data->purchase_history, $data->shipping_address, $id, $username);
    $stmt->execute();

    echo json_encode(array("message" => "User updated"));
    break;


  case 'DELETE':
    $id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $username = isset($_GET['username']) ? $_GET['username'] : null;

    if (!empty($id)) {
      $checkUser = "SELECT * FROM users WHERE user_id = ?";
      $deleteUser = "DELETE FROM users WHERE user_id = ?";
      $stmt = $conn->prepare($checkUser);
      $stmt->bind_param("i", $id);
    } elseif (!empty($username)) {
      $checkUser = "SELECT * FROM users WHERE username = ?";
      $deleteUser = "DELETE FROM users WHERE username = ?";
      $stmt = $conn->prepare($checkUser);
      $stmt->bind_param("s", $username);
    } else {
      echo json_encode(array("message" => "Missing user_id or username"));
      return;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
      http_response_code(404);
      echo json_encode(array("message" => "User not found"));
      return;
    }

    $stmt = $conn->prepare($deleteUser);
    if (!empty($id)) {
      $stmt->bind_param("i", $id);
    } else {
      $stmt->bind_param("s", $username);
    }
    $stmt->execute();

    echo json_encode(array("message" => "User deleted"));
    break;

  default:
    echo json_encode(array("message" => "Invalid request method"));
    break;
}
