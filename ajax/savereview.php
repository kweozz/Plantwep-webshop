<?php
session_start();
header('Content-Type: application/json');


$response = [];

try {
    if (!empty($_POST)) {
        include_once(__DIR__ . '/../classes/Review.php');
        include_once(__DIR__ . '/../classes/user.php');

        if (!isset($_SESSION['user']['id'])) {
            throw new Exception('User not logged in.');
        }

        // Sanitize and validate inputs: filter_input(INPUT_POST, 'input_name', FILTER_VALIDATE_INT) is een handige functie om input te valideren.
        // Hiermee kun je controleren of een variabele een integer is. Als de variabele geen integer is, wordt er false geretourneerd.  info op https://www.php.net/manual/en/function.filter-input.php
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

        if (!$product_id || !$user_id || !$rating || !$comment) {
            throw new Exception('Invalid input.');
        }

        // Fetch user details
        $user = User::getById($user_id);
        if (!$user) {
            throw new Exception('User not found.');
        }

        $review = new Review();
        $review->setUserId($user_id);
        $review->setProductId($product_id);
        $review->setRating($rating);
        $review->setComment($comment);

        if ($review->save()) {
            $response = [
                'status' => 'success',
                'body' => htmlspecialchars($review->getComment()),
                'rating' => $review->getRating(),
                'user_name' => htmlspecialchars($user->getName()),
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