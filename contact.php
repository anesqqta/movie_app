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

    <div class="banner">
        <div class="detail">
            <h1>Наші контакти</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Контакти</span>
        </div>
    </div>

    <!-- секція сервісу -->
    <div class="service">
        <div class="heading">
            <h1>Наш сервіс</h1>
            <p>написати щось</p>
        </div>
        <div class="box-container">
            <div class="box">
                <img src="image/s-icon (2).png">
                <div>
                    <h1>Легке скасування</h1>
                    <p>написати щось</p>
                </div>
            </div>

            <div class="box">
                <img src="image/s-icon (3).png">
                <div>
                    <h1>Гарантія повернення грошей</h1>
                    <p>написати щось</p>
                </div>
            </div>

            <div class="box">
                <img src="image/s-icon (1).png">
                <div>
                    <h1>Онлайн-підтримка 24/7</h1>
                    <p>написати щось</p>
                </div>
            </div>
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