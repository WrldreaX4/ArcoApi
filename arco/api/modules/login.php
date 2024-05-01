<?php

class login extends GlobalMethods {

private $pdo;

public function __construct(\PDO $pdo){
    $this->pdo = $pdo;
}

public function login($data) {
    try {
        if (empty($data->email) || empty($data->password)) {
            throw new Exception("All input fields are required!");
        }

        // Prepare and execute the query to fetch user information based on email
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();

        // Fetch the result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) { 
            // Verify password
            $password_correct = password_verify($data->password, $user['password']);

            if ($password_correct) {  
                // Passwords match, login successful
                return $this->sendResponse("Login successful", null, 200);
            } else {
                // Passwords do not match
                return $this->sendResponse("Email or password is incorrect", null, 401);
            }
        } else {
            // User not found
            return $this->sendResponse("User not found for email: ".$data->email, null, 404);
        }
    } catch (\Exception $e) {
        return $this->sendResponse("Failed to login: ".$e->getMessage(), null, 400);
    }
}


private function sendResponse($message, $error, $statusCode) {
    $response = [
        'status:' => $statusCode,
        'message:' => $message,
        'error:' => $error
    ];

    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($response);
  }
 }
 exit();
?>