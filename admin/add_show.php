<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['add_show'])) {

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

        $insert_show = $conn->prepare("INSERT INTO shows (movie_id, hall_id, show_date, show_time, seat_no ) VALUES (?, ?, ?, ?, ?)");
        $insert_show->execute([$movie_id, $hall_id, $show_date, $show_time, $seat_no]);

        $success_msg[] = 'Сеанс успішно додано';
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
            <h1>Додавання сеансу</h1>
            <p>Створюйте нові сеанси, обирайте фільм, зал, дату та час показу для формування розкладу</p>
            <span><a href="view_show.php">Переглянути сеанси</a><i class="bx bxs-right-arrow-alt"></i>Додавання сеансу</span>
        </div>
    </div>

    <!-- секція додавання сеансів-->
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Додати сеанс</h3>
            <div class="input-field">
                <p>Фільм <span>*</span></p>
                <select name="movie_id" class="box" required>
                    <option selected disabled>Виберіть фільм</option>
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
                    <option selected disabled>Виберіть зал</option>
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
                <input type="date" name="show_date" required maxlength="50" placeholder="Введіть дату сеансу" min="<?php echo date('Y-m-d') ?>" class="box">
            </div>
            <div class="input-field">
                <p>Час сеансу <span>*</span></p>
                <select name="show_time" class="box" required>
                    <option selected disabled>Виберіть час сеансу</option>
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
                <input type="text" name="seat_no" required maxlength="50" placeholder="Введіть загальну кількість доступних місць" class="box">
            </div>
            <button type="submit" name="add_show" class="btn">Додати сеанс</button>
        </form>
    </div>
     
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>