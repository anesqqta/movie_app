<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
        header('location:login.php');
    }

    //отримати id фільму з таблиці бронювання
    if (isset($_GET['get_id']) && !empty($_GET['get_id'])) {
        $booking_id = $_GET['get_id'];

        $check_booking = $conn->prepare("SELECT booking.*, movies.title FROM booking JOIN movies ON booking.movie_id = movies.id WHERE booking.id = ? AND booking.user_id = ? AND booking.status = ?");
        $check_booking->execute([$get_id, $user_id, 'оплачено']);

        if ($check_booking->rowCount() == 0) {
            $warning_msg[] = 'Ви можете залишити відгук тільки після оплаченого бронювання';
            header('location:my_booking.php');
            exit();
        }
    }

    //отримати фільм
    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        while($fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC)) {
            $fetch_img = $fetch_movie['thumbnail'];
            $movie_name = $fetch_movie['title'];
            $movie_duration = $fetch_movie['duration'];
            $release_year = $fetch_movie['release_year'];
            $trailer_url = $fetch_movie['trailer_url'];
        }
    }

    //додавання відгуку
    if (isset($_POST['add_review'])) {
        if ($user_id != '') {
            $id = unique_id();

            $title = $_POST['title'];
            $title = filter_var($title, FILTER_SANITIZE_STRING);

            $description = $_POST['description'];
            $description = filter_var($description, FILTER_SANITIZE_STRING);

            $rating = $_POST['ratings'];
            $rating = filter_var($rating, FILTER_SANITIZE_STRING);

            $image = $_FILES['image']['name'];
            $image = filter_var($image, FILTER_SANITIZE_STRING);
            $ext = pathinfo($image, PATHINFO_EXTENSION);
            $rename = unique_id().'.'.$ext;
            $image_size = $_FILES['image']['size'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = 'uploaded_files/'.$rename;

            $add_ratings = $conn->prepare("INSERT INTO reviews (id, movie_id, user_id, rating, title, photo, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $add_ratings->execute([$id, $movie_id, $user_id, $rating, $title, $rename, $description]);
            move_uploaded_file($image_tmp_name, $image_folder);
            header('location:my_booking.php');
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
            <h1>Додати відгук</h1>
            <p>Поділіться своїми враженнями про перегляд фільму, якість сервісу та допоможіть іншим користувачам зробити правильний вибір</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Додати відгук</span>
        </div>
    </div>

    <!-- секція відгуку -->
    <div class="review" style="padding: 5% 0;">
        <div class="heading">
            <h1>Опублікуйте свій відгук</h1>
            <p>Поділіться своїми враженнями про фільм, сервіс бронювання та якість обслуговування — ваша думка важлива для нас</p>
        </div>
        <div class="img-box">
            <div class="img">
                <img src="uploaded_files/<?= $fetch_img; ?>">
            </div>
            <div>
                <p>Назва фільму : <span><?= $movie_name; ?></span></p>
                <p>Тривалість : <span><?= $movie_duration; ?></span></p>
                <p>Рік випуску : <span><?= $release_year; ?></span></p>
            </div>
        </div>
        <div class="form-container">
            <form action="" method="post" class="login" enctype="multipart/form-data">
                <div class="col" style="display: flex;">
                    <div class="input-field">
                        <p>Назва <span>*</span></p>
                        <input type="text" name="title" placeholder="Введіть назву" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Завантажте зображення <span>*</span></p>
                        <input type="file" name="image" accept="image/*" required class="box">
                    </div>
                </div>
                <div class="input-field">
                    <p>Напишіть відгук <span>*</span></p>
                    <textarea name="description" placeholder="Введіть відгук" class="box" required cols="30" rows="10"></textarea>
                </div>
                <div class="input-field">
                    <p>Поставте оцінку <span>*</span></p>
                    <select class="box" name="ratings" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="flex-btn">
                    <button type="submit" name="add_review" class="btn">Опублікуйте свій відгук</button>
                    <a href="my_booking.php" class="btn">Повернутись назад</a>
                </div>
            </form>
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