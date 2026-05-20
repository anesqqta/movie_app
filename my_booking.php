<?php
    include 'components/connect.php';

    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
        header('location:login.php');
        exit();
    }

    if (isset($_POST['cancel_booking'])) {

        $booking_id = $_POST['booking_id'];
        $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);

        $check_booking = $conn->prepare(
            "SELECT * FROM booking 
            WHERE id = ? AND user_id = ? AND status = ?"
        );
        $check_booking->execute([$booking_id, $user_id, 'очікує оплату']);

        if ($check_booking->rowCount() > 0) {

            $update_booking = $conn->prepare(
                "UPDATE booking 
                SET status = ?, payment_status = ? 
                WHERE id = ?"
            );
            $update_booking->execute(['скасовано', 'скасовано', $booking_id]);

            $success_msg[] = 'Бронювання скасовано';

        }else{
            $warning_msg[] = 'Це бронювання неможливо скасувати';
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
            <h1>Мої бронювання</h1>
            <p>Переглядайте свої активні та завершені бронювання, контролюйте квитки та плануйте наступні походи в кіно</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Мої бронювання</span>
        </div>
    </div>

    <div class="my-booking">
        <div class="heading">
            <h1>Мої бронювання</h1>
        </div>

        <div class="box-container">
            <?php 
                $select_booking = $conn->prepare("SELECT * FROM booking WHERE user_id = ? ORDER BY id DESC");
                $select_booking->execute([$user_id]);

                if ($select_booking->rowCount() > 0) {
                    while($fetch_booking = $select_booking->fetch(PDO::FETCH_ASSOC)){

                        $show_id = $fetch_booking['show_id'];

                        $select_show = $conn->prepare("SELECT movie_id FROM shows WHERE id = ?");
                        $select_show->execute([$show_id]);

                        $movie_id = $select_show->fetchColumn();

                        $fetch_img = '';
                        $movie_name = 'Фільм не знайдено';
                        $movie_duration = '';

                        $select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                        $select_movie->execute([$movie_id]);

                        if ($select_movie->rowCount() > 0) {
                            $fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC);
                            $fetch_img = $fetch_movie['thumbnail'];
                            $movie_name = $fetch_movie['title'];
                            $movie_duration = $fetch_movie['duration'];
                        }

                        $status_class = '';

                        if($fetch_booking['status'] == 'оплачено'){
                            $status_class = 'paid';
                        }elseif($fetch_booking['status'] == 'очікує оплату'){
                            $status_class = 'pending';
                        }elseif($fetch_booking['status'] == 'скасовано'){
                            $status_class = 'cancelled';
                        }elseif($fetch_booking['status'] == 'анульовано'){
                            $status_class = 'expired';
                        }
            ?>

            <div class="box">

                <a href="view_booking.php?get_id=<?= $fetch_booking['id']; ?>">
                    <img src="uploaded_files/<?= $fetch_img; ?>">
                </a>

                <?php if ($fetch_booking['status'] == 'оплачено') { ?>
                    <a href="download_ticket.php?booking_id=<?= $fetch_booking['id']; ?>" class="download">
                        <i class="bx bx-download"></i>
                    </a>
                <?php } ?>

                <div class="detail">
                    <div class="content">
                        <div>
                            <h2><?= $movie_name; ?></h2>
                            <p><?= $movie_duration; ?></p>
                        </div>
                        <div>
                            <p>Кількість : <?= $fetch_booking['total_seat']; ?></p>
                            <p>Місце : <?= $fetch_booking['seat_details']; ?></p>
                        </div>
                        <div>
                            <p>Сума : <?= $fetch_booking['amount']; ?> грн</p>
                            <p>Оплата : <?= $fetch_booking['payment_status']; ?></p>
                        </div>
                    </div>
                    <div class="booking-status-box">
                        <h2>Статус :</h2>
                        <p>
                            <span class="booking-status <?= $status_class; ?>">
                                <?= $fetch_booking['status']; ?>
                            </span>
                        </p>
                    </div>
                    <?php if ($fetch_booking['status'] == 'оплачено') { ?>
                        <a href="rating.php?get_id=<?= $fetch_booking['id']; ?>" class="btn">Дайте відгук</a>
                    <?php } ?>
                    <?php if ($fetch_booking['status'] == 'очікує оплату') { ?>
                        <form action="" method="post">
                            <input type="hidden" name="booking_id" value="<?= $fetch_booking['id']; ?>">
                            <button type="submit" name="cancel_booking" class="btn" onclick="return confirm('Скасувати бронювання?');">
                                Скасувати
                            </button>
                        </form>
                    <?php } ?>
                </div>
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