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

    //редагування 
    if (isset($_POST['edit_show'])) {
        $update_id = $_POST['update_id'];
        $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);

        $movie_id = $_POST['movie_id'];
        $movie_id = filter_var($movie_id, FILTER_SANITIZE_STRING);
        
        $hall_id = $_POST['hall_id'];
        $hall_id = filter_var($hall_id, FILTER_SANITIZE_STRING);

        $show_date = $_POST['show_date'];
        $show_date = filter_var($show_date, FILTER_SANITIZE_STRING);

        $show_time = $_POST['show_time'];
        $show_time = filter_var($show_time, FILTER_SANITIZE_STRING);

        $seat_no = $_POST['seat_no'];
        $seat_no = filter_var($seat_no, FILTER_SANITIZE_STRING);

        $update_show = $conn->prepare("UPDATE shows SET movie_id = ?, hall_id = ?, show_date = ?, show_time = ?, seat_no = ? WHERE id = ?");
        $update_show->execute([$movie_id, $hall_id, $show_date, $show_time, $seat_no, $update_id]);

        $success_msg[] = 'Сеанс оновлено!';
        
        header('location:view_show.php');
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
                            <a href="view_show.php?get_id=<?= $fetch_show['id']; ?>" class="btn">Редагувати</a>
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

    <div class="update-container">
        <?php
            if (isset($_GET['get_id'])) {
                $edit_id = $_GET['get_id'];
                $edit_query = $conn->prepare("SELECT * FROM shows WHERE id = ?");
                $edit_query->execute([$edit_id]);

                if ($edit_query->rowCount() > 0) {
                    while($fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC)) {
                        //отримати фільм
                        $movie_id = $fetch_edit['movie_id'];
                        $select_movie = $conn->prepare("SELECT title FROM movies WHERE id = ?");
                        $select_movie->execute([$movie_id]);
                        $movie_title = $select_movie->fetchColumn();

                        //отримати назву залу
                        $hall_id = $fetch_edit['hall_id'];
                        $select_hall = $conn->prepare("SELECT name FROM halls WHERE id = ?");
                        $select_hall->execute([$hall_id]);
                        $hall_title = $select_hall->fetchColumn();

                        //отримання часу сеансу
                        $show_time_id = $fetch_edit['show_time'];
                        $select_time = $conn->prepare("SELECT time FROM show_time WHERE id = ?");
                        $select_time->execute([$show_time_id]);
                        $show_time = $select_time->fetchColumn();
        ?>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data" class="login">
                <input type="hidden" name="update_id" value="<?= $fetch_edit['id']; ?>">
                <h3>Редагувати сеанс</h3>
                <div class="input-field">
                    <p>Фільм <span>*</span></p>
                    <select name="movie_id" class="box" required>
                        <option selected disabled value="<?= $movie_title; ?>"><?= $movie_title; ?></option>
                        <?php
                            $select_movie = $conn->prepare("SELECT * FROM movies WHERE status = ?");
                            $select_movie->execute(['active']);

                            if ($select_movie->rowCount() > 0) {
                                while($fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <option value="<?= $fetch_movie['id']; ?>" style="background: #ccc"><?= $fetch_movie['title']; ?></option>
                        <?php                 
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="input-field">
                    <p>Зал <span>*</span></p>
                    <select name="hall_id" class="box" required>
                        <option selected disabled value="<?= $hall_title; ?>"><?= $hall_title; ?></option>
                        <?php
                            $select_hall = $conn->prepare("SELECT * FROM halls WHERE status = ?");
                            $select_hall->execute(['active']);

                            if ($select_hall->rowCount() > 0) {
                                while($fetch_hall = $select_hall->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <option value="<?= $fetch_hall['id']; ?>" style="background: #ccc"><?= $fetch_hall['name']; ?></option>
                        <?php                 
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="input-field">
                    <p>Дата сеансу <span>*</span></p>
                    <input type="date" name="show_date" maxlength="50" value="<?= $fetch_edit['show_date']; ?>" min="<?php echo date('Y-m-d') ?>" class="box">
                </div>
                <div class="input-field">
                    <p>Час сеансу <span>*</span></p>
                    <select name="show_time" class="box" required>
                        <option selected disabled value="<?= $show_time; ?>">Виберіть час сеансу</option>
                        <?php
                            $select_show_time = $conn->prepare("SELECT * FROM show_time");
                            $select_show_time->execute();

                            if ($select_show_time->rowCount() > 0) {
                                while($fetch_show_time = $select_show_time->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <option value="<?= $fetch_show_time['id']; ?>" style="background: #ccc"><?= $fetch_show_time['time']; ?></option>
                        <?php                 
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="input-field">
                    <p>Кількість доступних місць <span>*</span></p>
                    <input type="text" name="seat_no" maxlength="50" value="<?= $fetch_edit['seat_no']; ?>" class="box">
                </div>
                <button type="submit" name="edit_show" class="btn">Редагувати сеанс</button>
            </form>
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