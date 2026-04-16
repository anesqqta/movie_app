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
    <!-- головна секція -->
    <div class="home-section">
        <div class="slider">
            <div class="slider__slider slide1">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>Тор любов і грім</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 20хв</p>
                        <p>рік випуску : 20 травня 2026</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська | телугу | таймільська</p>
                        <p>пригода | бойовик | комедія | наукова фантастика</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide2">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>якесь кіно</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 20хв</p>
                        <p>рік випуску : 20 травня 2026</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська | телугу | таймільська</p>
                        <p>пригода | бойовик | комедія | наукова фантастика</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide3">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>якесь кіно</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 20хв</p>
                        <p>рік випуску : 20 травня 2026</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська | телугу | таймільська</p>
                        <p>пригода | бойовик | комедія | наукова фантастика</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide4">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>якесь кіно</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 20хв</p>
                        <p>рік випуску : 20 травня 2026</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська | телугу | таймільська</p>
                        <p>пригода | бойовик | комедія | наукова фантастика</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide5">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>якесь кіно</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 20хв</p>
                        <p>рік випуску : 20 травня 2026</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська | телугу | таймільська</p>
                        <p>пригода | бойовик | комедія | наукова фантастика</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->
             
            <div class="left-arrow"><i class="bx bxs-left-arrow"></i></div>
            <div class="right-arrow"><i class="bx bxs-right-arrow"></i></div>
        </div>
    </div>

    <!-- друга секція -->
    <div class="counter">
        <div class="heading">
            <span>Швидкі факти</span>
            <h1>Цікаві факти</h1>
            <p>Щороку ми транслюємо тисячі годин захопливих історій, перетворюючи звичайний перегляд на незабутню подорож у світ кіно.</p>
        </div>
        <div class="box-container">
            <div class="item">
                <img src="image/h-icon.png">
                <h1><span class="count" data-number="300"></span>M+</h1>
                <p>читачів</p>
            </div>
            <div class="item">
                <img src="image/h-icon0.png">
                <h1><span class="count" data-number="100"></span>+</h1>
                <p>учасників</p>
            </div>
            <div class="item">
                <img src="image/h-icon1.png">
                <h1><span class="count" data-number="690"></span>+</h1>
                <p>читачів</p>
            </div>
            <div class="item">
                <img src="image/h-icon.png">
                <h1><span class="count" data-number="9060"></span>+</h1>
                <p>підписників</p>
            </div>
        </div>
    </div>

    <div class="box-container">
        <div class="sub-banner">
            <div class="overlay"></div>
            <img src="image/banner_1.jpg">
        </div>
        <div class="sub-banner">
            <div class="overlay"></div>
            <img src="image/banner_2.jpg">
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