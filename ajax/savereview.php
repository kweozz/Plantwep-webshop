<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = [];

try {
    if (!empty($_POST)) {
        include_once(__DIR__ . '/../classes/Review.php');
        include_once(__DIR__ . '/../classes/User.php');

        if (!isset($_SESSION['user']['id'])) {
            throw new Exception('User not logged in.');
        }

        // Fetch user details
        $user = User::getById($_SESSION['user']['id']);
        if (!$user) {
            throw new Exception('User not found.');
        }

        $review = new Review();
        $review->setUserId($_SESSION['user']['id']);
        $review->setProductId($_POST['product_id']);
        $review->setRating(5); // Assuming a fixed rating for testing
        $review->setComment($_POST['comment']);

        if ($review->save()) {
            $response = [
                'status' => 'success',
                'body' => htmlspecialchars($review->getComment()),
                'rating' => $review->getRating(),
                'user_name' => $user->getName(),
                'message' => 'Review is geplaatst!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to save review.'
            ];
        }
    } else {
        throw new Exception('No POST data received.');
    }
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>