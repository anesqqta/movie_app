<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM message WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_user = $conn->prepare("DELETE FROM message WHERE id = ?");
            $delete_user->execute([$delete_id]);
            $success_msg[] = 'Повідомлення видалено';
        }else{
            $warning_msg[] = 'Повідомлення уже видалено';
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
            <h1>Повідомлення</h1>
            <p>Переглядайте повідомлення від користувачів, відповідайте на запити та керуйте зворотним зв’язком у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Повідомлення</span>
        </div>
    </div>

    <!-- секція повідомлення-->
    <div class="gride">
        <div class="heading">
            <span>Повідомлення</span>
        </div>
            <div class="box-container">
                <?php
                    if (isset($_POST['search_box']) OR isset($_POST['search_btn'])) {
                        $search_box = $_POST['search_box'];
                        $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);

                        $select_msg = $conn->prepare("SELECT * FROM message WHERE name LIKE '%{$search_box}%' OR email LIKE '%{$search_box}%'");
                        $select_msg->execute();
                    }else{
                        $select_msg = $conn->prepare("SELECT * FROM message");
                        $select_msg->execute();
                    }
                    if ($select_msg->rowCount() > 0) {
                        while($fetch_msg = $select_msg->fetch(PDO::FETCH_ASSOC)) {
                            
                ?>
                <div class="box">
                    <p>Ім'я : <span><?= $fetch_msg['name']; ?></span></p>
                    <p>Email : <span><?= $fetch_msg['email']; ?></span></p>
                    <p>Предмет : <span><?= $fetch_msg['subject']; ?></span></p>
                    <p>Повідомлення : <span><?= $fetch_msg['message']; ?></span></p>
                    <form action="" method="post">
                        <input type="hidden" name="delete_id" value="<?= $fetch_msg['id']; ?>">
                        <button type="submit" name="delete" onclick="return confirm('Видалити це повідомлення?');" class="btn">Видалити</button>
                    </form>
                </div>
                <?php
                        }
                    }elseif(isset($_POST['search_box']) OR isset($_POST['search_btn'])) {
                        header('location:notfound.php');
                    }else{
                        echo '
                        <div class="empty">
                            <p>Ще не надіслано жодного повідомленння!</p>
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