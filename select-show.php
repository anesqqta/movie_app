<?php
    include 'components/connect.php';

    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    session_start();

    if (!isset($_SESSION['booking']['movie_id'])) {
        header('location:fetch_movie.php');
        exit();
    }

    $movie_id = $_SESSION['booking']['movie_id'];
    $language = $_SESSION['booking']['language'];
    $formate = $_SESSION['booking']['formate'];

    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        $fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC);
        $fetch_img = $fetch_movie['poster'];
        $movie_name = $fetch_movie['title'];
        $movie_language = $fetch_movie['language'];
        $duration = $fetch_movie['duration'];
        $release_year = $fetch_movie['release_year'];
    }else{
        die('Фільм не знайдено');
    }

    $today = date('Y-m-d');

    $select_dates = $conn->prepare("
        SELECT DISTINCT show_date 
        FROM shows 
        WHERE movie_id = ? 
        AND show_date >= ? 
        ORDER BY show_date ASC
    ");
    $select_dates->execute([$movie_id, $today]);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
    <title>BOLETO</title>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="banner">
    <div class="detail">
        <h1>Виберіть сеанс</h1>
        <p>Оберіть зручну дату та час показу фільму</p>
        <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>виберіть сеанс</span>
    </div>
</div>

<div class="show-container">
    <img src="uploaded_files/<?= $fetch_img; ?>">

    <div class="movie-detail">
        <h1>Фільм : <?= $movie_name; ?></h1>
        <p>Мова фільму : <?= $movie_language; ?></p>
        <p>Тривалість : <?= $duration; ?></p>
        <p>Рік випуску : <?= $release_year; ?></p>
    </div>

    <div class="head">
        <p>Обрана мова : <?= $language; ?></p>
        <p>Обраний формат : <?= $formate; ?></p>
    </div>

    <?php
        if ($select_dates->rowCount() > 0) {
            while($fetch_date = $select_dates->fetch(PDO::FETCH_ASSOC)){
                $show_date = $fetch_date['show_date'];
    ?>

    <div class="show-date-box">
        <h2><?= date('d.m.Y', strtotime($show_date)); ?></h2>

        <div class="show-time-list">
            <?php
                $select_show = $conn->prepare("
                    SELECT shows.*, halls.name, halls.city, halls.location 
                    FROM shows 
                    JOIN halls ON shows.hall_id = halls.id 
                    WHERE shows.movie_id = ? 
                    AND shows.show_date = ? 
                    AND halls.status = ?
                    ORDER BY shows.show_time ASC
                ");
                $select_show->execute([$movie_id, $show_date, 'active']);

                if ($select_show->rowCount() > 0) {
                    while($fetch_show = $select_show->fetch(PDO::FETCH_ASSOC)){
            ?>

            <form action="save-step2.php" method="post" class="show-time-card">
                <input type="hidden" name="show_id" value="<?= $fetch_show['id']; ?>">
                <input type="hidden" name="show_date" value="<?= $fetch_show['show_date']; ?>">
                <input type="hidden" name="show_time" value="<?= $fetch_show['show_time']; ?>">

                <div class="detail">
                    <h3><?= date('H:i', strtotime($fetch_show['show_time'])); ?></h3>
                    <p>Зал : <span><?= $fetch_show['name']; ?></span></p>
                    <p>Локація : <span><?= $fetch_show['location']; ?></span></p>
                    <p>Місто : <span><?= $fetch_show['city']; ?></span></p>

                    <button type="submit" class="btn">Вибрати</button>
                </div>
            </form>

            <?php
                    }
                }else{
                    echo '<p class="empty">На цю дату немає доступних сеансів</p>';
                }
            ?>
        </div>
    </div>

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