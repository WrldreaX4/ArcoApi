<?php 
  class reports extends GlobalMethods {

    private $pdo;
  
      public function __construct(\PDO $pdo){
          $this->pdo = $pdo;
      }

      public function report($data) {

        // Check if the necessary data is provided
        if (!isset($data->report_id) || !isset($data->title) || !isset($data->description) || !isset($data->time_created) || !isset($data->user_id)) {
            return $this->sendResponse("Missing fields", null, 400);
        }
    
        try {
            // Prepare SQL statement to insert the report into the database
            $stmt = $this->pdo->prepare("INSERT INTO reports (title, description, time_created, user_id) 
                                        VALUES (:title, :description, :time_created, :user_id)");
            $stmt->execute([
                'title' => $data->title,
                'description' => $data->description,
                'time_created' => $data->time_created,
                'user_id' => $data->user_id
            ]);
    
            return $this->sendResponse("Report generated successfully", null, 200);
        } catch (\PDOException $e) {
            return $this->sendResponse("Failed to generate report", $e->getMessage(), 500);
        }
    }

    private function sendResponse($message, $error, $statusCode) {
        $response = [
            'message' => $message
        ];
        if ($error !== null) {
            $response['error'] = $error;
        }
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

  }


?>