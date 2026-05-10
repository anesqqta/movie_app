<?php
    include '../components/connect.php';

    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
        exit();
    }

    if (isset($_GET['get_id'])) {
        $edit_id = $_GET['get_id'];
        $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
    }else{
        header('location:view_hall.php');
        exit();
    }

    if (isset($_POST['edit_hall'])) {

        $hall_name = filter_var($_POST['hall_name'], FILTER_SANITIZE_STRING);
        $hall_location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);

        $update_hall = $conn->prepare("
            UPDATE halls 
            SET name = ?, location = ?, city = ?
            WHERE id = ?
        ");

        $update_hall->execute([
            $hall_name,
            $hall_location,
            $city,
            $edit_id
        ]);

        $success_msg[] = 'Зал оновлено!';
    }

    $edit_query = $conn->prepare("SELECT * FROM halls WHERE id = ?");
    $edit_query->execute([$edit_id]);

    if ($edit_query->rowCount() > 0) {
        $fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC);
    }else{
        header('location:view_hall.php');
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>BOLETO</title>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<div class="banner">
    <div class="detail">
        <h1>Редагувати зал</h1>
        <p>Оновіть інформацію про зал кінотеатру</p>
        <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Редагувати зал</span>
    </div>
</div>

<div class="form-container">
    <form action="" method="post" class="login">

        <h3>Редагувати зал</h3>

        <div class="input-field">
            <p>Назва залу <span>*</span></p>
            <input type="text" name="hall_name" value="<?= $fetch_edit['name']; ?>" class="box" required>
        </div>

        <div class="input-field">
            <p>Розташування залу <span>*</span></p>
            <input type="text" name="location" value="<?= $fetch_edit['location']; ?>" class="box" required>
        </div>

        <div class="input-field">
            <p>Місто <span>*</span></p>
            <input type="text" name="city" value="<?= $fetch_edit['city']; ?>" class="box" required>
        </div>

        <div class="flex-btn">
            <button type="submit" name="edit_hall" class="btn">Зберегти зміни</button>
            <a href="view_hall.php" class="btn">Повернутись назад</a>
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