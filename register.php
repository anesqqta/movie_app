<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
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
            <h1>Зареєструватися зараз</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>зареєструватися зараз</span>
        </div>
    </div>

    <!-- секція реєстрації -->
    <div class="form-container form-area">
        <form action="" method="post" enctype="multipert/form-data" class="register">
            <h3>Створити акаунт</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Ваше ім'я <span>*</span></p>
                        <input type="text" name="name" required maxlength="50" placeholder="Введіть ім'я" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваш email <span>*</span></p>
                        <input type="email" name="email" required maxlength="50" placeholder="Введіть email" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваш номер <span>*</span></p>
                        <input type="text" name="number" required maxlength="50" placeholder="Введіть номер" class="box">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>Ваш пароль <span>*</span></p>
                        <input type="password" name="pass" required maxlength="20" placeholder="Введіть пароль" class="box">
                    </div>
                    <div class="input-field">
                        <p>Підтвердіть ваш пароль <span>*</span></p>
                        <input type="password" name="cpass" required maxlength="20" placeholder="Введіть повторно пароль" class="box">
                    </div>
                    <div class="input-field">
                        <p>Фото профілю <span>*</span></p>
                        <input type="file" name="image" required accept="image/*" class="box">
                    </div>
                </div>
            </div>
            <p class="link">Вже є обліковий запис ? <a href="login.php">Увійти зараз</a></p>
            <button type="submit" name="register" class="btn">зареєструватися зараз</button>
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