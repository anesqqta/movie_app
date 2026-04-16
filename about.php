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
            <h1>Про нас</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>про нас</span>
        </div>
    </div>

    <!-- секція дізнатися про нас -->
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

    <!-- секція допомоги -->
    <div class="help">
        <div class="container">
            <div class="heading">
                <span>Якість & Захоплення</span>
                <p>Якість та захоплення нашими послугами!</p>
                <p>Тут має бути написано про Якість & Захоплення</p>
            </div>
            <div class="box-container">
                <div class="box">
                    <img src="image/icon0.png">
                    <h1>100% безпечна оплата</h1>
                    <p>тут має бути щось написано про безпечну оплату</p>
                </div>
                <div class="box">
                    <img src="image/icon.png">
                    <h1>Бот-помічник</h1>
                    <p>тут має бути щось написано про бота-помічника</p>
                </div>
                <div class="box">
                    <img src="image/icon1.png">
                    <h1>Центр допомоги</h1>
                    <p>тут має бути щось написано про Центр допомоги</p>
                </div>
            </div>
        </div>
    </div>

    <!-- секція з лічильником -->
    <div class="counter">
        <div class="heading">
            <span>Швидкі факти</span>
            <h1>З популярних</h1>
            <p>Щороку ми транслюємо тисячі годин захопливих історій, перетворюючи звичайний перегляд на незабутню подорож у світ кіно.</p>
        </div>
        <div class="box-container">
            <div class="item">
                <img src="image/about-counter01.png">
                <h1><span class="count" data-number="300"></span>M+</h1>
                <p>Клієнтів</p>
            </div>
            <div class="item">
                <img src="image/about-counter02.png">
                <h1><span class="count" data-number="100"></span>+</h1>
                <p>Країни</p>
            </div>
            <div class="item">
                <img src="image/about-counter03.png">
                <h1><span class="count" data-number="690"></span>+</h1>
                <p>Міста</p>
            </div>
            <div class="item">
                <img src="image/about-counter04.png">
                <h1><span class="count" data-number="960"></span>+</h1>
                <p>Екрани</p>
            </div>
        </div>
    </div>

    <!-- секція з клієнтами -->
    <div class="testimonial-container">
        <div class="testimonial">
            <div class="heading">
                <span>Клієнти з</span>
                <h1>привідом для посмішки</h1>
            </div>
            <div class="container">
                <div class="testimonial-item active">
                    <i class="bx bxs-quote-right" id="quote"></i>
                    <img src="image/ourteam0.webp">
                    <h1>Джон Сміт</h1>
                    <p>тут має щось писати</p>
                </div>
                <!-- кінець одного slide -->

                <div class="testimonial-item">
                    <i class="bx bxs-quote-right" id="quote"></i>
                    <img src="image/ourteam.webp">
                    <h1>Джон Сміт</h1>
                    <p>тут має щось писати</p>
                </div>
                <!-- кінець одного slide -->

                <div class="testimonial-item">
                    <i class="bx bxs-quote-right" id="quote"></i>
                    <img src="image/ourteam1.webp">
                    <h1>Джон Сміт</h1>
                    <p>тут має щось писати</p>
                </div>
                <!-- кінець одного slide -->

                <div class="testimonial-item">
                    <i class="bx bxs-quote-right" id="quote"></i>
                    <img src="image/ourteam2.webp">
                    <h1>Алвена Сміт</h1>
                    <p>тут має щось писати</p>
                </div>
                <!-- кінець одного slide -->

                <div class="left-arrow" onclick="rightSlide()"><i class="bx bxs-left-arrow-alt"></i></div>
                <div class="right-arrow" onclick="leftSlide()"><i class="bx bxs-right-arrow-alt"></i></div>
            </div>
        </div>
    </div>


    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            //лічильник
            let count = document.querySelectorAll('.count');
            let arr = Array.from(count);

            arr.map(function(item){
                let startnumber = 0;

                function counterUp(){
                    startnumber++
                    item.innerHTML = startnumber

                    if (startnumber == item.dataset.number) {
                        clearInterval(stop)
                    }
                }
                let stop = setInterval(function(){
                    counterUp();
                }, 50)
            })

            let slide = document.querySelectorAll('.testimonial-item');
            let index = 0;

            function rightSlide(){
                slide[index].classList.remove('active');
                index = (index + 1) % slide.length;
                slide[index].classList.add('active');
            }

            function leftSlide(){
                slide[index].classList.remove('active');
                index = (index - 1 + slide.length) % slide.length;
                slide[index].classList.add('active');
            }
        </script>


        <?php include 'components/alert.php'; ?>
    </body>
</html>