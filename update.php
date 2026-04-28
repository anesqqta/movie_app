<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    if (isset($_POST['update'])) {
        $select_user = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $select_user->execute([$user_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

        $prev_pass = $fetch_user['password'];
        $prev_image = $fetch_user['image'];

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        //оновити ім'я
        if (!empty($name)) {
            $update_name = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
            $update_name->execute([$name, $user_id]);
            $success_msg[] = 'Імя користувача оновлено';
        }

        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        //оновити email
        if (!empty($email)) {
            $select_email = $conn->prepare("SELECT email FROM users WHERE id = ? AND email = ?");
            $select_email->execute([$user_id, $email]);

            if ($select_email->rowCount() > 0) {
                $warning_msg[] = 'Електронна адреса вже існує';
            }else{
                $update_email = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                $update_email->execute([$email, $user_id]);
                $success_msg[] = 'Email оновлено';
            }
        }

        $number = $_POST['number'];
        $number = filter_var($number, FILTER_SANITIZE_STRING);

        //оновити номер
        if (!empty($number)) {
            $update_number = $conn->prepare("UPDATE users SET number = ? WHERE id = ?");
            $update_number->execute([$number, $user_id]);
            $success_msg[] = 'Номер оновлено';
        }

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id().'.'.$ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploaded_files/'.$rename;

        //оновити фото
        if (!empty($image)) {
            if ($image_size > 2000000) {
                $warning_msg[] = 'Розмір фото занадто великий';
            }else{
                $update_image = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
                $update_image->execute([$rename, $user_id]);
                move_uploaded_file($image_tmp_name, $image_folder);

                $success_msg[] = 'Фото оновлено';
            }
        }

        //оновити пароль
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

        $old_pass = sha1($_POST['old_pass']);
        $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);

        $new_pass = sha1($_POST['new_pass']);
        $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);

        $cpass = sha1($_POST['cpass']);
        $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

        if ($old_pass != $empty_pass) {
            if ($old_pass != $prev_pass) {
                $warning_msg[] = 'Старий пароль не збігається';
            }elseif($new_pass != $cpass) {
                $warning_msg[] = 'Підтвердити пароль не вдалося';
            }else{
                if ($new_pass != $empty_pass) {
                    $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_pass->execute([$cpass, $user_id]);
                    $success_msg[] = 'Пароль оновлено';
                }else{
                    $warning_msg[] = 'Будь ласка, введіть новий пароль';
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
        <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Оновити профіль</h1>
            <p>Редагуйте особисті дані, змінюйте контактну інформацію та підтримуйте свій акаунт в актуальному стані</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Оновити профіль</span>
        </div>
    </div>

    <!-- секція оновлення профілю -->
    <div class="form-container form-area">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <div class="img-box">
                <img src="uploaded_files/<?= $fetch_profile['image']; ?>">
            </div>
            <h3>Оновити профіль</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Ваше ім'я <span>*</span></p>
                        <input type="text" name="name" maxlength="50" placeholder="<?= $fetch_profile['name']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваш email <span>*</span></p>
                        <input type="email" name="email" maxlength="50" placeholder="<?= $fetch_profile['email']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваш номер <span>*</span></p>
                        <input type="text" name="number" maxlength="50" placeholder="<?= $fetch_profile['number']; ?>" class="box">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>Старий пароль <span>*</span></p>
                        <input type="password" name="old_pass" maxlength="20" placeholder="Введіть старий пароль" class="box">
                    </div>
                    <div class="input-field">
                        <p>Новий пароль <span>*</span></p>
                        <input type="password" name="new_pass" maxlength="20" placeholder="Введіть новий пароль" class="box">
                    </div>
                    <div class="input-field">
                        <p>Підтвердіть ваш пароль <span>*</span></p>
                        <input type="password" name="cpass" required maxlength="20" placeholder="Введіть повторно пароль" class="box">
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>Оновити профіль <span>*</span></p>
                <input type="file" name="image" required accept="image/*" class="box">
            </div>
            <div class="flex-btn">
                <button type="submit" name="update" class="btn">Оновити профіль</button>
                <a href="profile.php" class="btn">Повернутись назад</a>
            </div>
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