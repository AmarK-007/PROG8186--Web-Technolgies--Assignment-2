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
            $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
            $limit = isset($_GET['limit']) ? $_GET['limit'] : null;

            if (!empty($product_id)) {
                // Get individual product
                $sql = "SELECT DISTINCT product_id FROM products WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $product_ids = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } elseif (!empty($limit)) {
                // Get limited number of products
                $sql = "SELECT DISTINCT product_id FROM products LIMIT ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $product_ids = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                // Get all products
                $sql = "SELECT DISTINCT product_id FROM products";
                $product_ids = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            }

            $products = array();
            foreach ($product_ids as $row) {
                $sql = "SELECT p.*, pi.image_url, ps.size_us, ps.quantity FROM products p 
                    LEFT JOIN productimages pi ON p.product_id = pi.product_id 
                    LEFT JOIN productsizes ps ON p.product_id = ps.product_id
                    WHERE p.product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $row['product_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($r = $result->fetch_assoc()) {
                    $product_id = $r['product_id'];
                    if (!isset($products[$product_id])) {
                        $products[$product_id] = array(
                            'product_id' => $r['product_id'],
                            'product_name' => $r['title'],
                            'product_description' => $r['description'],
                            'product_price' => $r['price'],
                            'image_url' => array(),
                            'sizes' => array()
                        );
                    }
                    $products[$product_id]['sizes'][] = array('size_us' => $r['size_us'], 'quantity' => $r['quantity']);
                    if (!in_array($r['image_url'], $products[$product_id]['image_url'])) {
                        $products[$product_id]['image_url'][] = $r['image_url'];
                    }
                }
            }

            if (!empty($products)) {
                echo json_encode(array_values($products));
            } else {
                echo json_encode(array("message" => "No products found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "GET request failed: " . $e->getMessage()));
        }
        break;

    case 'POST':
        try {
            $conn->begin_transaction();

            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->product_id) && !empty($data->title) && !empty($data->description) && !empty($data->price) && !empty($data->image_url) && !empty($data->sizes)) {
                $sql = "INSERT INTO products (product_id, title, description, price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issd", $data->product_id, $data->title, $data->description, $data->price);
                $stmt->execute();

                foreach ($data->image_url as $image_url) {
                    $sql = "INSERT INTO productimages (product_id, image_url) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $data->product_id, $image_url);
                    $stmt->execute();
                }

                foreach ($data->sizes as $size) {
                    $sql = "INSERT INTO productsizes (product_id, size_us, quantity) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isi", $data->product_id, $size->size_us, $size->quantity);
                    $stmt->execute();
                }

                echo json_encode(array("message" => "Product created"));
            } else {
                echo json_encode(array("message" => "Missing product data"));
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

            $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

            if (!empty($product_id)) {
                $title = !empty($data->title) ? $data->title : null;
                $description = !empty($data->description) ? $data->description : null;
                $price = !empty($data->price) ? $data->price : null;

                $sql = "UPDATE products SET title = COALESCE(?, title), description = COALESCE(?, description), price = COALESCE(?, price) WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdi", $title, $description, $price, $product_id);
                $stmt->execute();

                if (!empty($data->image_url)) {
                    $sql = "DELETE FROM productimages WHERE product_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();

                    foreach ($data->image_url as $image_url) {
                        $sql = "INSERT INTO productimages (product_id, image_url) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("is", $product_id, $image_url);
                        $stmt->execute();
                    }
                }

                if (!empty($data->sizes)) {
                    $sql = "DELETE FROM productsizes WHERE product_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();

                    foreach ($data->sizes as $size) {
                        $sql = "INSERT INTO productsizes (product_id, size_us, quantity) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("isi", $product_id, $size->size_us, $size->quantity);
                        $stmt->execute();
                    }
                }

                echo json_encode(array("message" => "Product updated"));
            } else {
                echo json_encode(array("message" => "Missing product_id"));
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
            $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

            if (!empty($product_id)) {
                // First delete the associated images
                $sql = "DELETE FROM productimages WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();

                // Then delete the associated sizes
                $sql = "DELETE FROM productsizes WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();

                // Finally, delete the product
                $sql = "DELETE FROM products WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();

                echo json_encode(array("message" => "Product deleted"));
            } else {
                echo json_encode(array("message" => "Missing product_id"));
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
