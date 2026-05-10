<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //видалення фільму з бази даниих
    if (isset($_POST['delete'])) {
        $delete_id = $_POST['movie_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM movies WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $select_images = $conn->prepare("SELECT * FROM movies WHERE id = ?");
            $select_images->execute([$delete_id]);

            while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
                $thumbnail = $fetch_images['thumbnail'];
                $poster = $fetch_images['poster'];

                unlink('../uploaded_files/'.$thumbnail);

                if (!empty($poster)) {
                    unlink('../uploaded_files/'.$poster);
                }
                foreach(json_decode($fetch_images['movie_thumb']) as $image){
                    $movie_thumb = $image;

                    if (!empty($image)) {
                        unlink('../uploaded_files/'.$image);
                    }
                }
            }
            $delete_movie = $conn->prepare("DELETE FROM movies WHERE id = ?");
            $delete_movie->execute([$delete_id]);
            $success_msg[] = 'Фільм видалено';
        }else{
            $warning_msg[] = 'Фільм уже видалено';
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
        <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Переглянути фільми</h1>
            <p>Переглядайте додані фільми, редагуйте інформацію та керуйте каталогом кінопрем’єр у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути фільми</span>
        </div>
    </div>

    <!-- секція перегляду фільмів-->
    <div class="hall-container">
        <div class="heading">
            <h1>Переглянути фільми</h1>
            <a href="add_movie.php" class="btn">+</a>
        </div>

        <div class="box-container" style="overflow-x: auto;">
            <?php
                $select_movies = $conn->prepare("SELECT * FROM movies ORDER BY id DESC");
                $select_movies->execute();

                if ($select_movies->rowCount() > 0) {
            ?>

            <table cellspacing="0" style="width: 100%;">
                <tr>
                    <th>Постер</th>
                    <th>Назва</th>
                    <th>Жанр</th>
                    <th>Рік</th>
                    <th>Тривалість</th>
                    <th>Статус</th>
                    <th>Дія</th>
                </tr>

                <?php while($fetch_movie = $select_movies->fetch(PDO::FETCH_ASSOC)){ ?>
                <tr>
                    <td>
                        <img src="../uploaded_files/<?= $fetch_movie['thumbnail']; ?>" style="width: 70px; height: 90px; object-fit: cover; border-radius: .5rem;">
                    </td>
                    <td><?= $fetch_movie['title']; ?></td>
                    <td><?= $fetch_movie['genre']; ?></td>
                    <td><?= $fetch_movie['release_year']; ?></td>
                    <td><?= $fetch_movie['duration']; ?></td>
                    <td><?= $fetch_movie['status']; ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="movie_id" value="<?= $fetch_movie['id']; ?>">
                            <a href="read_movie.php?get_id=<?= $fetch_movie['id']; ?>" class="btn">Деталі</a>
                            <a href="edit_movie.php?get_id=<?= $fetch_movie['id']; ?>" class="btn">Редагувати</a>
                            <button type="submit" name="delete" onclick="return confirm('Видалити цей фільм?');" class="btn">Видалити</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <?php
                }else{
                    echo '
                    <div class="empty">
                        <p>Фільм ще не додано!<br><a href="add_movie.php" class="btn">Додати фільм</a></p>
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