<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
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
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>