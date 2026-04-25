<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    session_start();

    //збереження movie_id з url
    if (isset($_GET['movie_id'])) {
        $_SESSION['booking']['movie_id'] = $_GET['movie_id'];
    }

    $movie_id = $_SESSION['booking']['movie_id'];

    //отримати назву фільму та банер
    $movie_stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);

    if ($movie_stmt->rowCount() > 0) {
        while($fetch_movie = $movie_stmt->fetch(PDO::FETCH_ASSOC)) {
            $fetch_img = $fetch_movie['poster'];
            $movie_name = $fetch_movie['title'];
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
            <h1>Виберіть мову</h1>
            <p>Оберіть зручну мову перегляду фільму, щоб отримати максимальне задоволення від кінопрем’єри</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>виберіть мову</span>
        </div>
    </div>

    <!-- секція виберати мову -->
     <div class="select-container">
        <img src="uploaded_files/<?= $fetch_img; ?>">
        <div class="form">
            <form action="save-step1.php" method="post"> 
                <div class="flex">
                    <div class="col">
                        <div class="input-field">
                            <p>мова <span>*</span></p>
                            <select name="language" required class="box">
                                <option selected disabled>Виберіть мову</option>
                                <option value="Ukraine">Українська</option>
                                <option value="English">Англійська</option>
                                <option value="Spanish">Іспанська</option>
                                <option value="French">Французька</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Формат <span>*</span></p>
                            <select name="formate" required class="box">
                                <option selected disabled>Виберіть формат</option>
                                <option value="2D">2D</option>
                                <option value="3D">3D</option>
                                <option value="IMAX">IMAX</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-field">
                            <p>Виберіть час показу <span>*</span></p>
                            <select name="time" required class="box">
                                <option selected disabled>Виберіть час показу</option>
                                <?php
                                    $select_time = $conn->prepare("SELECT * FROM show_time");
                                    $select_time->execute();

                                    if ($select_time->rowCount() > 0) {
                                        while($fetch_time = $select_time->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <option value="<?= $fetch_time['time']; ?>"><?= $fetch_time['time']; ?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Дата <span>*</span></p>
                            <input type="date" name="date" class="box" required min="<?php echo date('Y-m-d') ?>">
                        </div>
                        <br><br>
                    </div>
                </div>
                <div class="flex-btn">
                    <a href="fetch_movie.php" class="btn">Повернутись назад</a>
                    <button type="submit" class="btn">Далі</button>
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