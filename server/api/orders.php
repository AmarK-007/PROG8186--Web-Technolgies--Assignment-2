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
        try {
            $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
            $limit = isset($_GET['limit']) ? $_GET['limit'] : null;

            if (!empty($order_id)) {
                // Get individual order with details
                $sql = "SELECT orders.*, orderdetails.* FROM orders 
                        INNER JOIN orderdetails ON orders.order_id = orderdetails.order_id 
                        WHERE orders.order_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } elseif (!empty($limit)) {
                // Get limited number of orders with details
                $sql = "SELECT orders.*, orderdetails.* FROM orders 
                        INNER JOIN orderdetails ON orders.order_id = orderdetails.order_id 
                        LIMIT ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                // Get all orders with details
                $sql = "SELECT orders.*, orderdetails.* FROM orders 
                        INNER JOIN orderdetails ON orders.order_id = orderdetails.order_id";
                $orders = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            }

            if (!empty($orders)) {
                echo json_encode($orders);
            } else {
                echo json_encode(array("message" => "No orders found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "GET request failed: " . $e->getMessage()));
        }
        break;

    case 'POST':
        try {
            $conn->begin_transaction();

            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->order_id) && !empty($data->user_id) && !empty($data->total_amount) && !empty($data->order_date) && !empty($data->payment_method) && !empty($data->delivery_status) && !empty($data->return_status)) {
                $sql = "INSERT INTO orders (order_id, user_id, total_amount, order_date, payment_method, delivery_status, return_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisssss", $data->order_id, $data->user_id, $data->total_amount, $data->order_date, $data->payment_method, $data->delivery_status, $data->return_status);
                $stmt->execute();

                foreach ($data->orderdetails as $detail) {
                    $sql = "INSERT INTO orderdetails (order_id, product_id, quantity, product_size) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiis", $data->order_id, $detail->product_id, $detail->quantity, $detail->product_size);
                    $stmt->execute();
                }

                $sql = "
                        UPDATE productsizes ps
                        INNER JOIN (
                            SELECT od.product_id, od.quantity, od.product_size
                            FROM orders o
                            INNER JOIN orderdetails od ON o.order_id = od.order_id
                            WHERE o.order_id = ?
                        ) o ON ps.product_id = o.product_id AND ps.size_us = o.product_size
                        SET ps.quantity = ps.quantity - o.quantity
                    ";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $data->order_id);
                $stmt->execute();

                echo json_encode(array("message" => "Order created"));
            } else {
                echo json_encode(array("message" => "Missing order data"));
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(array("message" => "POST request failed: " . $e->getMessage()));
        }
        break;

        case 'PUT':
            try {
                $conn->begin_transaction();
        
                $data = json_decode(file_get_contents("php://input"));
        
                // Check if all necessary fields are set and are of the correct type
                if (!isset($data->user_id, $data->total_amount, $data->order_date, $data->payment_method, $data->delivery_status, $data->return_status, $data->orderdetails) || 
                    !is_int($data->user_id) || !is_float($data->total_amount) || !is_string($data->order_date) || !is_string($data->payment_method) || 
                    !is_string($data->delivery_status) || !is_string($data->return_status) || !is_array($data->orderdetails)) {
                    echo json_encode(array("message" => "Invalid or missing data"));
                    return;
                }
        
                $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
        
                if (!empty($order_id)) {
                    // Check if order_id exists in the database
                    $sql = "SELECT * FROM orders WHERE order_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $order_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
        
                    if ($result->num_rows == 0) {
                        echo json_encode(array("message" => "Order not found"));
                        return;
                    }
        
                    // Since we've checked that all fields are set and are of the correct type, we can directly assign them
                    $user_id = $data->user_id;
                    $total_amount = $data->total_amount;
                    $order_date = $data->order_date;
                    $payment_method = $data->payment_method;
                    $delivery_status = $data->delivery_status;
                    $return_status = $data->return_status;
        
                    $sql = "UPDATE orders SET user_id = ?, total_amount = ?, order_date = ?, payment_method = ?, delivery_status = ?, return_status = ? WHERE order_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("idissssi", $user_id, $total_amount, $order_date, $payment_method, $delivery_status, $return_status, $order_id);
                    $stmt->execute();
        
                    // Update orderdetails
                    foreach ($data->orderdetails as $detail) {
                        if (!isset($detail->product_id, $detail->quantity, $detail->product_size) || !is_int($detail->product_id) || !is_int($detail->quantity) || !is_string($detail->product_size)) {
                            echo json_encode(array("message" => "Invalid or missing data in orderdetails"));
                            return;
                        }
        
                        $product_id = $detail->product_id;
                        $quantity = $detail->quantity;
                        $product_size = $detail->product_size;
        
                        $sql = "UPDATE orderdetails SET product_id = ?, quantity = ?, product_size = ? WHERE order_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iisi", $product_id, $quantity, $product_size, $order_id);
                        $stmt->execute();
                    }
        
                    // Update productsizes
                    $sql = "
                        UPDATE productsizes ps
                        INNER JOIN (
                            SELECT od.product_id, od.quantity, od.product_size
                            FROM orders o
                            INNER JOIN orderdetails od ON o.order_id = od.order_id
                            WHERE o.order_id = ?
                        ) o ON ps.product_id = o.product_id AND ps.size_us = o.product_size
                        SET ps.quantity = ps.quantity - o.quantity
                    ";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $order_id);
                    $stmt->execute();
        
                    echo json_encode(array("message" => "Order updated"));
                } else {
                    echo json_encode(array("message" => "Missing order_id"));
                }
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(array("message" => "PUT request failed: " . $e->getMessage()));
            }
            break;



            case 'DELETE':
                try {
                    $conn->begin_transaction();
                    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
            
                    if (!empty($order_id)) {
                        // Check if order_id exists in the database
                        $sql = "SELECT * FROM orders WHERE order_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $order_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
            
                        if ($result->num_rows == 0) {
                            echo json_encode(array("message" => "Order not found"));
                            return;
                        }
            
                        // Delete the order details
                        $sql = "DELETE FROM orderdetails WHERE order_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $order_id);
                        $stmt->execute();
            
                        // Delete the order
                        $sql = "DELETE FROM orders WHERE order_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $order_id);
                        $stmt->execute();
            
                        echo json_encode(array("message" => "Order deleted"));
                    } else {
                        echo json_encode(array("message" => "Missing order_id"));
                    }
                    $conn->commit();
                } catch (Exception $e) {
                    $conn->rollback();
                    echo json_encode(array("message" => "DELETE request failed: " . $e->getMessage()));
                }
                break;

    default:
        echo json_encode(array("message" => "Invalid request method"));
        break;
}
