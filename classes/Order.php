<?php
class Order
{
    private $id;
    private $user_id;
    private $total_price;
    private $address;
    private $created_at;

    // Getters and Setters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function save()
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO orders (user_id, total_price, address) VALUES (:user_id, :total_price, :address)');
        $query->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $query->bindValue(':total_price', $this->total_price, PDO::PARAM_STR);
        $query->bindValue(':address', $this->address, PDO::PARAM_STR);
        $query->execute();
        $this->id = $db->lastInsertId();
        return $this->id;
    }

    public static function getByUserId($userId)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function hasPurchasedProduct($userId, $productId)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT COUNT(*) FROM order_items JOIN orders ON order_items.order_id = orders.id WHERE orders.user_id = :user_id AND order_items.product_id = :product_id");
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':product_id', $productId);
        $statement->execute();
        return $statement->fetchColumn() > 0;
    }

    public static function processCheckout($userId, $address)
    {
        // Get the user's basket
        $basket = Basket::getBasket($userId);
        if (!$basket) {
            throw new Exception('Basket not found.');
        }

        // Get basket items
        $basketItems = BasketItem::getItemsByBasketId($basket['id']);
        if (empty($basketItems)) {
            throw new Exception('Your basket is empty.');
        }

        // Calculate total price of the basket
        $totalPrice = 0;
        foreach ($basketItems as $item) {
            $totalPrice += $item['total_price'];
        }

        // Get the user's current currency
        $user = User::getById($userId);
        $currentCurrency = $_SESSION['user']['currency'];

        // Check if user has enough currency
        if ($currentCurrency < $totalPrice) {
            throw new Exception('You do not have enough digital currency to complete this purchase.');
        }

        try {
            // Start a database transaction
            $db = Db::getConnection();
            $db->beginTransaction();

            // Deduct total price from user's currency
            $newCurrency = $currentCurrency - $totalPrice;
            $updateCurrencyQuery = $db->prepare('UPDATE users SET currency = :currency WHERE id = :user_id');
            $updateCurrencyQuery->bindValue(':currency', $newCurrency, PDO::PARAM_INT);
            $updateCurrencyQuery->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $updateCurrencyQuery->execute();

            // Save the order
            $order = new Order();
            $order->setUserId($userId);
            $order->setTotalPrice($totalPrice);
            $order->setAddress($address);
            $orderId = $order->save();

            // Save each item in the order
            foreach ($basketItems as $item) {
                $orderItem = new OrderItem();
                $orderItem->setOrderId($orderId);
                $orderItem->setProductId($item['product_id']);
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setPrice($item['price']);
                $orderItem->setOptionIds($item['option_ids']);
                $orderItem->setPriceAddition($item['price_addition']);
                $orderItem->setTotalPrice($item['total_price']);
                $orderItem->save();

                // Update the stock for the product
                $product = Product::getById($item['product_id']);
                if ($product) {
                    $newStock = $product['stock'] - $item['quantity'];
                    if ($newStock < 0) {
                        throw new Exception('Not enough stock for product ID: ' . $item['product_id']);
                    }
                    $productObj = new Product();
                    $productObj->setId($product['id']);
                    $productObj->setStock($newStock);
                    $productObj->save();
                } else {
                    throw new Exception('Product not found: ' . $item['product_id']);
                }
            }

            // Clear the user's basket
            BasketItem::clearBasket($basket['id']);

            // Commit the transaction
            $db->commit();

            // Update the user's session currency
            $_SESSION['user']['currency'] = $newCurrency;

            // Redirect to success page
            header('Location: success.php');
            exit();
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $db->rollBack();
            throw new Exception('An error occurred during checkout: ' . $e->getMessage());
        }
    }
    // get last order by user id
    public static function getLastOrderByUserId($userId, $limit)

    {

        $db = Db::getConnection();

        $query = $db->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit');

        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);

        $query->bindValue(':limit', $limit, PDO::PARAM_INT);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);

    }
}
?>