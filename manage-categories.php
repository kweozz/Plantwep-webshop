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
            $deleteSuccessMessage = 'Categorie successvol verwijderd!';
        } else {
            $deleteErrorMessage = 'Categorie kon niet worden verwijderd.';
        }
    } catch (Exception $e) {
        $deleteErrorMessage = 'Error: ' . $e->getMessage();
    }
}


// Retrieve messages from session
if (isset($_SESSION['categorySuccessMessage'])) {
    $categorySuccessMessage = $_SESSION['categorySuccessMessage'];
    unset($_SESSION['categorySuccessMessage']);
}

if (isset($_SESSION['categoryErrorMessage'])) {
    $categoryErrorMessage = $_SESSION['categoryErrorMessage'];
    unset($_SESSION['categoryErrorMessage']);
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
        <?php include 'classes/Nav.php'; ?>
    </nav>
    <section class="manage-categories">
        <div class="back">
            <a class="back-icon" href="admin-dash.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>
            <h2>Beheer categorieën</h2>
        </div>

        <?php if (isset($deleteSuccessMessage)): ?>
            <div class="alert-success"><?= htmlspecialchars($deleteSuccessMessage); ?></div>
        <?php endif; ?>
        <?php if (isset($deleteErrorMessage)): ?>
            <div class="alert-danger"><?= htmlspecialchars($deleteErrorMessage); ?></div>
        <?php endif; ?>

        <div class="categories">
            <?php foreach ($categories as $category): ?>
                <div class="category-card ">
                    <form class="delete-form" action="" method="POST" style="display:inline;">
                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['id']); ?>">
                        <button class="delete-btn" type="submit" name="delete_category">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </form>
                    <img src="<?= htmlspecialchars($category['image']); ?>"
                        alt="<?= htmlspecialchars($category['name']); ?>">
                    <h4><?= htmlspecialchars($category['name']); ?></h4>

                    <a href="edit-category.php?id=<?= $category['id']; ?>" class="btn btn-edit">
                        <i class="fa fa-edit"></i> Bewerken
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </section>
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!confirm('Ben je zeker dat je deze categorie wilt verwijderen?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>