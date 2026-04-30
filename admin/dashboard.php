<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1">
        <!-- посилання на іконки  -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Панель керування</h1>
            <p>Керуйте каталогом фільмів, розкладом сеансів, бронюваннями користувачів та основними налаштуваннями системи</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Панель керування</span>
        </div>
    </div>

    <!-- секція панель керування-->
    <div class="dashboard">
        <div class="heading">
            <span>Моя панель керування</span>
            <h1>Панель керування</h1>
        </div>
        <div class="box-container">
            <div class="box">
                <?php
                    $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
                    $select_profile->execute([$admin_id]);
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <h3>Вітаємо</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href="update.php" class="btn">Оновити профіль</a>
            </div>
            <div class="box">
                <?php
                    $total_income = $conn->query("SELECT SUM(amount) FROM booking")->fetchColumn();
                    if (!$total_income) {
                        $total_income = 0;
                    }
                ?>
                <h3>$ <?php echo number_format($total_income); ?></h3>
                <p>Загальний дохід</p>
                <div class="btn">
                    <i class="bx bx-dollar"></i>Гривень
                </div>
            </div>
            <div class="box">
                <?php
                    $select_users = $conn->prepare("SELECT * FROM users");
                    $select_users->execute();
                    $num_of_users = $select_users->rowCount();
                ?>
                <h3><?= $num_of_users ?></h3>
                <p>Зареєстровано користувачів</p>
                <a href="user_account.php" class="btn">Переглянути користувачів</a>
            </div>
            <div class="box">
                <?php
                    $select_bookings = $conn->prepare("SELECT * FROM booking");
                    $select_bookings->execute();
                    $total_bookings = $select_bookings->rowCount();
                ?>
                <h3><?= $total_bookings ?></h3>
                <p>Загальна кількість бронювань</p>
                <a href="admin_bookings.php" class="btn">Переглянути бронювання</a>
            </div>
            
            <div class="box">
                <?php
                    $select_movies = $conn->prepare("SELECT * FROM movies");
                    $select_movies->execute();
                    $total_movies = $select_movies->rowCount();
                ?>
                <h3><?= $total_movies ?></h3>
                <p>Загальна кількість фільмів</p>
                <a href="view_movies.php" class="btn">Переглянути фільми</a>
            </div>
            <div class="box">
                <?php
                    $select_reviews = $conn->prepare("SELECT * FROM reviews");
                    $select_reviews->execute();
                    $total_reviews = $select_reviews->rowCount();
                ?>
                <h3><?= $total_reviews ?></h3>
                <p>Загальна кількість відгуків</p>
                <a href="comments.php" class="btn">Переглянути відгуки</a>
            </div>
        </div>
    </div>

    

            

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>