<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

//подключение
require_once '../Config/db_connect.php';

//анные о куриной грудке
$sql = "SELECT * FROM products WHERE product_name = 'Куриная грудка'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

//если есть изображение, меняю его в base64 для отображения
$productImage = '';
if (!empty($product['food_img'])) {
    $productImage = 'data:image/jpeg;base64,' . base64_encode($product['food_img']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Дашборд</title>
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Dashboard/dashboard.css">
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
    <my-header logged-in="true" 
               user-name="<?php echo htmlspecialchars($userName); ?>"
               user-email="<?php echo htmlspecialchars($userEmail); ?>">
    </my-header>
    
    <!-- сайдбар слева -->
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <h2 class="sidebar-title">Мой дневник</h2>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item active">
                    <div class="sidebar-icon"><!-- иконка --></div>
                    <span>Дневник</span>
                </a>
                <a href="statistics.php" class="sidebar-item">
                    <div class="sidebar-icon"><!-- иконка --></div>
                    <span>Статистика</span>
                </a>
                <a href="my-products.php" class="sidebar-item">
                    <div class="sidebar-icon"><!-- иконка --></div>
                    <span>Мои продукты</span>
                </a>
                <a href="profile.php" class="sidebar-item">
                    <div class="sidebar-icon"><!-- иконка --></div>
                    <span>Настройки</span>
                </a>
            </nav>
            
            <div class="quick-actions">
                <h3 class="quick-title">Быстрые действия</h3>
                <button class="quick-btn" id="addMealBtn">+ Добавить приём пищи</button>
            </div>
            
            <div class="sidebar-calendar">
                <div class="calendar-placeholder">Календарь</div>
            </div>
        </aside>
        
        <!-- контент на странице -->
        <main class="main-content">
            <div class="cards-row">
                <div class="stat-card">
                    <div class="stat-card-title">Статистика дня</div>
                    <div>
                        <span class="stat-number">1 845</span>
                        <span class="stat-unit">ккал</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 68%"></div>
                    </div>
                    <div class="macro-labels">
                        <span>Б 120г</span>
                        <span>Ж 55г</span>
                        <span>У 210г</span>
                    </div>
                </div>
                
                <div class="product-card">
                    <?php if ($productImage): ?>
                        <div class="product-image">
                            <img src="<?php echo $productImage; ?>" alt="<?php echo $product['product_name']; ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 0.75rem;">
                        </div>
                    <?php endif; ?>
                    <div class="product-title"><?php echo $product['product_name']; ?></div>
                    <div class="product-kbju">
                        <?php echo $product['calories']; ?> ккал | 
                        Б: <?php echo $product['protein']; ?>г | 
                        Ж: <?php echo $product['fat']; ?>г | 
                        У: <?php echo $product['carbs']; ?>г
                    </div>
                    <button class="product-btn">+ Добавить</button>
                </div>
            </div>
            
            <div class="diary-header">
                <h2 class="diary-title">Сегодняшний дневник</h2>
                <button class="add-product-btn">+ Добавить продукт</button>
            </div>
            
            <table class="food-table">
                <thead>
                    <tr>
                        <th>Продукт</th>
                        <th>Вес (г)</th>
                        <th>Кал</th>
                        <th>Б</th>
                        <th>Ж</th>
                        <th>У</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Овсянка</td>
                        <td>100</td>
                        <td>389</td>
                        <td>13</td>
                        <td>6.9</td>
                        <td>66</td>
                        <td class="delete-icon"><img src="../Images/Icons/trashicon.png" alt="удалить" style="width: 1.125rem; height: 1.125rem; cursor: pointer;"></td>
                    </tr>
                    <tr>
                        <td>Яблоко</td>
                        <td>200</td>
                        <td>104</td>
                        <td>0.4</td>
                        <td>0.4</td>
                        <td>22</td>
                        <td class="delete-icon"><img src="../Images/Icons/trashicon.png" alt="удалить" style="width: 1.125rem; height: 1.125rem; cursor: pointer;"></td>
                    </tr>
                    <tr>
                        <td>Гречка</td>
                        <td>150</td>
                        <td>165</td>
                        <td>6</td>
                        <td>3.5</td>
                        <td>28</td>
                        <td class="delete-icon"><img src="../Images/Icons/trashicon.png" alt="удалить" style="width: 1.125rem; height: 1.125rem; cursor: pointer;"></td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>ИТОГО</strong></td>
                        <td>—</td>
                        <td><strong>658</strong></td>
                        <td><strong>19.4</strong></td>
                        <td><strong>10.5</strong></td>
                        <td><strong>116</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>
    
    <my-footer></my-footer>
    
    <script>
        document.getElementById('addMealBtn')?.addEventListener('click', () => {
            alert('Добавление приёма пищи');
        });
        
        document.querySelector('.add-product-btn')?.addEventListener('click', () => {
            alert('Добавление продукта');
        });
        
        document.querySelectorAll('.delete-icon').forEach(icon => {
            icon.addEventListener('click', () => {
                alert('Удалить продукт');
            });
        });
    </script>
</body>
</html>