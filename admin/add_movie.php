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
            <h1>Додавання фільму</h1>
            <p>Додавайте нові кінопрем’єри, заповнюйте інформацію про фільми та керуйте каталогом у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Додавання фільму</span>
        </div>
    </div>

    <!-- секція додавання фільму-->
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>add movie</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>Назва <span>*</span></p>
                        <input type="text" name="title" required maxlength="100" placeholder="movie title" class="box">
                    </div>
                    <div class="input-field">
                        <p>Мова <span>*</span></p>
                        <input type="text" name="language" required maxlength="100" placeholder="movie title" class="box">
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
                        <input type="text" name="genre" required maxlength="100" placeholder="movie title" class="box">
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
                        <input type="text" name="release_year" required maxlength="100" placeholder="movie title" class="box">
                    </div>
                    <div class="input-field">
                        <p>Трейлер <span>*</span></p>
                        <input type="text" name="trailer_url" required maxlength="100" placeholder="movie title" class="box">
                    </div>
                    <div class="input-field">
                        <p>Тривалість <span>*</span></p>
                        <input type="text" name="duration" required maxlength="100" placeholder="movie title" class="box">
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
                <textarea name="content" required style="height: 20rem;" class="box" placeholder="movie description"></textarea>
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