<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['delete'])) {
        $b_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_STRING);
        $seat_id = filter_var($_POST['seat_id'], FILTER_SANITIZE_STRING);
        
        //check booking exists
        $check = $conn->prepare("SELECT id FROM booking WHERE id = ?");
        $check->execute([$b_id]);

        if ($check->rowCount() > 0) {
            $delete_booking = $conn->prepare("DELETE FROM booking WHERE id = ?");
            $delete_booking->execute([$b_id]);

            //delete seat details
            $delete_seat = $conn->prepare("DELETE FROM seat_details WHERE id = ?");
            $delete_seat->execute([$seat_id]);

            $success_msg[] = "Бронювання видалено";
        }else{
            $warning_msg[] = 'Вже видалено';
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
        <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Зарезервовані місця</h1>
            <p>Переглядайте інформацію про заброньовані місця, обрані сеанси та керуйте бронюваннями в системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Зарезервовані місця</span>
        </div>
    </div>

    <!-- секція зарезервовані місця-->
    <div class="researved-seat">
        <div class="heading">
            <span>Зарезервовані місця</span>
            <h1>Зарезервовані місця</h1>
        </div>
        <div class="box-container">
            <?php
                $select_reserved = $conn->prepare("SELECT * FROM booking");
                $select_reserved->execute();

                if ($select_reserved->rowCount() > 0) {
                    while($fetch_reserved = $select_reserved->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <table cellspaning="0">
                <tr>
                    <th>Деталі місця</th>
                    <th>Статус</th>
                    <th>Дія</th>
                </tr>
                <tr>
                    <td><?= $fetch_reserved['seat_details']; ?></td>
                    <td style="color: <?php if($fetch_reserved['status'] == 'підтверджено'){echo "green";}else{echo "red";} ?>"><?= $fetch_reserved['status']; ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="seat_id" value="<?= $fetch_reserved['seat_detail_id']; ?>">
                            <input type="hidden" name="booking_id" value="<?= $fetch_reserved['id']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('Видалити це бронювання?');" class="btn">Видалити</button>
                        </form>
                    </td>
                </tr>
            </table>
            <?php
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Ще не зареєстровано жодного користувача!</p>
                    </div>
                    ';
                }
            ?>
        </div>
    </div>
    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>