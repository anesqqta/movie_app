<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['add_actor'])) {

        $actor_name = $_POST['actor_name'];
        $actor_name = filter_var($actor_name, FILTER_SANITIZE_STRING);
        
        $actor_role = $_POST['role'];
        $actor_role = filter_var($actor_role, FILTER_SANITIZE_STRING);

        $actor_bio = $_POST['bio'];
        $actor_bio = filter_var($actor_bio, FILTER_SANITIZE_STRING);

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id().'.'.$ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/actors/'.$rename;

        $select_actor = $conn->prepare("SELECT * FROM actors WHERE name = ?");
        $select_actor->execute([$actor_name]);

        if ($select_actor->rowCount() > 0) {
            $warning_msg[] = 'Актор вже існує';
        }else{
            $insert_actor = $conn->prepare("INSERT INTO actors (name, role, bio, image) VALUES (?, ?, ?, ?)");
            $insert_actor->execute([$actor_name, $actor_role, $actor_bio, $rename]);
            move_uploaded_file($image_tmp_name, $image_folder);

            $success_msg[] = 'Актора успішно додано';
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
            <h1>Додавання актора</h1>
            <p>Додавайте нових акторів, заповнюйте інформацію про них та пов’язуйте з відповідними фільмами</p>
            <span><a href="view_ators.php">Переглянути акторів</a><i class="bx bxs-right-arrow-alt"></i>Додавання актора</span>
        </div>
    </div>

    <!-- секція додавання актора-->
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Додати актора</h3>
            <div class="input-field">
                <p>Ім'я актора<span>*</span></p>
                <input type="text" name="actor_name" required maxlength="50" placeholder="Введіть ім'я актора" class="box">
            </div>
            <div class="input-field">
                <p>Роль актора<span>*</span></p>
                <input type="text" name="role" required maxlength="50" placeholder="Введіть роль актора" class="box">
            </div>
            <div class="input-field">
                <p>Біографія актора<span>*</span></p>
                <textarea name="bio" required style="height:10rem;" class="box"></textarea>
            </div>
            <div class="input-field">
                <p>Фото актора<span>*</span></p>
                <input type="file" name="image" required accept="image/*" class="box">
            </div>
            <button type="submit" name="add_actor" class="btn">Додати актора</button>
        </form>
    </div>
     

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>