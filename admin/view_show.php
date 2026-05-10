<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //видалення сеансу з бази даниих
    if (isset($_POST['delete'])) {
        $delete_id = $_POST['show_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM shows WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_show = $conn->prepare("DELETE FROM shows WHERE id = ?");
            $delete_show->execute([$delete_id]);
            $success_msg[] = 'Сеанс видалено';
        }else{
            $warning_msg[] = 'Сеанс уже видалено';
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
            <h1>Переглянути сеанси</h1>
            <p>Переглядайте список сеансів, їх розклад та керуйте даними у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути сеанси</span>
        </div>
    </div>

    <!-- секція перегляду сеансів-->
    <div class="hall-container">
        <div class="heading">
            <h1>Деталі сеансу</h1>
            <a href="add_show.php" class="btn">+</a>
        </div> 
        <div class="box-container">
            <?php
                $select_shows = $conn->prepare("SELECT * FROM shows");
                $select_shows->execute();

                if ($select_shows->rowCount() > 0) {
                    while($fetch_show = $select_shows->fetch(PDO::FETCH_ASSOC)) {
                        $movie_id = $fetch_show['movie_id'];

                        //отримати фільм
                        $select_movie = $conn->prepare("SELECT title FROM movies WHERE id = ?");
                        $select_movie->execute([$movie_id]);
                        $movie_title = $select_movie->fetchColumn();

                        //отримати назву залу
                        $hall_id = $fetch_show['hall_id'];
                        $select_hall = $conn->prepare("SELECT name FROM halls WHERE id = ?");
                        $select_hall->execute([$hall_id]);
                        $hall_title = $select_hall->fetchColumn();
            ?>
            <table cellspacing="0">
                <tr>
                    <th>Назва</th>
                    <th>Дата</th>
                    <th>Час</th>
                    <th>Зал</th>
                    <th>Кількість місць</th>
                    <th>Дія</th>
                </tr>
                <tr>
                    <td><?= $movie_title; ?></td>
                    <td><?= $hall_title; ?></td>
                    <td><?= date('d.m.Y', strtotime($fetch_show['show_date'])); ?></td>
                    <td><?= $fetch_show['show_time']; ?></td>
                    <td><?= $fetch_show['seat_no']; ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="show_id" value="<?= $fetch_show['id']; ?>">
                            <a href="edit_show.php?get_id=<?= $fetch_show['id']; ?>" class="btn">Редагувати</a>
                            <button type="submit" name="delete" onclick="return confirm('Видалити цей сеанс?');" class="btn">Видалити</button>
                        </form>
                    </td>
                </tr>
            </table>
            <?php 
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Сеанс ще не додано!<br><a href="add_show.php" class="btn">Додати сеанс</a></p>
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