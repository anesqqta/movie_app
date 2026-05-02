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
            <h1>Переглянути акторів</h1>
            <p>Переглядайте список акторів, їхню інформацію та керуйте даними у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути акторів</span>
        </div>
    </div>

    <!-- секція перегляду акторів-->
    <div class="gride">
        <div class="heading">
            <h1>Всі актори</h1>
            <a href="add_actors.php" class="btn">+</a>
        </div>
        <div class="box-container">
            <?php
                $select_actors = $conn->prepare("SELECT * FROM actors");
                $select_actors->execute();

                if ($select_actors->rowCount() > 0) {
                    while($fetch_actors = $select_actors->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="actor_id" value="<?= $fetch_actors['id']; ?>">
                <img src="../uploaded_files/actors/<?= $fetch_actors['image']; ?>">
                <h2><?= $fetch_actors['name']; ?></h2>
                <span><?= $fetch_actors['role']; ?></span>
                <div class="flex-btn">
                    <button type="submit" name="delete" onclick="return confirm('Видалити цього актора?');" class="btn">Видалити</button>
                    <a href="edit_movie.php?get_id=<?= $fetch_actors['id']; ?>" class="btn">Редагувати</a>
                </div>
            </form>
            <?php 
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Актора ще не додано!<br><a href="add_actors.php" class="btn">Додати актора</a></p>
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