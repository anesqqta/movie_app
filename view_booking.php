<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
        header('location:login.php');
    }

    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];
    }else{
        $get_id = '';
        header('location:my_booking.php');
    }

    if (isset($_POST['canceled'])) {
        $update_booking = $conn->prepare("UPDATE booking SET status = ? WHERE id = ? LIMIT 1");
        $update_booking->execute(['скасовано', $get_id]);
        header('location:my_booking.php');
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
            <h1>Деталі бронювання</h1>
            <p>Перегляньте повну інформацію про ваше бронювання, обраний фільм, сеанс та заброньовані місця</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Деталі бронювання</span>
        </div>
    </div>

    <!-- секція деталі бронювання -->
    <div class="view-booking">
        <div class="heading">
            <h1>Деталі бронювання</h1>
        </div>
        <div class="box-container">
            <?php
                $select_booking = $conn->prepare("SELECT * FROM booking WHERE id = ? LIMIT 1");
                $select_booking->execute([$get_id]);

                if ($select_booking->rowCount() > 0) {
                    while($fetch_booking = $select_booking->fetch(PDO::FETCH_ASSOC)){
                        $show_id = $fetch_booking['show_id'];

                        //отримати деталі
                        $show_stmt = $conn->prepare("SELECT * FROM shows WHERE id = ?");
                        $show_stmt->execute([$show_id]);

                        if ($show_stmt->rowCount() > 0) {
                            while($fetch_show = $show_stmt->fetch(PDO::FETCH_ASSOC)){
                                $hall_id = $fetch_show['hail_id'];
                                
                                $select_hall = $conn->prepare("SELECT * FROM halls WHERE id = ?");
                                $select_hall->execute([$hall_id]);

                                if ($select_hall->rowCount() > 0) {
                                    while($fetch_hall = $select_hall->fetch(PDO::FETCH_ASSOC)){
                                        $hall_name = $fetch_hall['name'];
                                        $hall_location = $fetch_hall['location'];
                                        $hall_city = $fetch_hall['city'];
                                    }
                                }
                            }
                        }
                    $select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                    $select_movie->execute([$fetch_booking['movie_id']]);

                    if ($select_movie->rowCount() > 0) {
                        while($fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC)){
                            $fetch_img = $fetch_movie['poster'];
                            $movie_name = $fetch_movie['title'];
                            $movie_duration = $fetch_movie['duration'];
                            $release_year = $fetch_movie['release_year'];
                            $trailer_url = $fetch_movie['trailer_url'];
                        }
                    }
            ?>
            <div class="box">
                <img src="uploaded_files/<?= $fetch_img; ?>">
                <div class="head">
                    <div class="title">Назва фільму : <span><?= $movie_name; ?></span></div>
                    <div class="title">Тривалість : <span><?= $movie_duration; ?></span></div>
                    <div class="title">Рік випуску : <span><?= $release_year; ?></span></div>
                    <a href="download_ticket.php?booking_id=<? $fetch_booking['id']; ?>"><i class="bx bx-download"></i></a>
                    <a href="<?= $trailer_url ?>"><img src="image/play-button.png" class="img"></a>
                </div>
                <div class="booking-summary">
                    <h3>Сумарне бронювання</h3>
                    <div class="detail">
                        <p>Мова : <span><?= $fetch_booking['language']; ?></span></p>
                        <p>формат : <span><?= $fetch_booking['formate']; ?></span></p>
                        <p>Назва залу : <span><?= $hall_name; ?></span></p>
                        <p>Розташування : <span><?= $hall_location; ?></span></p>
                        <p>Місто : <span><?= $hall_city; ?></span></p>
                        <p>Загальна кількість місць : <span><?= $fetch_booking['total_seat']; ?></span></p>
                        <p>Місця : <span><?= $fetch_booking['seat_details']; ?></span></p>
                        <p>Загальна сума : <span><?= $fetch_booking['amount']; ?></span></p>
                        <p>Дата : <span><?= $fetch_booking['date']; ?></span></p>
                        <p>Час : <span><?= $fetch_booking['time']; ?></span></p>
                        <p>Статус оплати : <span><?= $fetch_booking['payment_status']; ?></span></p>
                        <p>Статус бронювання : <span style="color: <?php if($fetch_booking['status'] == 'підтверджено'){echo "#31d7a9";}else{echo "red";} ?>"><?= $fetch_booking['status']; ?></span></p>
                    </div>
                </div>
                <?php if($fetch_booking['status'] == 'скасовано') {?>
                    <div class="flex-btn">
                        <a href="fetch_movie.php?get_id=<?= $fetch_booking['id']; ?>" class="btn">Забронювати знову</a>
                        <a href="my_booking.php?post_id=<?= $fetch_booking['id']; ?>"class="btn">Повернутись назад</a>
                        <a href="rating.php?get_id=<?= $fetch_movie['id'] ?>" class="btn">Отримати відгук</a>
                    </div>
                <?php }else{ ?>
                    <form action="" method="post" class="flex-btn">
                        <button type="submit" name="canceled" class="btn" onclick="return confirm('Ви хочете скасувати бронювання?')">Скасувати</button>
                        <a href="my_booking.php?post_id=<?= $fetch_booking['id']; ?>"class="btn">Повернутись назад</a>
                        <a href="rating.php?get_id=<?= $fetch_movie['id'] ?>" class="btn">Написати відгук</a>
                </form>
                <?php } ?>
            </div>
            <?php
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Бронювання ще не додано!</p>
                    </div>
                    ';
                }
            ?>
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