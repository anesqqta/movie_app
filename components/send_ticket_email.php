<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/PHPMailer-master/src/SMTP.php';

function sendTicketEmail($conn, $booking_id){
    $select_ticket = $conn->prepare("
        SELECT 
            booking.*,
            users.name AS user_name,
            users.email AS user_email,
            movies.title AS movie_title,
            halls.name AS hall_name
        FROM booking
        JOIN users ON booking.user_id = users.id
        JOIN movies ON booking.movie_id = movies.id
        JOIN shows ON booking.show_id = shows.id
        JOIN halls ON shows.hall_id = halls.id
        WHERE booking.id = ?
        LIMIT 1
    ");
    $select_ticket->execute([$booking_id]);

    if ($select_ticket->rowCount() == 0) {
        return false;
    }

    $ticket = $select_ticket->fetch(PDO::FETCH_ASSOC);

    if ($ticket['payment_status'] != 'оплачено' || empty($ticket['ticket_code'])) {
        return false;
    }

    $verify_link = 'https://localhost/movie_app/verify_ticket.php?code=' . $ticket['ticket_code'];
    $qr_link = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($verify_link);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cinemaboleto@gmail.com';
        $mail->Password = 'fuap gkrj uink edhw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('cinemaboleto@gmail.com', 'BOLETO Cinema');
        $mail->addAddress($ticket['user_email'], $ticket['user_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Ваш квиток BOLETO';

        $mail->Body = '
            <div style="font-family: Arial, sans-serif; background:#001232; padding:25px; color:#ffffff;">
                <div style="max-width:600px; margin:auto; background:#032055; padding:25px; border-radius:12px;">
                    <h2 style="color:#31d7a9; text-align:center;">Ваш квиток BOLETO</h2>
                    <p>Вітаємо, <b>' . htmlspecialchars($ticket['user_name']) . '</b>!</p>
                    <p>Ваше бронювання успішно оплачено.</p>

                    <p><b>Фільм:</b> ' . htmlspecialchars($ticket['movie_title']) . '</p>
                    <p><b>Зал:</b> ' . htmlspecialchars($ticket['hall_name']) . '</p>
                    <p><b>Дата:</b> ' . htmlspecialchars($ticket['date']) . '</p>
                    <p><b>Час:</b> ' . htmlspecialchars($ticket['time']) . '</p>
                    <p><b>Місця:</b> ' . htmlspecialchars($ticket['seat_details']) . '</p>
                    <p><b>Сума:</b> ' . htmlspecialchars($ticket['amount']) . ' грн</p>
                    <p><b>Код квитка:</b> ' . htmlspecialchars($ticket['ticket_code']) . '</p>

                    <div style="text-align:center; margin:25px 0;">
                        <img src="' . $qr_link . '" alt="QR ticket" style="background:#fff; padding:10px; border-radius:8px;">
                    </div>

                    <p style="text-align:center;">
                        <a href="' . $verify_link . '" style="color:#31d7a9;">Перевірити квиток</a>
                    </p>

                    <p style="font-size:13px; color:#dbe2fb;">Покажіть цей QR-код при вході до залу.</p>
                </div>
            </div>
        ';

        $mail->AltBody = 'Ваш квиток BOLETO. Код квитка: ' . $ticket['ticket_code'] . '. Перевірка: ' . $verify_link;

        $mail->send();

        $update_sent = $conn->prepare("UPDATE booking SET ticket_sent = 1 WHERE id = ?");
        $update_sent->execute([$booking_id]);

        return true;

    } catch (Exception $e) {
        return false;
    }
}
?>