<?php
    include 'components/connect.php';

    if(isset($_COOKIE['user_id'])){
        header('location:home.php');
        exit();
    }

    if(isset($_COOKIE['admin_id'])){
        header('location:admin/dashboard.php');
        exit();
    }

    if(isset($_POST['submit'])){

        $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $pass = sha1($_POST['pass']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        // перевірка адміна
        $select_admin = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
        $select_admin->execute([$email, $pass]);

        if($select_admin->rowCount() > 0){

            $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);

            setcookie('admin_id', $fetch_admin['id'], time() + 60*60*24*30, '/');

            header('location:admin/dashboard.php');
            exit();

        }else{

            // перевірка користувача
            $select_user = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $select_user->execute([$email, $pass]);

            if($select_user->rowCount() > 0){

                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

                setcookie('user_id', $fetch_user['id'], time() + 60*60*24*30, '/');

                header('location:home.php');
                exit();

            }else{
                $warning_msg[] = 'Невірний email або пароль!';
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">

    <title>BOLETO</title>
</head>
<body>

    <div class="form-area">
        <section class="form-container">
            <form action="" method="post" class="login">
                <h3>увійти зараз</h3>
                <p>Ваш email <span>*</span></p>
                <input type="email" name="email" placeholder="Введіть email" required class="box">
                <p>Ваш пароль <span>*</span></p>
                <input type="password" name="pass" placeholder="Введіть пароль" required class="box">
                <input type="submit" name="submit" value="Увійти" class="btn">
                <p class="link">Не маєте акаунта? <a href="register.php">Зареєструватись</a></p>
            </form>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include 'js/user_script.js'; ?>
    </script>

    <?php include 'components/alert.php'; ?>

</body>
</html>