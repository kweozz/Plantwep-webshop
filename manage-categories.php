<?php
session_start();

if ($_SESSION['role'] !== 1) {
    echo '<h1 style="text-align: center; padding:10%; color:red; font-family:Helvetica;">' . htmlspecialchars('You do not have access to this page') . '</h1>';
    header("refresh:3;url=login.php");
    exit();
}

include_once __DIR__ . '/classes/Db.php';
include_once __DIR__ . '/classes/Category.php';

// Fetch categories from the database
$categories = Category::getAll();

// Delete Category
if (isset($_POST['delete_category'])) {
    $categoryId = htmlspecialchars(trim($_POST['category_id']), ENT_QUOTES, 'UTF-8');
    try {
        $category = new Category();
        $category->setId($categoryId);
        if ($category->delete($categoryId)) {
            $deleteSuccessMessage = 'Category successfully deleted!';
        } else {
            $deleteErrorMessage = 'Failed to delete category.';
        }
    } catch (Exception $e) {
        $deleteErrorMessage = 'Error: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Delete Category</title>
</head>

<body>
    <nav>
        <a href="index.php"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <input type="text" placeholder="Search categories..." class="search-bar">
    </nav>

    <h1>Manage Categories</h1>

    <?php if (isset($deleteSuccessMessage)): ?>
        <div class="message success"><?= htmlspecialchars($deleteSuccessMessage); ?></div>
    <?php endif; ?>
    <?php if (isset($deleteErrorMessage)): ?>
        <div class="message error"><?= htmlspecialchars($deleteErrorMessage); ?></div>
    <?php endif; ?>

    <div class="categories">
        <?php foreach ($categories as $category): ?>
            <div class="category-card manage-card">
                <form class="delete-form" action="" method="POST" style="display:inline;">
                    <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['id']); ?>">
                    <button class="delete-btn" type="submit" name="delete_category">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </form>
                <img src="<?= htmlspecialchars($category['image']); ?>" alt="<?= htmlspecialchars($category['name']); ?>">
                <h4><?= htmlspecialchars($category['name']); ?></h4>

                <a href="edit-category.php?id=<?= $category['id']; ?>" class="btn btn-edit">
                    <i class="fa fa-edit"></i> Bewerken
                </a>
            </div>
        <?php endforeach; ?>
    </div>


    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!confirm('Are you sure you want to delete this category?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>