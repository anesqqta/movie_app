<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['uploade'])) {
        //$movie_id = unique_id();

        $name = $_POST['title'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $language = $_POST['language'];
        $language = filter_var($language, FILTER_SANITIZE_STRING);

        $genre = $_POST['genre'];
        $genre = filter_var($genre, FILTER_SANITIZE_STRING);

        $content = $_POST['content'];
        $content = filter_var($content, FILTER_SANITIZE_STRING);

        $duration = $_POST['duration'];
        $duration = filter_var($duration, FILTER_SANITIZE_STRING);

        $actors = $_POST['actors'] ?? [];
        $actors1 = implode(',', $actors);

        $crews = $_POST['crew'] ?? [];
        $crews1 = implode(',', $crews);

        $trailer_url = $_POST['trailer_url'];
        $trailer_url = filter_var($trailer_url, FILTER_SANITIZE_STRING);

        $release_year = $_POST['release_year'];
        $release_year = filter_var($release_year, FILTER_SANITIZE_STRING);

        $status = 'active';

        //вставити заставку
        $totalFiles = count($_FILES['thumbnails']['name']);
        $fileArray = array();

        for ($i=0; $i < $totalFiles; $i++) {
            $imageName = $_FILES['thumbnails']['name'][$i];
            $tmpName = $_FILES['thumbnails']['tmp_name'][$i];
            $imageExtension = explode('.', $imageName);
            $imageExtension = strtolower(end($imageExtension));

            $NewImageName = unique_id().'.'.$imageExtension;

            $image_folder = '../uploaded_files/'.$NewImageName;

            move_uploaded_file($tmpName, $image_folder);

            $fileArray[] = $NewImageName;
        }
        $fileArray = json_encode($fileArray);

        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/'.$image;

        $thumbnail = $_FILES['thumbnail']['name'];
        $thumbnail = filter_var($thumbnail, FILTER_SANITIZE_STRING);
        $thumbnail_size = $_FILES['thumbnail']['size'];
        $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_folder = '../uploaded_files/'.$thumbnail;

        $select_image = $conn->prepare("SELECT * FROM movies WHERE poster = ?");
        $select_image->execute([$image]);

        if (isset($image)) {
            if ($select_image->rowCount() > 0) {
                $warning_msg[] = 'Назва зображення повторюється';
            }elseif ($image_size > 2000000) {
                $warning_msg[] = 'Розмір зображення занадто великий';
            }else{
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        }else{
            $image = "";
        }
        if ($select_image->rowCount() > 0 AND $image != '') {
            $warning_msg[] = 'Перейменуйте зображення';
        }else{
            $insert_movie = $conn->prepare("INSERT INTO movies (title, description, language, genre, release_year, duration, poster, thumbnail, trailer_url, movie_thumb, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_movie->execute([$name, $content, $language, $genre, $release_year, $duration, $image, $thumbnail, $trailer_url, $fileArray, $status]);

            $movie_id = $conn->lastInsertId();

            move_uploaded_file($thumbnail_tmp_name, $thumbnail_folder);
        }

        //додавання акторського складу
        $movie_actors = $conn->prepare("INSERT INTO movie_actors (movie_id, actor_id) VALUES (?, ?)");
        $movie_actors->execute([$movie_id, $actors1]);

        //додавання команди
        $movie_crews = $conn->prepare("INSERT INTO movie_directors (movie_id, director_id) VALUES (?, ?)");
        $movie_crews->execute([$movie_id, $crews1]);

        $success_msg[] = 'Фільм додано!';
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
            <h1>Додавання фільму</h1>
            <p>Додавайте нові кінопрем’єри, заповнюйте інформацію про фільми та керуйте каталогом у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Додавання фільму</span>
        </div>
    </div>

    <!-- секція додавання фільму-->
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>Додавання фільму</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Назва <span>*</span></p>
                        <input type="text" name="title" required maxlength="100" placeholder="Назва фільму" class="box">
                    </div>
                    <div class="input-field">
                        <p>Мова <span>*</span></p>
                        <input type="text" name="language" required maxlength="100" placeholder="Мова фільму" class="box">
                    </div>
                    <div class="input-field">
                        <p>Команда <span>*</span></p>
                        <div class="dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('directorsDropdown')">
                                <span>Виберіть членів команда </span><i class="bx bx-chevron-down"></i>
                            </div>
                            <div class="dropdown-content" id="directorsDropdown">
                                <?php
                                    $crew_members = $conn->query("SELECT * FROM crew_members")->fetchAll();
                                    foreach ($crew_members as $c) {
                                        echo "
                                            <label>
                                                <input type='checkbox' name='crew[]' value='{$c['id']}'>
                                                {$c['name']}
                                            </label>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Жанр <span>*</span></p>
                        <input type="text" name="genre" required maxlength="100" placeholder="Жанр фільму" class="box">
                    </div>
                    <div class="input-field">
                        <p>Знімки з фільму <span>*</span></p>
                        <input type="file" name="thumbnails[]" id="images" class="box" multiple>
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
                                    $actors = $conn->query("SELECT * FROM actors")->fetchAll();
                                    foreach ($actors as $a) {
                                        echo "
                                            <label>
                                                <input type='checkbox' name='actors[]' value='{$a['id']}'>
                                                {$a['name']}
                                            </label>
                                        ";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Рік випуску <span>*</span></p>
                        <input type="text" name="release_year" required maxlength="100" placeholder="Рік випуску фільму" class="box">
                    </div>
                    <div class="input-field">
                        <p>Трейлер <span>*</span></p>
                        <input type="text" name="trailer_url" required maxlength="100" placeholder="Трейлер фільму" class="box">
                    </div>
                    <div class="input-field">
                        <p>Тривалість <span>*</span></p>
                        <input type="text" name="duration" required maxlength="100" placeholder="Тривалість фільму" class="box">
                    </div>
                    <div class="input-field">
                        <p>Постер фільму <span>*</span></p>
                        <input type="file" name="image" required accept="image/*" class="box"">
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>Заставка фільму <span>*</span></p>
                <input type="file" name="thumbnail" required accept="image/*" class="box"">
            </div>
            <div class="input-field">
                <p>Опис фільму <span>*</span></p>
                <textarea name="content" required style="height: 20rem;" class="box" placeholder="Опис фільму"></textarea>
            </div>
            <button type="submit" name="uploade" class="btn">Завантажити</button>
        </form>
    </div>
     

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script type="text/javascript">
        <?php include '../js/admin_script.js'; ?>
    </script>

    <?php include '../components/alert.php'; ?>
    </body>
</html>