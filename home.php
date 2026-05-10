<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    include 'components/add_wishlist.php';

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
                    <h1>забронюйте квиток <br>на <span>Тор: кохання і грім</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 20хв </p>
                        <p>рік випуску : 2022</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська </p>
                        <p> комедія | мелодрама | фантастика | фентезі | бойовик | пригоди</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide2">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>Месники</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 23хв </p>
                        <p> рік випуску : 2021</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська </p>
                        <p> фантастика | бойовик | пригоди</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide3">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>Рапунцель</span></h1>
                    <div class="detail">
                        <p>тривалість : 1год : 40хв </p>
                        <p> рік випуску : 2010</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська </p>
                        <p> дитячий | мюзикл</p>
                    </div>
                    <a href="fetch_movie.php" class="btn">забронювати квиток зараз</a>
                </div>
            </div>
            <!-- кінець slide -->

            <div class="slider__slider slide5">
                <div class="overlay"></div>
                <div class="slider-detail">
                    <h1>забронюйте квиток <br>на <span>Вартові галактики</span></h1>
                    <div class="detail">
                        <p>тривалість : 2год : 1хв </p>
                        <p><br>рік випуску : 2014</p>
                    </div>
                    <div class="detail">
                        <p>мова : українська | англійська </p>
                        <p> фантастика | бойовик | пригоди | комедія</p>
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

    <!-- третя секція -->
    <div class="show-movie">
        <div class="heading">
            <span>Фільми</span>
            <h1>найкращі фільми в кінотеатрах</h1>
            <p>у кінотеатрі Boleto & театрах</p>
        </div>
        <div class="box-container">
            <?php
                $select_movies = $conn->prepare("SELECT * FROM movies WHERE status = ?");
                $select_movies->execute(['active']);

                if ($select_movies->rowCount() > 0) {
                    while($fetch_movie = $select_movies->fetch(PDO::FETCH_ASSOC)){
                        // code ...
            ?>
            <form action="" method="post" class="box">
                <img src="uploaded_files/<?= $fetch_movie['thumbnail']; ?>" class="img1">
                <div class="content">
                    <div class="button">
                        <div><h3><?= $fetch_movie['title']; ?></h3></div>
                        <div>
                            <button type="submit" name="add_to_wishlist"><img src="image/heart.png"></button>
                            <a href="<?= $fetch_movie['trailer_url']; ?>" class="bx bx-play"></a>
                            <a href="view_movie.php?pid=<?= $fetch_movie['id']; ?>" class="bx bxs-show"></a>
                        </div>
                    </div>
                    <div class="rate">
                        <p><span><img src="image/tomato.png"></span>88%</p>
                        <p><img src="image/cake.png">88%</p>
                    </div>
                    <input type="hidden" name="movie_id" value="<?= $fetch_movie['id']; ?>">
                    <a href="select-language.php?movie_id=<?=$fetch_movie['id']; ?>" class="btn">бронювати квиток</a>
                </div>
            </form> 
            <?php
                    }
                }
                else{
                    echo '
                    <div class="empty">
                        <p>Фільм ще не додано!</p>
                    </div>
                    ';
                }
            ?>
            
        </div>
    </div>

    <!-- четверта секція -->
     <div class="about">
        <div class="box-container">
            <div class="detail">
                <div class="heading">
                    <span>Погляньте на</span>
                    <h1>Нашу філософія</h1>
                </div>
                <div class="img-box">
                    <div class="box">
                        <img src="image/a-icon1.png">
                        <h3>Чесність & Справедливість</h3>
                        <p>Ми забезпечуємо рівні умови для всіх користувачів: кожен відвідувач може переглянути актуальний каталог кінопрем’єр, обрати зручний сеанс і забронювати квиток без прихованих умов.</p>
                    </div>
                    <div class="box">
                        <img src="image/a-icon2.png">
                        <h3>Ясність & Прозорість</h3>
                        <p>Уся інформація про фільми, розклад сеансів, доступні місця та бронювання подається зрозуміло й відкрито, щоб користувач міг швидко прийняти рішення.</p>
                    </div>
                    <div class="box">
                        <img src="image/a-icon3.png">
                        <h3>Орієнтація на клієнта</h3>
                        <p>Система створена для зручності глядачів: швидкий пошук фільмів, перегляд трейлерів, вибір місць у залі та автоматизоване бронювання квитків.</p>
                    </div>
                </div>
            </div>
        </div>
     </div>

    <!-- п'ята секція -->
     <div class="team">
        <div class="heading">
            <span>познайомтеся з нашими спеціалістами</span>
            <h1>Члени команди</h1>
            <p>Наша команда відповідає за наповнення каталогу кінопрем’єр, оновлення розкладу сеансів, підтримку користувачів та стабільну роботу системи бронювання квитків.</p>
        </div>
        <div class="box-container">
            <div class="box">
                <img src="image/team.webp" class="img">
                <div class="content">
                    <h2>Семенюк Анастасія</h2>
                    <p>адміністратор каталогу</p>
                    <div class="icons">
                        <i class="bx bxl-facebook"></i>
                        <i class="bx bxl-instagram-alt"></i>
                        <i class="bx bxl-linkedin"></i>
                        <i class="bx bxl-twitter"></i>
                        <i class="bx bxl-pinterest-alt"></i>
                    </div>
                </div>
            </div>
            <!-- кінець першої карточки людини -->

            <div class="box">
                <img src="image/team0.webp" class="img">
                <div class="content">
                    <h2>Бордун Владислав</h2>
                    <p>менеджер бронювань</p>
                    <div class="icons">
                        <i class="bx bxl-facebook"></i>
                        <i class="bx bxl-instagram-alt"></i>
                        <i class="bx bxl-linkedin"></i>
                        <i class="bx bxl-twitter"></i>
                        <i class="bx bxl-pinterest-alt"></i>
                    </div>
                </div>
            </div>
            <!-- кінець другої карточки людини -->

            <div class="box">
                <img src="image/team1.webp" class="img">
                <div class="content">
                    <h2>Семенюк Вікторія</h2>
                    <p>спеціаліст підтримки</p>
                    <div class="icons">
                        <i class="bx bxl-facebook"></i>
                        <i class="bx bxl-instagram-alt"></i>
                        <i class="bx bxl-linkedin"></i>
                        <i class="bx bxl-twitter"></i>
                        <i class="bx bxl-pinterest-alt"></i>
                    </div>
                </div>
            </div>
            <!-- кінець третьої карточки людини -->

            <div class="box">
                <img src="image/team2.webp" class="img">
                <div class="content">
                    <h2>Самофал Денис</h2>
                    <p>технічний адміністратор</p>
                    <div class="icons">
                        <i class="bx bxl-facebook"></i>
                        <i class="bx bxl-instagram-alt"></i>
                        <i class="bx bxl-linkedin"></i>
                        <i class="bx bxl-twitter"></i>
                        <i class="bx bxl-pinterest-alt"></i>
                    </div>
                </div>
            </div>
            <!-- кінець четвретої карточки людини -->
        </div>
     </div>

    <!-- розсилка -->
    <div class="newletter">
        <div class="overlay"></div>
        <div class="box-detail">
            <span>Підпишіться на Boleto</span>
            <h1>щоб отримати ексклюзивні переваги</h1>
            <p>Ми поважаємо вашу конфіденційність, тому ніколи не передаємо вашу інформацію</p>
            <div class="input-fielt">
                <input type="mail" name="" placeholder="введіть ваш email..." class="box"><br>
                <button type="" class="btn">підпишись</button>
            </div>
            <div class="icons">
                <i class="bx bxl-facebook"></i>
                <i class="bx bxl-instagram-alt"></i>
                <i class="bx bxl-linkedin"></i>
                <i class="bx bxl-twitter"></i>
                <i class="bx bxl-pinterest-alt"></i>
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