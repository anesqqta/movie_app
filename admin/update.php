<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['update'])) {
        $select_admin = $conn->prepare("SELECT * FROM admin  WHERE id = ? LIMIT 1");
        $select_admin->execute([$admin_id]);
        $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);

        $prev_pass = $fetch_admin['password'];
        $prev_image = $fetch_admin['image'];

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        //оновити ім'я
        if (!empty($name)) {
            $update_name = $conn->prepare("UPDATE admin SET name = ? WHERE id = ?");
            $update_name->execute([$name, $admin_id]);
            $success_msg[] = 'Імя адміна оновлено';
        }

        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        //оновити email
        if (!empty($email)) {
            $select_email = $conn->prepare("SELECT email FROM admin WHERE id = ? AND email = ?");
            $select_email->execute([$admin_id, $email]);

            if ($select_email->rowCount() > 0) {
                $warning_msg[] = 'Електронна адреса вже існує';
            }else{
                $update_email = $conn->prepare("UPDATE admin SET email = ? WHERE id = ?");
                $update_email->execute([$email, $admin_id]);
                $success_msg[] = 'Email оновлено';
            }
        }

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id().'.'.$ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/'.$rename;

        //оновити фото
        if (!empty($image)) {
            if ($image_size > 2000000) {
                $warning_msg[] = 'Розмір фото занадто великий';
            }else{
                $update_image = $conn->prepare("UPDATE admin SET image = ? WHERE id = ?");
                $update_image->execute([$rename, $admin_id]);
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
                    $update_pass = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
                    $update_pass->execute([$cpass, $admin_id]);
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
        <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Оновлення профіль</h1>
            <p>Редагуйте дані адміністратора, змінюйте інформацію та підтримуйте актуальність облікового запису</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Оновлення профіль</span>
        </div>
    </div>

    <!-- секція оновлення профілю-->
    <div class="form-container form-area">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <div class="img-box">
                <img src="../uploaded_files/<?= $fetch_profile['image']; ?>">
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
                        <p>Оновити профіль <span>*</span></p>
                        <input type="file" name="image" required accept="image/*" class="box">
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
            <div class="flex-btn" style="margin-top: 2rem">
                <button type="submit" name="update" class="btn">Оновити профіль</button>
                <a href="profile.php" class="btn">Повернутись назад</a>
            </div>
        </form>
    </div>
    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>