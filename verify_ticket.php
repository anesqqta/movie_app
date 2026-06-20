<?php
include 'components/connect.php';

$code = '';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $code = filter_var($code, FILTER_SANITIZE_STRING);
}

$ticket_found = false;

if (!empty($code)) {
    $select_ticket = $conn->prepare(" SELECT booking.*, movies.title AS movie_title, halls.name AS hall_name, users.name AS user_name, users.email AS user_email FROM booking JOIN movies ON booking.movie_id = movies.id JOIN shows ON booking.show_id = shows.id JOIN halls ON shows.hall_id = halls.id JOIN users ON booking.user_id = users.id WHERE booking.ticket_code = ? LIMIT 1 ");
    $select_ticket->execute([$code]);

    if ($select_ticket->rowCount() > 0) {
        $ticket_found = true;
        $ticket = $select_ticket->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLETO</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="ticket-check">
    <div class="ticket-box">
        <?php if ($ticket_found) { ?>

            <?php
                $show_datetime = strtotime($ticket['date'] . ' ' . $ticket['time']);
                $current_datetime = time();

                if ($ticket['payment_status'] != 'оплачено') {
                    $ticket_status_text = 'Квиток не оплачено';
                    $ticket_status_class = 'invalid';
                } elseif ($ticket['ticket_used'] == 1) {
                    $ticket_status_text = 'Квиток уже використаний';
                    $ticket_status_class = 'used';
                } elseif ($ticket['status'] == 'скасовано' || $ticket['status'] == 'анульовано') {
                    $ticket_status_text = 'Квиток недійсний';
                    $ticket_status_class = 'invalid';
                } elseif ($show_datetime < $current_datetime) {
                    $ticket_status_text = 'Сеанс уже пройшов';
                    $ticket_status_class = 'invalid';
                } else {
                    $ticket_status_text = 'Квиток дійсний';
                    $ticket_status_class = 'valid';
                }
            ?>

            <h1>Перевірка квитка</h1>
            <div class="ticket-status <?= $ticket_status_class; ?>">
                <?= $ticket_status_text; ?>
            </div>

            <div class="ticket-info">
                <p><span>Код квитка:</span> <?= $ticket['ticket_code']; ?></p>
                <p><span>Фільм:</span> <?= $ticket['movie_title']; ?></p>
                <p><span>Користувач:</span> <?= $ticket['user_name']; ?></p>
                <p><span>Email:</span> <?= $ticket['user_email']; ?></p>
                <p><span>Зал:</span> <?= $ticket['hall_name']; ?></p>
                <p><span>Дата:</span> <?= $ticket['date']; ?></p>
                <p><span>Час:</span> <?= $ticket['time']; ?></p>
                <p><span>Місця:</span> <?= $ticket['seat_details']; ?></p>
                <p><span>Сума:</span> $<?= $ticket['amount']; ?></p>
                <p><span>Статус бронювання:</span> <?= $ticket['status']; ?></p>
                <p><span>Статус оплати:</span> <?= $ticket['payment_status']; ?></p>
            </div>

        <?php } else { ?>

            <h1>Перевірка квитка</h1>
            <div class="ticket-status invalid">
                Квиток не знайдено
            </div>
            <p>QR-код недійсний або бронювання не існує.</p>

        <?php } ?>

        <a href="home.php" class="btn">На головну</a>
    </div>
</div>

</body>
</html>