<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Plantwerp</title>
</head>

<body>
    <nav>
        <a href="index.html"><img class="logo" src="images/logo-plantwerp.png" alt="Plantwerp Logo"></a>
        <div class="nav-items">
            <input type="text" placeholder=" Search for plants..." class="search-bar">
            <a href="profile.php" class="icon profile-icon" aria-label="Profile">
                <i class="fas fa-user"></i> 
            </a>
            <a href="#" class="icon basket-icon" aria-label="Basket">
                <i class="fas fa-shopping-basket"></i> 
            </a>
        </div>
    </nav>

    <h1>Plantwerp</h1>
    <div class="hero">
    </div>
    <h2>Categories</h2>
    <section class="category-section">
        <div class="categories-wrapper">
    
            <button class="scroll-btn left-btn">
                &#8592; 
            </button>

            <div class="categories">
                <a href="#vetplant" class="category-card">
                    <img src="images/vetplant.png" alt="Succulents">
                    <p>Vetplanten</p>
                </a>
                <a href="#cactus" class="category-card">
                    <img src="images/cactus.png" alt="Cactus">
                    <p>Cactussen</p>
                </a>
                <a href="#hangplant" class="category-card">
                    <img src="images/hangplant.png" alt="Hangplant">
                    <p>Hangplanten</p>
                </a>
                <a href="#bloeiplant" class="category-card">
                    <img src="images/bloeiplant.png" alt="Bloeiplant">
                    <p>Bloeiplanten</p>
                </a>
                <a href="#groeneplant" class="category-card">
                    <img src="images/groenekamerplant.png" alt="Groene plant">
                    <p>Groene planten</p>
                </a>
            </div>

   
            <button class="scroll-btn right-btn">
                &#8594; 
            </button>
        </div>
    </section>

    <section class="products-section">
        <h2>Products</h2>
        <div class="products">
            <a href="product.php?id=1" class="product-card">
                <img src="images/groenekamerplant.png" alt="Monstera Plant">
                <p>Monstera Deliciosa</p>
                <p>$29.99</p>
            </a>
            <a href="product.php?id=2" class="product-card">
                <img src="images/groenekamerplant.png" alt="Monstera Plant">
                <p>Monstera Deliciosa</p>
                <p>$29.99</p>
            </a>
            <a href="product.php?id=3" class="product-card">
                <img src="images/groenekamerplant.png" alt="Monstera Plant">
                <p>Monstera Deliciosa</p>
                <p>$29.99</p>
            </a>
        </div>
    </section>


    <script src="script.js" defer></script>
</body>

</html>