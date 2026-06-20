<?php
    include 'components/connect.php';
    include 'components/send_ticket_email.php';

    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
        header('location:login.php');
        exit();
    }

    if (isset($_GET['booking_id'])) {
        $booking_id = $_GET['booking_id'];
        $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);
    }else{
        header('location:my_booking.php');
        exit();
    }

    $check_booking = $conn->prepare("SELECT * FROM booking WHERE id = ? AND user_id = ?");
    $check_booking->execute([$booking_id, $user_id]);

    if ($check_booking->rowCount() == 0) {
        header('location:my_booking.php');
        exit();
    }

    if (isset($_POST['add_bar_item'])) {

        $product_id = $_POST['product_id'];
        $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);

        $quantity = $_POST['quantity'];
        $quantity = filter_var($quantity, FILTER_SANITIZE_STRING);

        if ($quantity < 1) {
            $quantity = 1;
        }

        $select_product = $conn->prepare("SELECT * FROM cinema_bar WHERE id = ? AND status = ?");
        $select_product->execute([$product_id, 'active']);

        if ($select_product->rowCount() > 0) {
            $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);

            $price = $fetch_product['price'];
            $total_price = $price * $quantity;

            $check_item = $conn->prepare("SELECT * FROM booking_bar_items WHERE booking_id = ? AND product_id = ?");
            $check_item->execute([$booking_id, $product_id]);

            if ($check_item->rowCount() > 0) {
                $fetch_item = $check_item->fetch(PDO::FETCH_ASSOC);

                $new_quantity = $fetch_item['quantity'] + $quantity;
                $new_total = $new_quantity * $price;

                $update_item = $conn->prepare("UPDATE booking_bar_items SET quantity = ?, total_price = ? WHERE id = ?");
                $update_item->execute([$new_quantity, $new_total, $fetch_item['id']]);

                $success_msg[] = 'Кількість товару оновлено';
            }else{
                $insert_item = $conn->prepare("INSERT INTO booking_bar_items (booking_id, product_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
                $insert_item->execute([$booking_id, $product_id, $quantity, $price, $total_price]);

                $success_msg[] = 'Товар додано до замовлення';
            }
        }else{
            $warning_msg[] = 'Товар не знайдено';
        }
    }

    if (isset($_POST['delete_item'])) {
        $item_id = $_POST['item_id'];
        $item_id = filter_var($item_id, FILTER_SANITIZE_STRING);

        $delete_item = $conn->prepare("DELETE FROM booking_bar_items WHERE id = ? AND booking_id = ?");
        $delete_item->execute([$item_id, $booking_id]);

        $success_msg[] = 'Товар видалено із замовлення';
    }

    if (isset($_POST['finish_order'])) {
        $select_booking_status = $conn->prepare("SELECT payment_status, ticket_code, ticket_sent FROM booking WHERE id = ? AND user_id = ?");
        $select_booking_status->execute([$booking_id, $user_id]);

        if ($select_booking_status->rowCount() > 0) {
            $booking_status = $select_booking_status->fetch(PDO::FETCH_ASSOC);

            if ($booking_status['payment_status'] == 'оплачено') {

                if (empty($booking_status['ticket_code'])) {
                    $ticket_code = 'TKT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));

                    $update_ticket = $conn->prepare("UPDATE booking SET ticket_code = ? WHERE id = ?");
                    $update_ticket->execute([$ticket_code, $booking_id]);
                }

                if ($booking_status['ticket_sent'] == 0) {
                    sendTicketEmail($conn, $booking_id);
                }
            }
        }

        header('location:my_booking.php');
        exit();
    }

    $select_items = $conn->prepare("SELECT booking_bar_items.*, cinema_bar.name FROM booking_bar_items JOIN cinema_bar ON booking_bar_items.product_id = cinema_bar.id WHERE booking_bar_items.booking_id = ?");
    $select_items->execute([$booking_id]);

    $bar_total = 0;
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
            <h1>Кінобар</h1>
            <p>Додайте попкорн, напої або комбо до свого бронювання</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Кінобар</span>
        </div>
    </div>

    <section class="cinema-bar">
        <div class="heading">
            <span>BOLETO cinema bar</span>
            <h1>Додати до бронювання</h1>
            <p>Ви можете обрати товари кінобару або пропустити цей крок</p>
        </div>
        <div class="box-container">
            <?php
                $select_products = $conn->prepare("SELECT * FROM cinema_bar WHERE status = ? ORDER BY id DESC");
                $select_products->execute(['active']);

                if ($select_products->rowCount() > 0) {
                    while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
            ?>
            <form action="" method="post" class="box">
                <img src="uploaded_files/<?= $fetch_product['image']; ?>" alt="">
                <div class="content">
                    <h3><?= $fetch_product['name']; ?></h3>
                    <p><?= $fetch_product['description']; ?></p>
                    <span><?= $fetch_product['category']; ?></span>
                    <h2><?= $fetch_product['price']; ?> грн</h2>
                    <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="10" class="qty">
                    <button type="submit" name="add_bar_item" class="btn">Додати</button>
                </div>
            </form>
            <?php
                    }
                }else{
                    echo '<div class="empty"><p>Товари кінобару ще не додано!</p></div>';
                }
            ?>
        </div>
        <div class="bar-order">
            <h2>Ваше замовлення з кінобару</h2>
            <?php
                if ($select_items->rowCount() > 0) {
                    while($fetch_item = $select_items->fetch(PDO::FETCH_ASSOC)){
                        $bar_total += $fetch_item['total_price'];
            ?>
            <form action="" method="post" class="bar-item">
                <input type="hidden" name="item_id" value="<?= $fetch_item['id']; ?>">
                <p><?= $fetch_item['name']; ?></p>
                <p><?= $fetch_item['quantity']; ?> шт.</p>
                <p><?= $fetch_item['total_price']; ?> грн</p>
                <button type="submit" name="delete_item" onclick="return confirm('Видалити товар?');">
                    <i class="bx bx-trash"></i>
                </button>
            </form>
            <?php
                    }
                }else{
                    echo '<p class="empty">Ви ще не додали товари кінобару</p>';
                }
            ?>
            <div class="bar-total">
                <h3>Сума кінобару: <span><?= $bar_total; ?> грн</span></h3>
            </div>
            <div class="flex-btn">
                <form action="" method="post">
                    <button type="submit" name="finish_order" class="btn">Завершити</button>
                </form>

                <form action="" method="post">
                    <button type="submit" name="finish_order" class="btn">Пропустити</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include 'js/user_script.js'; ?>
    </script>

    <?php include 'components/alert.php'; ?>

</body>
</html>