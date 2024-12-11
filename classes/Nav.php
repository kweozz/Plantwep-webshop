<!-- nav.php -->
<?php
require_once 'classes/Db.php';
require_once 'classes/Basket.php';
require_once 'classes/BasketItem.php';
require_once 'classes/Product.php';
require_once 'classes/Option.php';

$totalItems = 0;
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $basket = Basket::getBasket($userId);
    if ($basket) {
        $totalItems = BasketItem::getTotalItems($basket['id']);
    }
}
?>
<nav>
    <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
    <input type="text" placeholder="Zoek naar planten..." class="search-bar">
    <div class="nav-items">
        <a href="profile.php" class="icon profile-icon" aria-label="Profiel">
            <i class="fas fa-user"></i>
        </a>
        <?php if (isset($_SESSION['user']['currency'])): ?>
            <div class="currency">
                <i class="fas fa-coins currency"></i>
                <span class="display-currency"><?php echo htmlspecialchars($_SESSION['user']['currency']); ?></span>
            </div>
        <?php endif; ?>
        <a href="basket-page.php" class="icon basket-icon" aria-label="Winkelmand">
            <i class="fas fa-shopping-basket"></i>
            <?php if ($totalItems > 0): ?>
                <span class="basket-count"><?php echo $totalItems; ?></span>
            <?php endif; ?>
        </a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
            <a href="admin-dash.php" class="icon admin-icon" aria-label="Admin Dashboard">
                <i class="fas fa-tools"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>
