<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        $pass = sha1($_POST['pass']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        $select_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $select_user->execute([$email]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if ($select_user->rowCount() > 0) {
            setcookie('user_id', $row['id'], time() + 60*60+24, '/');
            header('location:home.php');
        }else{
            $warning_msg[] = 'Некоректний пароль або email';
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
        <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Увійти</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Увійти</span>
        </div>
    </div>

    <!-- секція входу -->
    <div class="form-container form-area">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Увійти</h3>

            <div class="input-field">
                <p>Ваш email <span>*</span></p>
                <input type="email" name="email" required maxlength="50" placeholder="Введіть email" class="box">
            </div>
            <div class="input-field">
                <p>Ваш пароль <span>*</span></p>
                <input type="password" name="pass" required maxlength="20" placeholder="Введіть пароль" class="box">
            </div>
            <p class="link">Не маєте обліковий запис ? <a href="register.php">Зареєструватися</a></p>
            <button type="submit" name="login" class="btn">увійти</button>
        </form>
    </div>
    

    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            <?php include 'js/user_script.js'; ?>
        </script>


        <?php include 'components/alert.php'; ?>
    </body>
</html>