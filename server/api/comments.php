<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'dbconfig.php';

class Comment
{
    private $conn;
    private $table_name = "comments";

    public $comment_id;
    public $product_id;
    public $user_id;
    public $rating;
    public $image_url;
    public $comment;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function readByProduct($limit = null)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ?";
        if ($limit !== null) {
            $query .= " LIMIT ?";
        }
        $stmt = $this->conn->prepare($query);
        if ($limit !== null) {
            $stmt->bind_param("ii", $this->product_id, $limit);
        } else {
            $stmt->bind_param("i", $this->product_id);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET product_id = ?, user_id = ?, rating = ?, image_url = ?, comment = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiiss", $this->product_id, $this->user_id, $this->rating, $this->image_url, $this->comment);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function update()
    {
        if (!$this->exists()) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . " SET rating = ?, image_url = ?, comment = ? WHERE ";
        $params = array($this->rating, $this->image_url, $this->comment);
        $types = "sss";

        if (!empty($this->comment_id)) {
            $query .= "comment_id = ?";
            $params[] = $this->comment_id;
            $types .= "i";
        } else if (!empty($this->user_id) && !empty($this->product_id)) {
            $query .= "user_id = ? AND product_id = ?";
            $params[] = $this->user_id;
            $params[] = $this->product_id;
            $types .= "ii";
        } else {
            return false;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE ";
        $params = array();
        $types = "";
    
        if (!empty($this->comment_id)) {
            $query .= "comment_id = ?";
            $params[] = $this->comment_id;
            $types .= "i";
        } else if (!empty($this->user_id) && !empty($this->product_id)) {
            $query .= "user_id = ? AND product_id = ?";
            $params[] = $this->user_id;
            $params[] = $this->product_id;
            $types .= "ii";
        } else {
            return false;
        }
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function exists()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE comment_id = ? OR (user_id = ? AND product_id = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $this->comment_id, $this->user_id, $this->product_id);
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
$comment = new Comment($db); */
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

$comment = new Comment($conn);


$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {
    case 'GET':
        if (!empty($_GET["product_id"])) {
            $comment->product_id = intval($_GET["product_id"]);
            $limit = isset($_GET["limit"]) ? $_GET["limit"] : null;
            if ($limit !== null && !is_numeric($limit)) {
                http_response_code(400);
                echo json_encode(array("message" => "Invalid limit value."));
                break;
            }
            $limit = $limit !== null ? intval($limit) : null;
            $stmt = $comment->readByProduct($limit);
            if ($stmt->num_rows > 0) {
                $comments = array();
                while ($row = $stmt->fetch_assoc()) {
                    array_push($comments, $row);
                }
                http_response_code(200);
                echo json_encode($comments);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Product not found."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Product ID is missing."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->product_id) && !empty($data->user_id) && !empty($data->rating) && !empty($data->comment)) {
            $comment->product_id = $data->product_id;
            $comment->user_id = $data->user_id;
            $comment->rating = $data->rating;
            $comment->image_url = $data->image_url;
            $comment->comment = $data->comment;
            if ($comment->create()) {
                http_response_code(200);
                echo json_encode(array("message" => "Comment created."));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Unable to create comment."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create comment. Data is incomplete."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->comment_id) && !empty($data->comment_id)) {
            $comment->comment_id = intval($data->comment_id);
        } else if (isset($data->user_id) && !empty($data->user_id) && isset($data->product_id) && !empty($data->product_id)) {
            $comment->user_id = intval($data->user_id);
            $comment->product_id = intval($data->product_id);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update comment. Comment ID or User ID and Product ID are missing."));
            break;
        }

        $comment->rating = $data->rating;
        $comment->image_url = $data->image_url;
        $comment->comment = $data->comment;

        if ($comment->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Comment updated."));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Comment not found. Unable to update."));
        }
        break;



    case 'DELETE':
        if (!empty($_GET["comment_id"]) || (!empty($_GET["user_id"]) && !empty($_GET["product_id"]))) {
            $comment->comment_id = isset($_GET["comment_id"]) ? intval($_GET["comment_id"]) : null;
            $comment->user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : null;
            $comment->product_id = isset($_GET["product_id"]) ? intval($_GET["product_id"]) : null;

            if ($comment->exists() && $comment->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Comment deleted."));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Comment not found."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Comment ID or User ID and Product ID are missing."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Request method not allowed."));
        break;
}
