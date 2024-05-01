<?php
// Include necessary files
require_once "./modules/get.php";
require_once "./modules/post.php";
require_once "./config/database.php";

// Handle OPTIONS requests for CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Content-Type: application/json; charset=UTF-8");
    header("HTTP/1.1 200 OK");
    exit();
}
// Other headers for actual requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Initialize database connection
$conn = new Connection();
$pdo = $conn->connect();

// Initialize Get and Post objects
$get = new Get($pdo);
$post = new Post($pdo);

// Check if 'request' parameter is set in the request
if (isset($_REQUEST['request'])) {
    // Split the request into an array based on '/'
    $request = explode('/', $_REQUEST['request']);
} else {
    // If 'request' parameter is not set, return a 404 response
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    // Handle GET requests
    case 'GET':
        switch ($request[0]) {
            case 'get_signup':
                $result = [];
                if (count($request) > 1) {
                    $result = $get->get_signup($request[1]);
                } else {
                    $result = $get->get_signup();
                }
                // Check if there is valid data to output
                if (!empty($result)) {
                    echo json_encode($result);
                } else {
                    // Output an appropriate JSON error message
                    http_response_code(404);
                    echo json_encode(["error" => "Data not found"]);
                }
                break;
            default:
                // Return a 403 response for unsupported requests
                http_response_code(403);
                echo json_encode(["error" => "Forbidden"]);
                break;
        }
        break;
    // Handle POST requests
    case 'POST':
        // Decode JSON data from request body
        $data = json_decode(file_get_contents("php://input"));
        switch ($request[0]) {
            
            case 'signup':
                $result = $post->signup($data);
                echo json_encode($result);
                break;

            case 'login':
                $result = $post->login($data);
                echo json_encode($result);
                break;

            default:
                http_response_code(403);
                echo json_encode(["error" => "Forbidden"]);
                break;
        }
        break;
    default:
        // Return a 405 response for unsupported HTTP methods
        http_response_code(405);
        echo json_encode(["error" => "Method Not Allowed"]);
        break;
}

?>
