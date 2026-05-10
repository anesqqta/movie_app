<?php
    include '../components/connect.php';

    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
        exit();
    }

    $get_id = $_GET['get_id'];

    if (isset($_POST['edit'])) {
        $movie_id = filter_var($_POST['movie_id'], FILTER_SANITIZE_STRING);

        $name = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
        $language = filter_var($_POST['language'], FILTER_SANITIZE_STRING);
        $genre = filter_var($_POST['genre'], FILTER_SANITIZE_STRING);
        $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
        $duration = filter_var($_POST['duration'], FILTER_SANITIZE_STRING);
        $trailer_url = filter_var($_POST['trailer_url'], FILTER_SANITIZE_STRING);
        $release_year = filter_var($_POST['release_year'], FILTER_SANITIZE_STRING);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

        $actors = $_POST['actors'] ?? [];
        $actors1 = implode(',', $actors);

        $crews = $_POST['crew'] ?? [];
        $crews1 = implode(',', $crews);

        // старі дані фільму
        $old_movie = $conn->prepare("SELECT * FROM movies WHERE id = ? LIMIT 1");
        $old_movie->execute([$movie_id]);
        $fetch_old = $old_movie->fetch(PDO::FETCH_ASSOC);

        $image = $fetch_old['poster'];
        $thumbnail = $fetch_old['thumbnail'];
        $fileArray = $fetch_old['movie_thumb'];

        // оновити постер, якщо вибрано новий
        if (!empty($_FILES['image']['name'])) {
            $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = '../uploaded_files/'.$image;

            move_uploaded_file($image_tmp_name, $image_folder);
        }

        // оновити заставку, якщо вибрано нову
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumbnail = filter_var($_FILES['thumbnail']['name'], FILTER_SANITIZE_STRING);
            $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
            $thumbnail_folder = '../uploaded_files/'.$thumbnail;

            move_uploaded_file($thumbnail_tmp_name, $thumbnail_folder);
        }

        // оновити знімки з фільму, якщо вибрано нові
        if (!empty($_FILES['thumbnails']['name'][0])) {
            $newFiles = [];

            $totalFiles = count($_FILES['thumbnails']['name']);

            for ($i = 0; $i < $totalFiles; $i++) {
                $imageName = $_FILES['thumbnails']['name'][$i];
                $tmpName = $_FILES['thumbnails']['tmp_name'][$i];

                $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
                $newImageName = unique_id().'.'.$imageExtension;

                $image_folder = '../uploaded_files/'.$newImageName;
                move_uploaded_file($tmpName, $image_folder);

                $newFiles[] = $newImageName;
            }

            $fileArray = json_encode($newFiles);
        }

        $update_movie = $conn->prepare("UPDATE movies SET title = ?, description = ?, language = ?, genre = ?, release_year = ?, duration = ?, poster = ?, thumbnail = ?, trailer_url = ?, movie_thumb = ?, status = ? WHERE id = ?");
        $update_movie->execute([$name, $content, $language, $genre, $release_year, $duration, $image, $thumbnail, $trailer_url, $fileArray, $status, $movie_id]);

        $update_actors = $conn->prepare("UPDATE movie_actors SET actor_id = ? WHERE movie_id = ?");
        $update_actors->execute([$actors1, $movie_id]);

        $update_crews = $conn->prepare("UPDATE movie_directors SET director_id = ? WHERE movie_id = ?");
        $update_crews->execute([$crews1, $movie_id]);

        $success_msg[] = 'Фільм оновлено!';
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
            <h1>Редагувати фільм</h1>
            <p>Оновлюйте інформацію про фільм, змінюйте опис, тривалість, зображення та інші дані в каталозі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Редагувати фільм</span>
        </div>
    </div>

    <!-- секція редагування фільму-->
    <div class="form-container">
        <?php
            $select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ? LIMIT 1");
            $select_movie->execute([$get_id]);

            if ($select_movie->rowCount() > 0) {
                while($fetch_movie = $select_movie->fetch(PDO::FETCH_ASSOC)){
                    $movie_id = $fetch_movie['id'];
        ?>
        <form action="" method="post" enctype="multipart/form-data" class="register add-movie">
            <input type="hidden" name="movie_id" value="<?= $movie_id; ?>">
            <h3>Редагувати фільм</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Назва <span>*</span></p>
                        <input type="text" name="title" maxlength="100" placeholder="<?= $fetch_movie['title'] ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Мова <span>*</span></p>
                        <input type="text" name="language" maxlength="100" placeholder="<?= $fetch_movie['language'] ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Команда <span>*</span></p>
                        <div class="dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('directorsDropdown')">
                                <span>Виберіть членів команда </span><i class="bx bx-chevron-down"></i>
                            </div>
                            <div class="dropdown-content" id="directorsDropdown">
                                <?php
                                    //1. отримання членів команди конкретного фільму
                                    $selectDirectors = $conn->prepare("SELECT director_id FROM movie_directors WHERE movie_id = ?");
                                    $selectDirectors->execute([$movie_id]);

                                    $directorIDs = $selectDirectors->fetchColumn();

                                    $directorArray = explode(',', $directorIDs);

                                    $allDirectors = $conn->query("SELECT * FROM crew_members")->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($allDirectors as $d) {
                                        $isChecked = in_array($d['id'], $directorArray) ? "checked" : "";
                                        echo "
                                            <label>
                                                <input type='checkbox' name='crew[]' value='{$d['id']}' $isChecked>
                                                {$d['name']}
                                            </label>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Жанр <span>*</span></p>
                        <input type="text" name="genre" maxlength="100" placeholder="<?= $fetch_movie['genre'] ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Знімки з фільму <span>*</span></p>
                        <input type="file" name="thumbnails[]" id="images" class="box" multiple>
                    </div>
                    <div class="input-field">
                        <p>Статус фільму <span>*</span></p>
                        <select name="status" class="box">
                            <option value="<?= $fetch_movie['status']; ?>" selected><?= $fetch_movie['status']; ?></option>
                            <option value="active" style="color:gray;">Активний</option>
                            <option value="inactive" style="color:gray;">Неактивний</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>Акторський склад <span>*</span></p>
                        <div class="dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('actorsDropdown')">
                                <span>Виберіть членів акторського складу </span><i class="bx bx-chevron-down"></i>
                            </div>
                            <div class="dropdown-content" id="actorsDropdown">
                                <?php
                                    //1. отримання членів акторського складу конкретного фільму
                                    $selectActors = $conn->prepare("SELECT actor_id FROM movie_actors WHERE movie_id = ?");
                                    $selectActors->execute([$movie_id]);

                                    $actorIDs = $selectActors->fetchColumn();

                                    $actorArray = explode(',', $actorIDs);

                                    $allActors = $conn->query("SELECT * FROM actors")->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($allActors as $actor) {
                                        $isChecked = in_array($actor['id'], $actorArray) ? "checked" : "";
                                        echo "
                                            <label>
                                                <input type='checkbox' name='actors[]' value='{$actor['id']}' $isChecked>
                                                {$actor['name']}
                                            </label>
                                        ";
                                    } 
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Рік випуску <span>*</span></p>
                        <input type="text" name="release_year" maxlength="100" placeholder="<?= $fetch_movie['release_year'] ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Трейлер <span>*</span></p>
                        <input type="text" name="trailer_url" maxlength="100" placeholder="<?= $fetch_movie['trailer_url'] ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Тривалість <span>*</span></p>
                        <input type="text" name="duration" maxlength="100" placeholder="<?= $fetch_movie['duration'] ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Заставка фільму <span>*</span></p>
                        <input type="file" name="thumbnail" required accept="image/*" class="box"">
                    </div>
                    <div class="input-field">
                        <p>Постер фільму <span>*</span></p>
                        <input type="file" name="image" required accept="image/*" class="box"">
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>Опис фільму <span>*</span></p>
                <textarea name="content" style="height: 20rem;" class="box"><?= $fetch_movie['description'] ?></textarea>
            </div>
            <div class="flex-btn">
                <button type="submit" name="edit" class="btn">Редагувати</button>
                <a href="view_movie.php" class="btn">Повернутись назад</a>
            </div>
        </form>
        <?php 
                }
            }else{
                echo '
                <div class="empty">
                    <p>Фільм ще не додано!<br><a href="add_movie.php" class="btn">Додати фільм</a></p>
                </div>
                ';
            }
        ?>
    </div>
     

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>