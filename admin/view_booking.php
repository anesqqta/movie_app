<?php
    include '../components/connect.php';

    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
        exit();
    }

    if (isset($_POST['delete'])) {
        $delete_id = $_POST['booking_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $delete_booking = $conn->prepare("DELETE FROM booking WHERE id = ?");
        $delete_booking->execute([$delete_id]);

        $success_msg[] = 'Бронювання видалено';
    }

    if (isset($_POST['mark_paid'])) {
        $booking_id = $_POST['booking_id'];
        $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);

        $check_booking = $conn->prepare("SELECT * FROM booking WHERE id = ?");
        $check_booking->execute([$booking_id]);
        $booking_data = $check_booking->fetch(PDO::FETCH_ASSOC);

        if ($booking_data) {

            $show_datetime = strtotime($booking_data['date'] . ' ' . $booking_data['time']);
            $current_datetime = time();

            if ($booking_data['status'] == 'скасовано' || $booking_data['status'] == 'анульовано') {
                $warning_msg[] = 'Неможливо оплатити скасоване або анульоване бронювання';
            } elseif ($show_datetime < $current_datetime) {
                $warning_msg[] = 'Неможливо оплатити бронювання, оскільки сеанс уже пройшов';
            } else {
                if (empty($booking_data['ticket_code'])) {
                    $ticket_code = 'TKT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
                } else {
                    $ticket_code = $booking_data['ticket_code'];
                }

                $update_booking = $conn->prepare("UPDATE booking SET status = ?, payment_status = ?, ticket_code = ? WHERE id = ?");

                $update_booking->execute(['оплачено', 'оплачено', $ticket_code, $booking_id]);

                $success_msg[] = 'Бронювання позначено як оплачено';
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>BOLETO</title>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<div class="banner">
    <div class="detail">
        <h1>Переглянути бронювання</h1>
        <p>Переглядайте дані бронювань користувачів, обрані фільми, місця та статус оплати</p>
        <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути бронювання</span>
    </div>
</div>

<div class="hall-container">
    <div class="heading">
        <h1>Деталі бронювань</h1>
    </div>

    <div class="box-container" style="overflow-x: auto;">
    <?php
        $select_bookings = $conn->prepare(" SELECT booking.*, movies.title AS movie_title, halls.name AS hall_name, users.name AS user_name, users.email AS user_email FROM booking JOIN movies ON booking.movie_id = movies.id JOIN shows ON booking.show_id = shows.id JOIN halls ON shows.hall_id = halls.id JOIN users ON booking.user_id = users.id ORDER BY booking.id DESC ");
        $select_bookings->execute();

        if ($select_bookings->rowCount() > 0) {
    ?>

    <table cellspacing="0" style="width: 100%;">
        <tr>
            <th>ID</th>
            <th>Фільм</th>
            <th>Користувач</th>
            <th>Email</th>
            <th>Зал</th>
            <th>Дата</th>
            <th>Час</th>
            <th>Місця</th>
            <th>Сума</th>
            <th>Статус</th>
            <th>Оплата</th>
            <th>Дія</th>
        </tr>

        <?php while($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)){ ?>
        <tr>
            <td>#<?= $fetch_booking['id']; ?></td>
            <td><?= $fetch_booking['movie_title']; ?></td>
            <td><?= $fetch_booking['user_name']; ?></td>
            <td><?= $fetch_booking['user_email']; ?></td>
            <td><?= $fetch_booking['hall_name']; ?></td>
            <td><?= $fetch_booking['date']; ?></td>
            <td><?= $fetch_booking['time']; ?></td>
            <td><?= $fetch_booking['seat_details']; ?></td>
            <td>$<?= $fetch_booking['amount']; ?>/-</td>
            <td><?= $fetch_booking['status']; ?></td>
            <td><?= $fetch_booking['payment_status']; ?></td>
            <td>
                <?php
                    $show_datetime = strtotime($fetch_booking['date'] . ' ' . $fetch_booking['time']);
                    $is_available_for_payment = (
                        $fetch_booking['payment_status'] != 'оплачено' &&
                        $fetch_booking['status'] != 'скасовано' &&
                        $fetch_booking['status'] != 'анульовано' &&
                        $show_datetime >= time()
                    );
                ?>

                <?php if ($is_available_for_payment) { ?>
                    <form action="" method="post" style="margin-bottom: .5rem;">
                        <input type="hidden" name="booking_id" value="<?= $fetch_booking['id']; ?>">
                        <button type="submit" name="mark_paid" onclick="return confirm('Позначити це бронювання як оплачено?');" class="btn">Позначити як оплачено</button>
                    </form>
                <?php } ?>

                <form action="" method="post">
                    <input type="hidden" name="booking_id" value="<?= $fetch_booking['id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Видалити це бронювання?');" class="btn">Видалити</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php
        }else{
            echo '
            <div class="empty">
                <p>Бронювань ще немає!</p>
            </div>
            ';
        }
    ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script type="text/javascript">
    <?php include '../js/admin_script.js'; ?>
</script>

<?php include '../components/alert.php'; ?>

</body>
</html>