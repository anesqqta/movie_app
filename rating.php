<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
        header('location:login.php');
    }

    //отримати id фільму з таблиці бронювання
    if (isset($_GET['get_id']) && !empty($_GET['get_id'])) {
        $booking_id = $_GET['get_id'];

        $select_booking = $conn->prepare("SELECT movie_id FROM booking WHERE id = ?");
        $select_booking->execute([$booking_id]);

        if ($select_booking->rowCount() > 0) {
            $fetch_booking = $select_booking->fetch(PDO::FETCH_ASSOC);
            $movie_id = $fetch_booking['movie_id'];
        }else{
            $warning_msg[] = 'Бронювання не знайдено';
        }
    }else{
        header('location:my_booking.php');
        exit();
    }

    //отримати фільм
    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        while($fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC)) {
            $fetch_img = $fetch_movie['thumbnail'];
            $movie_name = $fetch_movie['title'];
            $movie_duration = $fetch_movie['duration'];
            $release_year = $fetch_movie['release_year'];
            $trailer_url = $fetch_movie['trailer_url'];
        }
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1">
        <!-- посилання на іконки  -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Додати відгук</h1>
            <p>Поділіться своїми враженнями про перегляд фільму, якість сервісу та допоможіть іншим користувачам зробити правильний вибір</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Додати відгук</span>
        </div>
    </div>

    <!-- секція відгуку -->
    <div class="review" style="padding: 5% 0;">
        <div class="heading">
            <h1>Опублікуйте свій відгук</h1>
            <p>Поділіться своїми враженнями про фільм, сервіс бронювання та якість обслуговування — ваша думка важлива для нас</p>
        </div>
        <div class="img-box">
            <div class="img">
                <img src="uploaded_files/<?= $fetch_img; ?>">
            </div>
            <div>
                <p>Назва фільму : <span><?= $movie_name; ?></span></p>
                <p>Тривалість : <span><?= $movie_duration; ?></span></p>
                <p>Рік випуску : <span><?= $release_year; ?></span></p>
            </div>
        </div>
    </div>
    

    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            <?php include 'js/user_script.js'; ?>
        </script>

        <?php include 'components/alert.php'; ?>
    </body>
</html>