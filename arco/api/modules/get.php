<?php
require_once "global.php";

class Get extends GlobalMethods {

    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function get_records($table, $condition = null) {
        try {
            $sqlString = "SELECT * FROM $table";
            if ($condition != null) {
                $sqlString .= " WHERE " . $condition;
            }

            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->sendResponse('Successfully retrieved records', $result, null, 200);
            } else {
                return $this->sendResponse('No data found', null, null, 404);
            }
        } catch (\PDOException $e) {
            return $this->sendResponse($e->getMessage(), null, null, 500);
        }
    }

    public function get_signup($id = null) {
        $conditionString = $id != null ? "user_id = $id" : null;
        return $this->get_records("users", $conditionString);
    }

    private function sendResponse($message, $data = null, $error = null, $statusCode) {
        $response = [
            'success' => ($statusCode >= 200 && $statusCode < 300),
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($error !== null) {
            $response['error'] = $error;
        }
      
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

?>
