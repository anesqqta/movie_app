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
        header('location:view_show.php');
        exit();
    }

    if (isset($_POST['edit_show'])) {
        $movie_id = filter_var($_POST['movie_id'], FILTER_SANITIZE_STRING);
        $hall_id = filter_var($_POST['hall_id'], FILTER_SANITIZE_STRING);
        $show_date = filter_var($_POST['show_date'], FILTER_SANITIZE_STRING);
        $show_time = filter_var($_POST['show_time'], FILTER_SANITIZE_STRING);
        $seat_no = filter_var($_POST['seat_no'], FILTER_SANITIZE_STRING);

        $update_show = $conn->prepare("
            UPDATE shows 
            SET movie_id = ?, hall_id = ?, show_date = ?, show_time = ?, seat_no = ? 
            WHERE id = ?
        ");
        $update_show->execute([$movie_id, $hall_id, $show_date, $show_time, $seat_no, $edit_id]);

        $success_msg[] = 'Сеанс оновлено!';
    }

    $edit_query = $conn->prepare("SELECT * FROM shows WHERE id = ?");
    $edit_query->execute([$edit_id]);

    if ($edit_query->rowCount() > 0) {
        $fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC);
    }else{
        header('location:view_show.php');
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
        <h1>Редагувати сеанс</h1>
        <p>Оновіть дані сеансу, дату, час, зал та кількість доступних місць</p>
        <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Редагувати сеанс</span>
    </div>
</div>

<div class="form-container">
    <form action="" method="post" class="login">
        <h3>Редагувати сеанс</h3>

        <div class="input-field">
            <p>Фільм <span>*</span></p>
            <select name="movie_id" class="box" required>
                <?php
                    $select_movie = $conn->prepare("SELECT * FROM movies WHERE status = ?");
                    $select_movie->execute(['active']);

                    while($fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC)){
                        $selected = ($fetch_movie['id'] == $fetch_edit['movie_id']) ? 'selected' : '';
                ?>
                <option value="<?= $fetch_movie['id']; ?>" <?= $selected; ?>>
                    <?= $fetch_movie['title']; ?>
                </option>
                <?php } ?>
            </select>
        </div>

        <div class="input-field">
            <p>Зал <span>*</span></p>
            <select name="hall_id" class="box" required>
                <?php
                    $select_hall = $conn->prepare("SELECT * FROM halls WHERE status = ?");
                    $select_hall->execute(['active']);

                    while($fetch_hall = $select_hall->fetch(PDO::FETCH_ASSOC)){
                        $selected = ($fetch_hall['id'] == $fetch_edit['hall_id']) ? 'selected' : '';
                ?>
                <option value="<?= $fetch_hall['id']; ?>" <?= $selected; ?>>
                    <?= $fetch_hall['name']; ?>
                </option>
                <?php } ?>
            </select>
        </div>

        <div class="input-field">
            <p>Дата сеансу <span>*</span></p>
            <input type="date" name="show_date" value="<?= $fetch_edit['show_date']; ?>" min="<?= date('Y-m-d'); ?>" class="box" required>
        </div>

        <div class="input-field">
            <p>Час сеансу <span>*</span></p>
            <input type="time" name="show_time" value="<?= $fetch_edit['show_time']; ?>" class="box" required>
        </div>

        <div class="input-field">
            <p>Кількість доступних місць <span>*</span></p>
            <input type="number" name="seat_no" value="<?= $fetch_edit['seat_no']; ?>" class="box" required>
        </div>

        <div class="flex-btn">
            <button type="submit" name="edit_show" class="btn">Зберегти зміни</button>
            <a href="view_show.php" class="btn">Повернутись назад</a>
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