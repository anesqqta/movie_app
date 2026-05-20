<?php
    include '../components/connect.php';
    include '../components/auto_cancel.php';

    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
        exit();
    }

    $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $select_profile->execute([$admin_id]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

    $total_movies = $conn->prepare("SELECT COUNT(*) FROM movies");
    $total_movies->execute();
    $count_movies = $total_movies->fetchColumn();

    $total_users = $conn->prepare("SELECT COUNT(*) FROM users");
    $total_users->execute();
    $count_users = $total_users->fetchColumn();

    $total_shows = $conn->prepare("SELECT COUNT(*) FROM shows");
    $total_shows->execute();
    $count_shows = $total_shows->fetchColumn();

    $total_reviews = $conn->prepare("SELECT COUNT(*) FROM reviews");
    $total_reviews->execute();
    $count_reviews = $total_reviews->fetchColumn();

    $total_bookings = $conn->prepare("SELECT COUNT(*) FROM booking");
    $total_bookings->execute();
    $count_bookings = $total_bookings->fetchColumn();

    $paid_bookings = $conn->prepare("SELECT COUNT(*) FROM booking WHERE status = ?");
    $paid_bookings->execute(['оплачено']);
    $count_paid = $paid_bookings->fetchColumn();

    $pending_bookings = $conn->prepare("SELECT COUNT(*) FROM booking WHERE status = ?");
    $pending_bookings->execute(['очікує оплату']);
    $count_pending = $pending_bookings->fetchColumn();

    $cancelled_bookings = $conn->prepare("SELECT COUNT(*) FROM booking WHERE status = ?");
    $cancelled_bookings->execute(['скасовано']);
    $count_cancelled = $cancelled_bookings->fetchColumn();

    $expired_bookings = $conn->prepare("SELECT COUNT(*) FROM booking WHERE status = ?");
    $expired_bookings->execute(['анульовано']);
    $count_expired = $expired_bookings->fetchColumn();

    $total_ticket_income = $conn->prepare("SELECT SUM(amount) FROM booking WHERE status = ?");
    $total_ticket_income->execute(['оплачено']);
    $ticket_income = $total_ticket_income->fetchColumn();

    if ($ticket_income == '') {
        $ticket_income = 0;
    }

    $total_bar_income = $conn->prepare("
        SELECT SUM(booking_bar_items.total_price)
        FROM booking_bar_items
        JOIN booking ON booking_bar_items.booking_id = booking.id
        WHERE booking.status = ?
    ");
    $total_bar_income->execute(['оплачено']);
    $bar_income = $total_bar_income->fetchColumn();

    if ($bar_income == '') {
        $bar_income = 0;
    }

    $total_income = $ticket_income + $bar_income;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1">
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

    <div class="dashboard">
        <div class="heading">
            <span>Моя панель керування</span>
            <h1>Панель керування</h1>
        </div>

        <div class="box-container">

            <div class="box">
                <i class="bx bx-user-circle"></i>
                <h3>Вітаємо</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href="update.php" class="btn">Оновити профіль</a>
            </div>

            <div class="box income-box">
                <i class="bx bx-wallet"></i>
                <h3><?= $total_income; ?> грн</h3>
                <p>Загальний дохід</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-calendar-check"></i>
                <h3><?= $count_bookings; ?></h3>
                <p>Бронювань</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-movie-play"></i>
                <h3><?= $count_movies; ?></h3>
                <p>Фільмів</p>
                <a href="view_movie.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-time-five"></i>
                <h3><?= $count_shows; ?></h3>
                <p>Сеансів</p>
                <a href="view_shows.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-message-dots"></i>
                <h3><?= $count_reviews; ?></h3>
                <p>Відгуків</p>
                <a href="comments.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-check-circle"></i>
                <h3><?= $count_paid; ?></h3>
                <p>Оплачених</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-hourglass"></i>
                <h3><?= $count_pending; ?></h3>
                <p>Очікують оплату</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-x-circle"></i>
                <h3><?= $count_cancelled; ?></h3>
                <p>Скасованих</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-error-circle"></i>
                <h3><?= $count_expired; ?></h3>
                <p>Анульованих</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-money"></i>
                <h3><?= $ticket_income; ?> грн</h3>
                <p>Дохід з квитків</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
            </div>

            <div class="box">
                <i class="bx bx-coffee"></i>
                <h3><?= $bar_income; ?> грн</h3>
                <p>Дохід з кінобару</p>
                <a href="view_booking.php" class="btn">Переглянути</a>
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