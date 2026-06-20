<?php
    include 'components/connect.php';
    include 'components/auto_cancel.php';

    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
        header('location:login.php');
        exit();
    }

    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];
        $get_id = filter_var($get_id, FILTER_SANITIZE_STRING);
    }else{
        header('location:my_booking.php');
        exit();
    }

    if (isset($_POST['canceled'])) {
        $check_booking = $conn->prepare("SELECT * FROM booking WHERE id = ? AND user_id = ? AND status = ?");
        $check_booking->execute([$get_id, $user_id, 'очікує оплату']);

        if ($check_booking->rowCount() > 0) {
            $update_booking = $conn->prepare("UPDATE booking SET status = ?, payment_status = ? WHERE id = ?");
            $update_booking->execute(['скасовано', 'скасовано', $get_id]);

            $success_msg[] = 'Бронювання скасовано';
        }else{
            $warning_msg[] = 'Це бронювання неможливо скасувати';
        }
    }

    $select_booking = $conn->prepare("SELECT * FROM booking WHERE id = ? AND user_id = ? LIMIT 1");
    $select_booking->execute([$get_id, $user_id]);

    if ($select_booking->rowCount() > 0) {
        $fetch_booking = $select_booking->fetch(PDO::FETCH_ASSOC);

        $show_id = $fetch_booking['show_id'];
        $movie_id = $fetch_booking['movie_id'];

        $hall_name = 'Не вказано';
        $hall_location = 'Не вказано';
        $hall_city = 'Не вказано';

        $show_stmt = $conn->prepare("SELECT shows.*, halls.name, halls.location, halls.city FROM shows LEFT JOIN halls ON shows.hall_id = halls.id WHERE shows.id = ?");
        $show_stmt->execute([$show_id]);

        if ($show_stmt->rowCount() > 0) {
            $fetch_show = $show_stmt->fetch(PDO::FETCH_ASSOC);
            $hall_name = $fetch_show['name'];
            $hall_location = $fetch_show['location'];
            $hall_city = $fetch_show['city'];
        }

        $fetch_img = '';
        $movie_name = 'Фільм не знайдено';
        $movie_duration = '';
        $release_year = '';
        $trailer_url = '#';

        $select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
        $select_movie->execute([$movie_id]);

        if ($select_movie->rowCount() > 0) {
            $fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC);
            $fetch_img = $fetch_movie['poster'];
            $movie_name = $fetch_movie['title'];
            $movie_duration = $fetch_movie['duration'];
            $release_year = $fetch_movie['release_year'];
            $trailer_url = $fetch_movie['trailer_url'];
        }

        $select_bar_total = $conn->prepare("SELECT SUM(total_price) FROM booking_bar_items WHERE booking_id = ?");
        $select_bar_total->execute([$get_id]);

        $bar_total = $select_bar_total->fetchColumn();

        if ($bar_total == '') {
            $bar_total = 0;
        }

        $final_total = $fetch_booking['amount'] + $bar_total;

        $status_color = '#dbe2fb';

        if ($fetch_booking['status'] == 'оплачено') {
            $status_color = '#31d7a9';
        }elseif ($fetch_booking['status'] == 'очікує оплату') {
            $status_color = '#ffd166';
        }elseif ($fetch_booking['status'] == 'скасовано' || $fetch_booking['status'] == 'анульовано') {
            $status_color = '#ff5c5c';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
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
    <div class="view-booking">
        <div class="heading">
            <h1>Деталі бронювання</h1>
        </div>
        <div class="box-container">
            <?php if ($select_booking->rowCount() > 0) { ?>
            <div class="box">
                <img src="uploaded_files/<?= $fetch_img; ?>">
                <div class="head">
                    <div class="title">Назва фільму : <span><?= $movie_name; ?></span></div>
                    <div class="title">Тривалість : <span><?= $movie_duration; ?></span></div>
                    <div class="title">Рік випуску : <span><?= $release_year; ?></span></div>
                    <?php if ($fetch_booking['status'] == 'оплачено') { ?>
                        <a href="download_ticket.php?booking_id=<?= $fetch_booking['id']; ?>">
                            <i class="bx bx-download"></i>
                        </a>
                    <?php } ?>
                    <a href="<?= $trailer_url; ?>">
                        <img src="image/play-button.png" class="img">
                    </a>
                </div>
                <div class="booking-summary">
                    <h3>Сумарне бронювання</h3>
                    <div class="detail">
                        <p>Мова : <span><?= $fetch_booking['language']; ?></span></p>
                        <p>Формат : <span><?= $fetch_booking['formate']; ?></span></p>
                        <p>Назва залу : <span><?= $hall_name; ?></span></p>
                        <p>Розташування : <span><?= $hall_location; ?></span></p>
                        <p>Місто : <span><?= $hall_city; ?></span></p>
                        <p>Кількість місць : <span><?= $fetch_booking['total_seat']; ?></span></p>
                        <p>Місця : <span><?= $fetch_booking['seat_details']; ?></span></p>
                        <p>Квитки : <span><?= $fetch_booking['amount']; ?> грн</span></p>
                        <p>Кінобар : <span><?= $bar_total; ?> грн</span></p>
                        <p>Разом : <span><?= $final_total; ?> грн</span></p>
                        <p>Дата : <span><?= $fetch_booking['date']; ?></span></p>
                        <p>Час : <span><?= $fetch_booking['time']; ?></span></p>
                        <p>Статус оплати : <span><?= $fetch_booking['payment_status']; ?></span></p>
                        <p>Статус бронювання : <span style="color: <?= $status_color; ?>"><?= $fetch_booking['status']; ?></span></p>
                    </div>
                </div>

                <?php if ($fetch_booking['payment_status'] == 'оплачено' && !empty($fetch_booking['ticket_code'])) { ?>

                <?php 
                    $verify_link = 'https://localhost/movie_app/verify_ticket.php?code=' . $fetch_booking['ticket_code'];
                    $qr_link = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($verify_link);
                ?>

                <div class="qr-ticket-box">
                    <h3>QR-код квитка</h3>
                    <img src="<?= $qr_link; ?>" alt="QR-код квитка">
                    <p>Покажіть цей QR-код при вході до залу</p>
                </div>

            <?php } else { ?>

                <div class="qr-ticket-box">
                    <h3>QR-код недоступний</h3>
                    <p>Квиток буде доступний після оплати бронювання.</p>
                </div>

            <?php } ?>

                <div class="flex-btn">
                    <?php if($fetch_booking['status'] == 'очікує оплату') { ?>
                        <form action="" method="post">
                            <button type="submit" name="canceled" class="btn" onclick="return confirm('Ви хочете скасувати бронювання?')">
                                Скасувати
                            </button>
                        </form>
                    <?php } ?>
                    <a href="my_booking.php" class="btn">Повернутись назад</a>
                    <?php if($fetch_booking['status'] == 'оплачено') { ?>
                        <a href="rating.php?get_id=<?= $fetch_booking['id']; ?>" class="btn">Написати відгук</a>
                    <?php } ?>
                </div>
            </div>
            <?php }else{ ?>
                <div class="empty">
                    <p>Бронювання не знайдено!</p>
                </div>
            <?php } ?>
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