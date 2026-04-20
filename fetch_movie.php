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

    <div class="banner">
        <div class="detail">
            <h1>Всі фільми</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>всі фільми</span>
        </div>
    </div>

    <!-- секція всіх фільмів -->
    <div class="show-movie">
        <div class="heading">
            <span>movie listings</span>
            <h1>movies listings in boleto</h1>
        </div>
        <div class="box-container">
            <?php
                $select_movies = $conn->prepare("SELECT * FROM movies WHERE status = ?");
                $select_movies->execute(['active']);

                if($select_movies->rowCount() > 0){
                    while($fetch_movie = $select_movies->fetch(PDO::FETCH_ASSOC)){
                        
            ?>
            <form action="" method="post" class="box">
                <img src="uploaded_files/<?= $fetch_movie['thumbnail'] ?>"  class="img1">

                <div class="content">
                    <div class="button">
                        <div><h3><?= $fetch_movie['title'] ?></h3></div>
                        <div>
                            <button type="submit" name="add_to_wishlist"><img src="image/heart.png"></button>
                            <a href="<?= $fetch_movie['trailer_url'] ?>" class="bx bx-play"></a>
                            <a href="view_movie.php?pid=<?= $fetch_movie['id'] ?>" class="bx bx-show"></a>
                        </div>
                    </div>
                    <div class="rate">
                        <p><span><img src="image/tomato.png"></span>88%</p>
                        <p><img src="image/cake.png">98%</p>
                    </div>
                    <input type="hidden" name="movie_id" value="<?= $fetch_movie['id']; ?>">
                    <a href="select-language.php?movie_id=<?=$fetch_movie['id']; ?>" class="btn">бронювати квиток</a>
                </div>
            </form>
            <?php 
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>no movie added yet!</p>
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