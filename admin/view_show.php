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
    <div class="hall-container" style="overflow-x: auto;">
        <?php
            $select_shows = $conn->prepare("
                SELECT shows.*, movies.title AS movie_title, halls.name AS hall_name
                FROM shows
                JOIN movies ON shows.movie_id = movies.id
                JOIN halls ON shows.hall_id = halls.id
                ORDER BY shows.show_date ASC, shows.show_time ASC
            ");
            $select_shows->execute();

            if ($select_shows->rowCount() > 0) {
        ?>

        <table cellspacing="0" style="width: 100%;">
            <tr>
                <th>Назва</th>
                <th>Дата</th>
                <th>Час</th>
                <th>Зал</th>
                <th>Кількість місць</th>
                <th>Дія</th>
            </tr>

            <?php while($fetch_show = $select_shows->fetch(PDO::FETCH_ASSOC)){ ?>
            <tr>
                <td><?= $fetch_show['movie_title']; ?></td>
                <td><?= date('d.m.Y', strtotime($fetch_show['show_date'])); ?></td>
                <td><?= $fetch_show['show_time']; ?></td>
                <td><?= $fetch_show['hall_name']; ?></td>
                <td><?= $fetch_show['seat_no']; ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="show_id" value="<?= $fetch_show['id']; ?>">
                        <a href="edit_show.php?get_id=<?= $fetch_show['id']; ?>" class="btn">Редагувати</a>
                        <button type="submit" name="delete" onclick="return confirm('Видалити цей сеанс?');" class="btn">Видалити</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <?php
            }else{
                echo '
                <div class="empty">
                    <p>Сеанс ще не додано!<br><a href="add_show.php" class="btn">Додати сеанс</a></p>
                </div>
                ';
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