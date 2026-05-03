<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //видалення зали з бази даниих
    if (isset($_POST['delete'])) {
        $delete_id = $_POST['hall_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM halls WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_hall = $conn->prepare("DELETE FROM halls WHERE id = ?");
            $delete_hall->execute([$delete_id]);
            $success_msg[] = 'Зал видалено';
        }else{
            $warning_msg[] = 'Зал уже видалено';
        }
    }

    //редагування 
    if (isset($_POST['edit_hall'])) {
        $update_id = $_POST['update_id'];
        $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);

        $hall_name = $_POST['hall_name'];
        $hall_name = filter_var($hall_name, FILTER_SANITIZE_STRING);
        
        $hall_location = $_POST['location'];
        $hall_location = filter_var($hall_location, FILTER_SANITIZE_STRING);

        $city = $_POST['city'];
        $city = filter_var($city, FILTER_SANITIZE_STRING);

        $update_hall = $conn->prepare("UPDATE halls SET name = ?, location = ?, city = ? WHERE id = ?");
        $update_hall->execute([$hall_name, $hall_location, $city, $update_id]);

        $success_msg[] = 'Зал оновлено!';
        
        header('location:view_hall.php');
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
            <h1>Переглянути зали</h1>
            <p>Переглядайте список кінозалів, їх характеристики та керуйте даними у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути зали</span>
        </div>
    </div>

    <!-- секція перегляду залів-->
    <div class="hall-container">
        <div class="heading">
            <h1>Деталі залу</h1>
            <a href="add_hall.php" class="btn">+</a>
        </div>
        <div class="box-container">
            <?php
                $select_hall = $conn->prepare("SELECT * FROM halls");
                $select_hall->execute();

                if ($select_hall->rowCount() > 0) {
                    while($fetch_hall = $select_hall->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <table cellspacing="0">
                <tr>
                    <th>Назва</th>
                    <th>Розташування</th>
                    <th>Місто</th>
                    <th>Дія</th>
                </tr>
                <tr>
                    <td><?= $fetch_hall['name']; ?></td>
                    <td><?= $fetch_hall['location']; ?></td>
                    <td><?= $fetch_hall['city']; ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="hall_id" value="<?= $fetch_hall['id']; ?>">
                            <a href="view_hall.php?get_id=<?= $fetch_hall['id']; ?>" class="btn">Редагувати</a>
                            <button type="submit" name="delete" onclick="return confirm('Видалити цей зал?');" class="btn">Видалити</button>
                        </form>
                    </td>
                </tr>
            </table>
            <?php 
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Зал ще не додано!<br><a href="add_hall.php" class="btn">Додати зал</a></p>
                    </div>
                    ';
                }
            ?>
        </div>
    </div>

    <div class="update-container">
        <?php
            if (isset($_GET['get_id'])) {
                $edit_id = $_GET['get_id'];
                $edit_query = $conn->prepare("SELECT * FROM halls WHERE id = ?");
                $edit_query->execute([$edit_id]);

                if ($edit_query->rowCount() > 0) {
                    while($fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="update-container">
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="login">
                    <h3>Редагувати зал</h3>
                    <input type="hidden" name="update_id" value="<?= $fetch_edit['id']; ?>">    
                    <div class="input-field">
                        <p>Назва залу <span>*</span></p>
                        <input type="text" name="hall_name" maxlength="100" placeholder="<?= $fetch_edit['name']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Розташування залу <span>*</span></p>
                        <input type="text" name="location" maxlength="100" placeholder="<?= $fetch_edit['location']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Місто <span>*</span></p>
                        <input type="text" name="city" maxlength="100" placeholder="<?= $fetch_edit['city']; ?>" class="box">
                    </div>
                    <button type="submit" name="edit_hall" class="btn">Редагувати зал</button>
                </form>
            </div>
        </div>
        <?php 
                    }
                }
                echo "<script>document.querySelector('.update-container').style.display='block'</script>";
            }
        ?>
    </div>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>