<?php
    include 'components/connect.php';

    if (!isset($_GET['booking_id'])) {
        die('Недійсний запит');
    }

    $booking_id = $_GET['booking_id'];

    //отримати бронювання
    $stmt = $conn->prepare("SELECT * FROM booking WHERE id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    $show_id = $booking['show_id'];

    //отримати деталі сеансу
    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$booking['movie_id']]);

    if ($movie_stmt->rowCount() > 0) {
        $fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC);

        $movie_name = $fetch_movie['title'];
        $movie_duration = $fetch_movie['duration'];
    }

    $show_stmt = $conn->prepare("SELECT * FROM shows WHERE id = ?");
    $show_stmt->execute([$show_id]);

    if ($show_stmt->rowCount() > 0) {
        while($fetch_show = $show_stmt->fetch(PDO::FETCH_ASSOC)){
            $hall_id = $fetch_show['hall_id'];

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
    if (!$booking) {
        die('Бронювання не знайдено');
    }

    //заголовки файлів word
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="movie_ticket_'.$booking_id.'.doc"');
    header('Pragma: no-cache');
    header('Expires: 0');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1">
        <title>Movie Ticket</title>
        <style type="text/css">
            body{
                font-family: Arial;
            }
            h3{
                text-align: center;
            }
            table{
                white-space: 100%;
                border-collapse: collapse;
            }
            td{
                padding: 8px;
                border: 1px solid #000;
            }
        </style>
    </head>
    <body>
        <h3>📽️Movie Ticket🎞️</h3>

        <table>
            <tr><td><b>Ідентифікатор бронювання</b><td><?= $booking['id']; ?></td></td></tr>
            <tr><td><b>Дата</b><td><?= $booking['date']; ?></td></td></tr>
            <tr><td><b>Час</b><td><?= $booking['time']; ?></td></td></tr>
            <tr><td><b>Назва фільму</b><td><?= $movie_name; ?></td></td></tr>
            <tr><td><b>Тривалість</b><td><?= $movie_duration; ?></td></td></tr>
            <tr><td><b>Мова</b><td><?= $booking['language']; ?></td></td></tr>
            <tr><td><b>Формат</b><td><?= $booking['formate']; ?></td></td></tr>
            <tr><td><b>Загальна кількість місць</b><td><?= $booking['total_seat']; ?></td></td></tr>
            <tr><td><b>Місця</b><td><?= $booking['seat_details']; ?></td></td></tr>
            <tr><td><b>Назва залу</b><td><?= $hall_name; ?></td></td></tr>
            <tr><td><b>Розташування</b><td><?= $hall_location; ?></td></td></tr>
            <tr><td><b>Місто</b><td><?= $hall_city; ?></td></td></tr>
            <tr><td><b>Статус оплати</b><td><?= $booking['payment_status']; ?></td></td></tr>
            <tr><td><b>Статус бронювання</b><td><?= $booking['status']; ?></td></td></tr>
        </table>
        <p style="text-align: center; margin-top: 20px;">Будь ласка, покажіть цей квиток при вході в кінотеатр</p>
    </body>
</html>