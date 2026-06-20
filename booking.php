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

    session_start();

    if (isset($_GET['show_id'])) {
        $show_id = $_GET['show_id'];
        $_SESSION['booking']['show_id'] = $show_id;
    }elseif(isset($_SESSION['booking']['show_id'])){
        $show_id = $_SESSION['booking']['show_id'];
    }else{
        die('Сеанс не вибрано');
    }

    $language = $_SESSION['booking']['language'];
    $formate = $_SESSION['booking']['formate'];
    $time = $_SESSION['booking']['time'];
    $date = $_SESSION['booking']['date'];
    $movie_id = $_SESSION['booking']['movie_id'];

    $movie_name = '';
    $fetch_img = '';

    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        $fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC);
        $fetch_img = $fetch_movie['thumbnail'];
        $movie_name = $fetch_movie['title'];
    }

    $hall_name = '';
    $hall_location = '';
    $hall_city = '';

    $show_stmt = $conn->prepare("SELECT * FROM shows WHERE id = ?");
    $show_stmt->execute([$show_id]);

    if ($show_stmt->rowCount() > 0) {
        $fetch_show = $show_stmt->fetch(PDO::FETCH_ASSOC);
        $hall_id = $fetch_show['hall_id'];

        $select_hall = $conn->prepare("SELECT * FROM halls WHERE id = ?");
        $select_hall->execute([$hall_id]);

        if ($select_hall->rowCount() > 0) {
            $fetch_hall = $select_hall->fetch(PDO::FETCH_ASSOC);
            $hall_name = $fetch_hall['name'];
            $hall_location = $fetch_hall['location'];
            $hall_city = $fetch_hall['city'];
        }
    }

    $seat_detail_id = '';
    $total_seats = 0;
    $seat_detail = '';
    $total_price = 0;

    $select_seat = $conn->prepare(
        "SELECT * FROM seat_details 
        WHERE user_id = ? AND show_id = ? 
        ORDER BY id DESC LIMIT 1"
    );
    $select_seat->execute([$user_id, $show_id]);

    if ($select_seat->rowCount() > 0) {
        $fetch_seat = $select_seat->fetch(PDO::FETCH_ASSOC);
        $seat_detail_id = $fetch_seat['id'];
        $total_seats = $fetch_seat['total_seat'];
        $seat_detail = $fetch_seat['selected_seats'];
        $total_price = $fetch_seat['amount'];
    }

    if (isset($_POST['booking'])) {
        if ($user_id != '') {

            $payment_method = $_POST['payment_method'];
            $payment_method = filter_var($payment_method, FILTER_SANITIZE_STRING);

            if ($payment_method == 'Онлайн оплата') {

                $card_details = $_POST['card_details'];
                $card_details = filter_var($card_details, FILTER_SANITIZE_STRING);

                $card_name = $_POST['card_name'];
                $card_name = filter_var($card_name, FILTER_SANITIZE_STRING);

                $expiration = $_POST['expiration'];
                $expiration = filter_var($expiration, FILTER_SANITIZE_STRING);

                $cvv = $_POST['cvv'];
                $cvv = filter_var($cvv, FILTER_SANITIZE_STRING);

                $status = 'оплачено';
                $payment_status = 'оплачено';

                $ticket_code = 'TKT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));

            }else{

                $card_details = '';
                $card_name = '';
                $expiration = '';
                $cvv = '';

                $status = 'очікує оплату';
                $payment_status = 'не оплачено';

                $ticket_code = NULL;
            }

            $insert_booking = $conn->prepare("INSERT INTO booking (user_id, show_id, movie_id, seat_detail_id, language, formate, date, time, total_seat, seat_details, amount, status, payment_method, nameon_card, card_details, expiration, cvv, payment_status, ticket_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_booking->execute([$user_id, $show_id, $movie_id, $seat_detail_id, $language, $formate, $date, $time, $total_seats, $seat_detail, $total_price, $status, $payment_method, $card_name, $card_details, $expiration, $cvv, $payment_status, $ticket_code]);

            $booking_id = $conn->lastInsertId();

            header('location:cinema_bar.php?booking_id='.$booking_id);
            exit();

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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
    <title>BOLETO</title>
</head>
<body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Оформлення бронювання</h1>
            <p>Перевірте дані бронювання та оберіть спосіб оплати</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Бронювання</span>
        </div>
    </div>
    <div class="booking-movie-detail">
        <img src="uploaded_files/<?= $fetch_img; ?>">
        <p>Фільм : <span><?= $movie_name; ?></span></p>
    </div>
    <div class="booking-summary">
        <h3>Деталі бронювання</h3>
        <div class="detail">
            <p>Мова <br><span><?= $language; ?></span></p>
            <p>Формат <br><span><?= $formate; ?></span></p>
            <p>Дата <br><span><?= $date; ?></span></p>
            <p>Час <br><span><?= $time; ?></span></p>
            <p>Зал <br><span><?= $hall_name; ?></span></p>
            <p>Місто <br><span><?= $hall_city; ?></span></p>
            <p>Місця <br><span><?= $seat_detail; ?></span></p>
            <p>Кількість <br><span><?= $total_seats; ?></span></p>
            <p>Сума <br><span><?= $total_price; ?> грн</span></p>
        </div>
    </div>
    <div class="form-container booking">
        <form action="" method="post" style="max-width: 600px !important; width: 100% !important; margin: 0 auto !important;">
            <h3>Оплата бронювання</h3>

            <div class="input-field">
                <p>Спосіб оплати <span>*</span></p>
                <select name="payment_method" id="payment-method" class="box" required>
                    <option value="" selected disabled>Оберіть спосіб оплати</option>
                    <option value="Онлайн оплата">Онлайн оплата</option>
                    <option value="Оплата в касі">Оплата в касі</option>
                </select>
            </div>
            <div id="card-fields">
                <div class="input-field">
                    <p>Ім’я на картці <span>*</span></p>
                    <input type="text" name="card_name" class="box" placeholder="Введіть ім’я на картці">
                </div>
                <div class="input-field">
                    <p>Номер картки <span>*</span></p>
                    <input type="text" name="card_details" class="box" placeholder="0000 0000 0000 0000" maxlength="19">
                </div>
                <div class="flex">
                    <div class="input-field">
                        <p>Термін дії <span>*</span></p>
                        <input type="text" name="expiration" class="box" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="input-field">
                        <p>CVV <span>*</span></p>
                        <input type="text" name="cvv" class="box" placeholder="123" maxlength="3">
                    </div>
                </div>
            </div>
            <button type="submit" name="booking" class="btn">Підтвердити бронювання</button>
        </form>
    </div>

    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include 'js/user_script.js'; ?>
    </script>

    <script>
        const paymentMethod = document.getElementById('payment-method');
        const cardFields = document.getElementById('card-fields');

        if (paymentMethod && cardFields) {
            const cardInputs = cardFields.querySelectorAll('input');

            cardFields.style.display = 'none';

            paymentMethod.addEventListener('change', function(){
                if (this.value === 'Онлайн оплата') {
                    cardFields.style.display = 'block';

                    cardInputs.forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                }else{
                    cardFields.style.display = 'none';

                    cardInputs.forEach(input => {
                        input.removeAttribute('required');
                        input.value = '';
                    });
                }
            });
        }
    </script>

    <?php include 'components/alert.php'; ?>

</body>
</html>