<?php

class GlobalMethods{

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