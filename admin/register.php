<?php
    include '../components/connect.php';
    
    if (isset($_POST['register'])) {
        $id = unique_id();

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        $pass = sha1($_POST['pass']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        $cpass = sha1($_POST['cpass']);
        $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id().'.'.$ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/'.$rename;

        $select_admin = $conn->prepare("SELECT * FROM admin WHERE email = ?");
        $select_admin->execute([$email]);

        if ($select_admin->rowCount() > 0) {
            $warning_msg[] = 'Електронна адреса вже існує';
        }else{
            if ($pass != $cpass) {
                $warning_msg[] = 'Паролі не збігаються';
            }else{
                $insert_admin = $conn->prepare("INSERT INTO admin (id, name, email, password, image) VALUES(?,?,?,?,?)");
                $insert_admin->execute([$id, $name, $email, $cpass, $rename]);

                move_uploaded_file($image_tmp_name, $image_folder);

                if ($insert_admin) {
                    $verify_admin = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ? LIMIT 1");
                    $verify_admin->execute([$email, $pass]);
                    $row = $verify_admin->fetch(PDO::FETCH_ASSOC);

                    if ($verify_admin->rowCount() > 0) {
                        setcookie('admin_id', $row['id'], time() + 60*60*24*30, '/');
                        header('location:login.php');
                    }else{
                        $warning_msg[] = 'Щось пішло не так';
                    }
                }
            }
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