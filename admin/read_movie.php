<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //отримання id  фільму з url
    $get_id = $_GET['get_id'];

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
            header('location:view_movie.php');
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
            <h1>Деталі фільму</h1>
            <p>Перегляньте повну інформацію про фільм, опис, тривалість та інші характеристики</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Деталі фільму</span>
        </div>
    </div>

    <!-- секція деталі фільму-->
    <div class="read-movies">
        <div class="heading">
            <h1>Деталі фільму</h1>
        </div>
        <div class="container">
            <?php
                $select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                $select_movie->execute([$get_id]);

                if ($select_movie->rowCount() > 0) {
                    while($fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="movie_id" value="<?= $fetch_movie['id']; ?>">
                <div class="big-img">
                    <img src="../uploaded_files/<?= $fetch_movie['poster']; ?>" class="poster">
                </div>
                <div class="head">
                    <div class="title">Назва : <span><?= $fetch_movie['title']; ?></span></div>
                    <div class="title">Мова : <span><?= $fetch_movie['language']; ?></span></div>
                    <div class="title"><i class="bx bxs-calendar"></i><span><?= $fetch_movie['release_year']; ?></span></div>
                    <div class="title"><i class="bx bxs-stopwatch"></i><span><?= $fetch_movie['duration']; ?></span></div>
                    <a href="<?= $fetch_movie['trailer_url']; ?>"><img src="../image/play-button.png"></a>
                    <div class="status" style="color: <?php if($fetch_movie['status'] == 'active'){echo "#31d7a9";}else{echo "red";} ?>;"><?= $fetch_movie['status']; ?></div>
                </div>
                <div class="content">
                    <h1>Знімки з фільму</h1>
                    <div class="screenshot">
                        <?php foreach(json_decode($fetch_movie['movie_thumb']) as $image) : ?>
                            <img src="../uploaded_files/<?= $image ?>" width="100">
                        <?php endforeach; ?>
                    </div>
                     <h1>Опис</h1>
                    <p class="description"><?= $fetch_movie['description']; ?></p>
                    <h1>Акторський склад</h1>
                    <div class="cast-section">
                        <?php
                            //1.отримати ідентифікатори акторів (розділені комами)
                            $stmt = $conn->prepare("SELECT actor_id FROM movie_actors WHERE movie_id = ?");
                            $stmt->execute([$get_id]);
                            $row = $stmt->fetch();

                            $actorIDs = $row['actor_id']; //"3,7,12"

                            //2.перетворити на масив
                            $actorArray = explode(',', $actorIDs);

                            //3.створіть заповнювачі для IN()
                            $placeholders = implode(',', array_fill(0, count($actorArray), '?'));

                            //4.отримати інформацію про актора
                            $sql = "SELECT * FROM actors WHERE id IN ($placeholders)";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($actorArray);
                            $actors = $stmt->fetchAll();

                            //5.відображаються імена
                            foreach ($actors as $actor) { ?>

                                <div class="detail">
                                    <div class="img-box">
                                        <img src="../uploaded_files/actors/<?= $actor['image']; ?>">
                                    </div><br>
                                    <h2><?= $actor['name'] ?></h2>
                                    <p><?= $actor['role'] ?></p>
                                </div>
                        <?php } ?>
                    </div>
                    <h1>Команда</h1>
                    <div class="cast-section">
                        <?php 
                            //1.отримати ідентифікатори екіпажу, розділені комами
                            $stmt = $conn->prepare("SELECT director_id FROM movie_directors WHERE movie_id = ?");
                            $stmt->execute([$get_id]);
                            $row = $stmt->fetch();

                            $crewIDs = $row['director_id']; //"2,3,4";

                            //2.перетворити на масив
                            $crewArray = explode(',', $crewIDs);

                            //3.підготуйте заповнювачі списку IN
                            $placeholders = implode(',', array_fill(0, count($crewArray), '?'));

                            $sql = "SELECT * FROM crew_members WHERE id IN ($placeholders)";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($crewArray);
                            $crews = $stmt->fetchAll();

                            foreach ($crews as $crew) { ?>
                                <div class="detail">
                                    <div class="img-box">
                                        <img src="../uploaded_files/actors/<?= $crew['image']; ?>">
                                    </div><br>
                                    <h2><?= $crew['name'] ?></h2>
                                    <p><?= $crew['role'] ?></p>
                                </div>
                        <?php } ?>
                    </div>
                    <div class="flex-btn">
                        <a href="edit_movie.php?get_id=<?= $fetch_movie['id']; ?>" class="btn">Редагувати</a>
                        <button type="submit" name="delete" class="btn" onclick="return confirm('Видалити цей фільм?');">Видалити</button>
                        <a href="view_movie.php" class="btn">Повернутись назад</a>
                    </div>
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