<?php
    $auto_cancel = $conn->prepare("
        UPDATE booking b
        JOIN shows s ON b.show_id = s.id
        SET 
            b.status = 'анульовано',
            b.payment_status = 'анульовано'
        WHERE 
            b.status = 'очікує оплату'
            AND TIMESTAMP(s.show_date, s.show_time) <= DATE_ADD(NOW(), INTERVAL 30 MINUTE)
    ");
    $auto_cancel->execute();
?>