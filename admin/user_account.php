<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
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
            <h1>Зареєстровані клієнти</h1>
            <p>Переглядайте список користувачів, їхні дані та керуйте обліковими записами в системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Зареєстровані клієнти</span>
        </div>
    </div>

    <!-- секція клієнти-->
    <div class="user-container">
        <div class="heading">
            <h1>Зареєстровані клієнти</h1>
        </div>
        <form action="" method="post" class="search_form">
            <input type="text" name="search_box" placeholder="Пошук користувачів..." maxlength="100" required>
            <button type="submit" name="search_btn" class="bx bxs-search-alt-2"></button>
        </form>
            <div class="box-container">
                <?php
                    if (isset($_POST['search_box']) OR isset($_POST['search_btn'])) {
                        $search_box = $_POST['search_box'];
                        $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);

                        $select_users = $conn->prepare("SELECT * FROM users WHERE name LIKE '%{$search_box}%' OR email LIKE '%{$search_box}%'");
                        $select_users->execute();
                    }else{
                        $select_users = $conn->prepare("SELECT * FROM users");
                        $select_users->execute();
                    }
                    if ($select_users->rowCount() > 0) {
                        while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {
                            $user_id = $fetch_users['id'];
                ?>
                <div class="box">
                    <img src="../uploaded_files/<?= $fetch_users['image'] ?>">
                    <div class="detail">
                        <p>Id користувача : <span><?= $user_id; ?></span></p>
                        <p>Ім'я : <span><?= $fetch_users['name']; ?></span></p>
                        <p>Email : <span><?= $fetch_users['email']; ?></span></p>
                        <p>Номер : <span><?= $fetch_users['number']; ?></span></p>
                        <form action="" method="post">
                            <input type="hidden" name="delete_id" value="<?= $fetch_users['id']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('Видалити цього користувача?');" class="btn">Видалити</button>
                        </form>
                    </div>
                </div>
                <?php
                        }
                    }elseif(isset($_POST['search_box']) OR isset($_POST['search_btn'])) {
                        header('location:notfound');
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