<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'dbconfig.php';

class Cart
{
    private $conn;
    private $table_name = "cart";

    public $cart_id;
    public $product_id;
    public $user_id;
    public $quantity;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function readByUser()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET product_id = ?, user_id = ?, quantity = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $this->product_id, $this->user_id, $this->quantity);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . " SET product_id = ?, user_id = ?, quantity = ? WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiii", $this->product_id, $this->user_id, $this->quantity, $this->cart_id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->cart_id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function exists() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->cart_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}

/* include_once 'dbconfig.php';

try {
    $db = include('dbconfig.php');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
    exit();
}
$cart = new Cart($db); */
include_once 'dbconfig.php';

try {
    // Check if the $conn variable is set and is a valid MySQLi object
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Database connection error");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
    exit();
}

$cart = new Cart($conn);



$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {
    case 'GET':
        if (!empty($_GET["user_id"])) {
            $cart->user_id = intval($_GET["user_id"]);
            $stmt = $cart->readByUser();
            if ($stmt->num_rows > 0) {
                $carts = array();
                while ($row = $stmt->fetch_assoc()) {
                    array_push($carts, $row);
                }
                http_response_code(200);
                echo json_encode($carts);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No cart items found for this user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "User ID is missing."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->product_id) && !empty($data->user_id) && !empty($data->quantity)) {
            $cart->product_id = $data->product_id;
            $cart->user_id = $data->user_id;
            $cart->quantity = $data->quantity;
            if ($cart->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Cart item created."));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Unable to create cart item."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create cart item. Data is incomplete."));
        }
        break;

    case 'PUT':
        if (!empty($_GET["cart_id"])) {
            $cart->cart_id = intval($_GET["cart_id"]);
            $data = json_decode(file_get_contents("php://input"));
            $cart->product_id = $data->product_id;
            $cart->user_id = $data->user_id;
            $cart->quantity = $data->quantity;
            if ($cart->exists() && $cart->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Cart item updated."));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No cart item found with this ID or unable to update the cart item."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update cart item. Cart ID is missing."));
        }
        break;

    case 'DELETE':
        if (!empty($_GET["cart_id"])) {
            $cart->cart_id = intval($_GET["cart_id"]);
            if ($cart->exists() && $cart->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Cart item deleted."));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No cart item found with this ID or unable to delete the cart item."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to delete cart item. Cart ID is missing."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Request method not allowed."));
        break;
}
?>