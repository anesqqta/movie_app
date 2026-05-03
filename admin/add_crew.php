<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['add_crew'])) {

        $crew_name = $_POST['crew_name'];
        $crew_name = filter_var($crew_name, FILTER_SANITIZE_STRING);
        
        $crew_role = $_POST['role'];
        $crew_role = filter_var($crew_role, FILTER_SANITIZE_STRING);

        $crew_bio = $_POST['bio'];
        $crew_bio = filter_var($crew_bio, FILTER_SANITIZE_STRING);

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id().'.'.$ext;
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/actors/'.$rename;

        $select_crew = $conn->prepare("SELECT * FROM crew_members WHERE name = ?");
        $select_crew->execute([$crew_name]);

        if ($select_crew->rowCount() > 0) {
            $warning_msg[] = 'Член команди вже існує';
        }else{
            $insert_crew = $conn->prepare("INSERT INTO crew_members (name, role, bio, image) VALUES (?, ?, ?, ?)");
            $insert_crew->execute([$crew_name, $crew_role, $crew_bio, $rename]);
            move_uploaded_file($image_tmp_name, $image_folder);

            $success_msg[] = 'Члена команди успішно додано';
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
            <h1>Додавання члену команди</h1>
            <p>Додавайте нових членів команди, заповнюйте інформацію про них та пов’язуйте з відповідними фільмами</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Додавання члену команди</span>
        </div>
    </div>

    <!-- секція додавання члену команди-->
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Додати члена команди</h3>
            <div class="input-field">
                <p>Ім'я члена команди<span>*</span></p>
                <input type="text" name="crew_name" required maxlength="50" placeholder="Введіть ім'я члена команди" class="box">
            </div>
            <div class="input-field">
                <p>Посада члена команди<span>*</span></p>
                <input type="text" name="role" required maxlength="50" placeholder="Введіть посаду члена команди" class="box">
            </div>
            <div class="input-field">
                <p>Біографія члена команди<span>*</span></p>
                <textarea name="bio" required style="height:10rem;" class="box"></textarea>
            </div>
            <div class="input-field">
                <p>Фото члена команди<span>*</span></p>
                <input type="file" name="image" required accept="image/*" class="box">
            </div>
            <button type="submit" name="add_crew" class="btn">Додати члена команди</button>
        </form>
    </div>
     

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>