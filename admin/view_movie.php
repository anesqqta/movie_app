<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
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
        <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Переглянути фільмів</h1>
            <p>Переглядайте додані фільми, редагуйте інформацію та керуйте каталогом кінопрем’єр у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути фільмів</span>
        </div>
    </div>

    <!-- секція перегляду фільмів-->
    <div class="show-movies">
        <div class="heading">
            <h1>Переглянути фільмів</h1>
            <a href="add_movie.php" class="btn">+</a>
        </div>
        <div class="box-container">
            <?php
                $select_movies = $conn->prepare("SELECT * FROM movies");
                $select_movies->execute();

                if ($select_movies->rowCount() > 0) {
                    while ($fetch_movie = $select_movies->fetch(PDO::FETCH_ASSOC)) {
            ?>  
            <form action="" method="post" class="box">
                <img src="../uploaded_files/<?= $fetch_movie['thumbnail']; ?>" class="img1">
                <div class="content">
                    <div><h3><?= $fetch_movie['title']; ?></h3></div>
                    <input type="hidden" name="movie_id" value="<?= $fetch_movie['id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Видалити цей фільм?');" class="btn">Видалити</button>
                    <a href="read_movie.php?get_id=<?= $fetch_movie['id']; ?>" class="btn">Читати</a>
                    <a href="edit_movie.php?get_id=<?= $fetch_movie['id']; ?>" class="btn">Редагувати<i class="bx bxs-edit"></i></a>
                </div>
            </form> 
            <?php 
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>no movie added yet!<br><a href="add_movie.php" class="btn">Додати фільм</a></p>
                    </div>
                    ';
                }
            ?>
        </div>
    </div>
    


    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>