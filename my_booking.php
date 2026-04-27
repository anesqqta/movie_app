<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
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
        <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Мої бронювання</h1>
            <p>Переглядайте свої активні та завершені бронювання, контролюйте квитки та плануйте наступні походи в кіно</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Мої бронювання</span>
        </div>
    </div>

    <!-- секція мої бронювання -->
    <div class="my-booking">
        <div class="heading">
            <h1>Мої бронювання</h1>
        </div>
        <div class="box-container">
            <?php 
                $select_booking = $conn->prepare("SELECT * FROM booking WHERE user_id = ?");
                $select_booking->execute([$user_id]);

                if ($select_booking->rowCount() > 0) {
                    while($fetch_booking = $select_booking->fetch(PDO::FETCH_ASSOC)){
                        $show_id = $fetch_booking['show_id'];

                        $select_show = $conn->prepare("SELECT movie_id FROM shows WHERE id = ?");
                        $select_show->execute([$show_id]);

                        $movie_id = $select_show->fetchColumn();

                        $select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                        $select_movie->execute([$movie_id]);

                        if ($select_movie->rowCount() > 0) {
                            while($fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC)) {
                                $fetch_img = $fetch_movie['thumbnail'];
                                $movie_name = $fetch_movie['title'];
                                $movie_duration = $fetch_movie['duration'];
                            }
                        }
            ?>
            <div class="box">
                <a href="view_booking.php?get_id= <?= $fetch_booking['id']; ?>">
                    <img src="uploaded_files/<?= $fetch_img; ?>">
                    <a href="download_ticket.php?booking_id=<?= $fetch_booking['id'] ?>" class="download"><i class="bx bx-download"></i></a>
                    <div class="detail">
                        <div class="content">
                            <div>
                                <h2><?= $movie_name; ?></h2>
                                <p><?= $movie_duration; ?></p>
                            </div>
                            <div>
                                <p>Кількість : <?= $fetch_booking['total_seat'] ?></p>
                                <p>Місце : <?= $fetch_booking['seat_details'] ?></p>
                            </div>
                        </div>
                        <a href="rating.php?get_id=<?= $fetch_booking['id']; ?>" class="btn">Дайте відгук</a>
                    </div>
                </a>
            </div>
            <?php
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Бронювання ще не додано!</p>
                    </div>
                    ';
                }
            ?>
        </div>
    </div>
    

    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            <?php include 'js/user_script.js'; ?>
        </script>

        <?php include 'components/alert.php'; ?>
    </body>
</html>