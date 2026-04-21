<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    //видалити товар зі списку бажань
    if (isset($_POST['delete_item'])) {
        $wishlist_id = $_POST['wishlist_id'];
        $wishlist_id = filter_var($wishlist_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM wishlist WHERE id = ?");
        $verify_delete->execute([$wishlist_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_wishlist = $conn->prepare("DELETE FROM wishlist WHERE id = ?");
            $delete_wishlist->execute([$wishlist_id]);
            $success_msg[] = 'Елемент списку бажань видалено';
        }else{
            $warning_msg[] = 'Елемент зі списку бажань вже видалено';
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
            <h1>Список бажаного</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>список бажаного</span>
        </div>
    </div>

    <!-- секція списку бажаного -->
     <div class="show-movie">
        <div class="heading">
            <h1>Фільми, додані до вашого списку бажань</h1>
        </div>
        <div class="box-container">
            <?php
                $select_wishlist = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ?");
                $select_wishlist->execute([$user_id]);
                
                if ($select_wishlist->rowCount() > 0) {
                    while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){
                        $search_movies = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                        $search_movies->execute([$fetch_wishlist['movie_id']]);

                        if ($search_movies->rowCount() > 0) {
                            $fetch_movies = $search_movies->fetch(PDO::FETCH_ASSOC);
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="wishlist_id" value="<?= $fetch_wishlist['id']; ?>">
                <img src="uploaded_files/<?= $fetch_movies['thumbnail']; ?>" class="img1">
                <div class="content">
                    <div class="button">
                        <div><h3><?= $fetch_movies['title']; ?></h3></div>
                        <div>
                            <button type="submit" name="delete_item"><img src="image/heart.png"></button>
                            <a href="<?= $fetch_movies['trailer_url'] ?>" class="bx bx-play"></a>
                            <a href="view_movie.php?pid=<?= $fetch_movies['id'] ?>" class="bx bxs-show"></a>
                        </div>
                    </div>
                    <div class="rate">
                        <p><span><img src="image/tomato.png"></span>88%</p>
                        <p><img src="image/cake.png">98%</p>
                    </div>
                    <input type="hidden" name="movie_id" value="<?= $fetch_movies['id']; ?>">
                    <a href="select-language.php?movie_id=<?= $fetch_movies['id']; ?>" class="btn">бронювати квиток</a>
                </div>
            </form>
            <?php
                        }
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>ще не додано жодного фільму!</p>
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