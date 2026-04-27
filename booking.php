<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    session_start();

    //спочатку отримати show_id з URL-адреси, повернутися до сеансу
    if (isset($_GET['show_id'])) {
        $show_id = $_GET['show_id'];
    }elseif(isset($_SESSION['booking']['show_id'])){
        $show_id = $_SESSION['booking']['show_id'];
    }else{
        die('Сеанс не вибрано');
    }

    //отримати значення сеансу
    $language = $_SESSION['booking']['language'];
    $formate = $_SESSION['booking']['formate'];
    $time = $_SESSION['booking']['time'];
    $date = $_SESSION['booking']['date'];
    $movie_id = $_SESSION['booking']['movie_id'];

    //отримати назву фільму
    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        while($fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC)){
            $fetch_img = $fetch_movie['thumbnail'];
            $movie_name = $fetch_movie['title'];
        }
    }
    
    //отримати деталі сеансу
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

    //отримати деталі місця
    $select_seat = $conn->prepare("SELECT * FROM seat_details WHERE user_id = ?");
    $select_seat->execute([$user_id]);

    if ($select_seat->rowCount() > 0) {
        while($fetch_seat = $select_seat->fetch(PDO::FETCH_ASSOC)){
            $seat_detail_id = $fetch_seat['id'];
            $total_seats = $fetch_seat['total_seat'];
            $seat_detail = $fetch_seat['selected_seats'];
            $total_price = $fetch_seat['amount'];
        }
    }

    //запит на бронювання
    if (isset($_POST['booking'])) {
        if ($user_id != '') {

            $id = unique_id();

            $payment_method = $_POST['payment_method'];
            $payment_method = filter_var($payment_method, FILTER_SANITIZE_STRING);

            $card_details = $_POST['card_details'];
            $card_details = filter_var($card_details, FILTER_SANITIZE_STRING);

            $card_name = $_POST['card_name'];
            $card_name = filter_var($card_name, FILTER_SANITIZE_STRING);

            $expiration = $_POST['expiration'];
            $expiration = filter_var($expiration, FILTER_SANITIZE_STRING);

            $cvv = $_POST['cvv'];
            $cvv = filter_var($cvv, FILTER_SANITIZE_STRING);

            $insert_booking = $conn->prepare("INSERT INTO booking (id, user_id, show_id, movie_id, language, formate, date, time, seat_detail_id, total_seat, seat_details, amount, payment_method, nameon_card, card_details, expiration, cvv) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_booking->execute([$id, $user_id, $show_id, $movie_id, $language, $formate, $date, $time, $seat_detail_id, $total_seats, $seat_detail, $total_price, $payment_method, $card_name, $card_details, $expiration, $cvv]);

            header('location:my_booking.php');
        }else{
            $warning_msg[] = 'Увійдіть спочатку';
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
            <h1>Бронювання</h1>
            <p>Завершіть оформлення бронювання квитків та підготуйтеся до незабутнього перегляду улюбленого фільму</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Бронювання</span>
        </div>
    </div>

    <!-- секція бронювання -->
    <div class="booking-movie-detail">
        <img src="uploaded_files/<?= $fetch_img; ?>">
        <p>Назва фільму : <span><?= $movie_name; ?></span></p>
    </div>
    <div class="booking-summary">
        <h3>сумарне бронювання</h3>
        <div class="detail">
            <p>Мова : <span><?= $language; ?></span></p>
            <p>Формат : <span><?= $formate; ?></span></p>
            <p>Дата : <span><?= $date; ?></span></p>
            <p>Час : <span><?= $time; ?></span></p>
            <p>Назва залу : <span><?= $hall_name; ?></span></p>
            <p>Розташування : <span><?= $hall_location; ?></span></p>
            <p>Місто : <span><?= $hall_city; ?></span></p>
            <p>Загальна кількість місць : <span><?= $total_seats; ?></span></p>
            <p>Деталі місця : <span><?= $seat_detail; ?></span></p>
            <p>Загальна сума : <span>$<?= $total_price; ?>/-</span></p>
        </div>
    </div>
    <div class="booking form-container">
        <h3>Введіть дані вашої картки</h3>
        <form action="" method="post" class="register">
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Варіант оплати <span>*</span></p>
                        <select name="payment_method" class="box" required>
                            <option selected disabled>Виберіть метод оплати</option>
                            <option value="credit card">Кредитна картка</option>
                            <option value="debit card">Дебетова картка</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div class="input-field">
                        <p>реквізити картки <span>*</span></p>
                        <input type="number" name="card_details" class="box" required>
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>Ім'я картки <span>*</span></p>
                        <input type="text" name="card_name" class="box" required>
                    </div>
                    <div class="input-field">
                        <p>закінчення терміну дії <span>*</span></p>
                        <input type="date" name="expiration" min="<?php echo date('Y-m-d') ?>" class="box" required>
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>cvv <span>*</span></p>
                <input type="text" name="cvv" class="box" required>
            </div>
            <button type="submit" name="booking" class="btn">Оплатити</button>
        </form>
    </div>
    

    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            <?php include 'js/user_script.js'; ?>
        </script>


        <?php include 'components/alert.php'; ?>
    </body>
</html>