<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    $select_movies = $conn->prepare("SELECT * FROM movies");
    $select_movies->execute();
    $total_movies = $select_movies->rowCount();

    $select_message = $conn->prepare("SELECT * FROM message");
    $select_message->execute();
    $total_message = $select_message->rowCount();

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
            <h1>Профіль</h1>
            <p>Переглядайте та редагуйте інформацію адміністратора, керуйте доступом і налаштуваннями облікового запису</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Профіль</span>
        </div>
    </div>

    <!-- секція профілю-->
     <section class="profile">
        <div class="img-box">
            <img src="../uploaded_files/<?= $fetch_profile['image']; ?>">
            <h3><?= $fetch_profile['name']; ?></h3>
            <a href="update.php" class="btn">Оновити профіль</a>
        </div>
        <div class="details">
            <div>
                <img src="../image/a-icon3.png">
                <p>Ваше ім'я : <span><?= $fetch_profile['name']; ?></span></p>
            </div>
            <div>
                <img src="../image/p-icon.png">
                <p>Ваш email : <span><?= $fetch_profile['email']; ?></span></p>
            </div>
            <div>
                <img src="../image/p-icon0.png">
                <p>Ваше номер : <span>+380986708240</span></p>
            </div>
            <div>
                <img src="../image/p-icon2.png">
                <p>Фільми : <span><?= $total_movies; ?></span></p>
            </div>
            <div>
                <img src="../image/a-icon1.png">
                <p>Відправлені повідомлення : <span><?= $total_message; ?></span></p>
            </div>
            <div>
                <img src="../image/p-icon3.png">
                <p>Ваш пароль : <span>****</span></p>
            </div>
        </div>
    </section>
    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>