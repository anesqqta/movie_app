<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
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
    <!--  секція -->
    <div class="banner">
        <div class="detail">
            <h1>Про нас</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>про нас</span>
        </div>
    </div>

    <div class="who">
        <bov class="box-container">
            <div class="box">
                <div class="heading">
                    <span>Ми — boleto</span>
                    <h1>дізнайтеся про нас</h1>
                </div>
                <p>тут треба щось написати</p>
                <p>тут треба щось написати</p>
                <div class="flex-btn">
                    <a href="fetch_movie.php" class="btn">переглянути більше фільмів</a>
                    <a href="fetch_movie.php" class="btn">відвідайте наші списки</a>
                </div>
            </div>
            <div class="box">
                <img src="image/about01.png" class="img">
            </div>
        </bov>
    </div>












     
    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            <?php include 'js/user_script.js'; ?>
        </script>


        <?php include 'components/alert.php'; ?>
    </body>
</html>