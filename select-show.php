<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    session_start();
    $movie_id = $_SESSION['booking']['movie_id'];

    $language = $_SESSION['booking']['language'];
    $formate = $_SESSION['booking']['formate'];
    $time = $_SESSION['booking']['time'];
    $date = $_SESSION['booking']['date'];

    $movie_id = $_SESSION['booking']['movie_id'];

    //отримати назву фільму та банер
    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        while($fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC)) {
            $fetch_img = $fetch_movie['poster'];
            $movie_name = $fetch_movie['title'];
            $movie_language = $fetch_movie['language'];
            $duration = $fetch_movie['duration'];
            $release_year = $fetch_movie['release_year'];
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
        <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Виберіть сеанс</h1>
            <p>Оберіть зручний час показу фільму та забронюйте квитки на найкращі місця у кінозалі</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>виберіть сеанс</span>
        </div>
    </div>

    <!-- секція вибору сеансу -->
    <div class="show-container">
        <img src="uploaded_files/<?= $fetch_img; ?>">
        <div class="movie-detail">
            <h1>Фільм : <?= $movie_name; ?></h1>
            <p>Мова : <?= $movie_language; ?></p>
            <p>Тривалість : <?= $duration; ?></p>
            <p>Рік випуску : <?= $release_year; ?></p>
        </div>
        <div class="head">
            <p>Мова : <?= $language; ?></p>
            <p>Формат : <?= $formate; ?></p>
            <p>Час : <?= $time; ?></p>
            <p>Дата : <?= $date; ?></p>
        </div>
        <?php
    $select_show = $conn->prepare("
        SELECT shows.*, halls.name, halls.city, halls.location
        FROM shows
        JOIN halls ON shows.hall_id = halls.id
        WHERE shows.movie_id = ?
        AND shows.show_time = ?
        AND shows.show_date = ?
        AND halls.status = ?
    ");
    $select_show->execute([$movie_id, $time, $date, 'active']);

    if ($select_show->rowCount() > 0) {
        while($fetch_show = $select_show->fetch(PDO::FETCH_ASSOC)){
?>
<form action="save-step2.php" method="post">
    <input type="hidden" name="show_id" value="<?= $fetch_show['id']; ?>">

    <div class="detail">
        <p>Назва залу : <span><?= $fetch_show['name']; ?></span></p>
        <p>Розташування : <span><?= $fetch_show['location']; ?></span></p>
        <p>Місто : <span><?= $fetch_show['city']; ?></span></p>

        <button type="submit" class="btn">Вибрати</button>
    </div>
</form>
<?php
        }
    }else{
        echo '
        <div class="empty">
            <p>Немає доступних сеансів для цього фільму!</p>
        </div>
        ';
    }
?>
    </div>

    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            <?php include 'js/user_script.js'; ?>
        </script>

        <?php include 'components/alert.php'; ?>
    </body>
</html>