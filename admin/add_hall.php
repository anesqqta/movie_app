<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['add_hall'])) {

        $hall_name = $_POST['hall_name'];
        $hall_name = filter_var($hall_name, FILTER_SANITIZE_STRING);
        
        $hall_location = $_POST['location'];
        $hall_location = filter_var($hall_location, FILTER_SANITIZE_STRING);

        $city = $_POST['city'];
        $city = filter_var($city, FILTER_SANITIZE_STRING);

        $select_hall = $conn->prepare("SELECT * FROM halls WHERE name = ? AND location = ?");
        $select_hall->execute([$hall_name, $hall_location]);

        if ($select_hall->rowCount() > 0) {
            $warning_msg[] = 'Зал вже існує';
        }else{
            $insert_hall = $conn->prepare("INSERT INTO halls (name, location, city) VALUES (?, ?, ?)");
            $insert_hall->execute([$hall_name, $hall_location, $city]);

            $success_msg[] = 'Зал успішно додано';
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
            <h1>Додавання залу</h1>
            <p>Додавайте нові кінозали, вказуйте їх характеристики, місткість та розташування для подальшого використання у сеансах</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Додавання залу</span>
        </div>
    </div>

    <!-- секція додавання залу-->
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Додати зал</h3>
            <div class="input-field">
                <p>Назва залу <span>*</span></p>
                <input type="text" name="hall_name" required maxlength="100" placeholder="Введіть назву залу" class="box">
            </div>
            <div class="input-field">
                <p>Розташування залу <span>*</span></p>
                <input type="text" name="location" required maxlength="100" placeholder="Введіть розташування залу" class="box">
            </div>
            <div class="input-field">
                <p>Місто <span>*</span></p>
                <input type="text" name="city" required maxlength="100" placeholder="Введіть місто" class="box">
            </div>
            <button type="submit" name="add_hall" class="btn">Додати зал</button>
        </form>
    </div>
     

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>