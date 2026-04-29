<?php
    include '../components/connect.php';
    
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
        <div class="form-container form-area">
            <form action="" method="post" enctype="multipart/form-data" class="register">
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
                    </div>
                </div>
                <div class="input-field">
                    <p>Фото профілю <span>*</span></p>
                    <input type="file" name="image" required accept="image/*" class="box">
                </div>
                <p class="link">Вже є обліковий запис ? <a href="login.php">Увійти</a></p>
                <button type="submit" name="register" class="btn">зареєструватися</button>
            </form>
        </div>

        <?php include '../components/alert.php'; ?>
    </body>
</html>