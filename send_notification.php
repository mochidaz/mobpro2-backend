<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Dotenv\Dotenv;

function sendFCMMessage($token, $title, $body) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $firebaseCredentialsPath = $_ENV['FIREBASE_CREDENTIALS'];

    $factory = (new Factory)
        ->withServiceAccount($firebaseCredentialsPath);

    $messaging = $factory->createMessaging();

    $message = CloudMessage::withTarget('token', $token)
        ->withNotification([
            'title' => $title,
            'body' => $body,
        ]);

    try {
        $response = $messaging->send($message);
        return $response;
    } catch (Exception $e) {
        return 'Error mengirim pesan: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $token = $data['token'] ?? null;
    $title = $data['title'] ?? null;
    $body = $data['body'] ?? null;

    if (!$token || !$title || !$body) {
        http_response_code(400);
        echo json_encode(['error' => 'Data tidak lengkap']);
        exit;
    }

    $result = sendFCMMessage($token, $title, $body);

    echo json_encode([
        'message' => 'Notification sent',
        'result' => $result
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method tidak diizinkan']);
}
