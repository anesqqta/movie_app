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

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    $genre_filter = isset($_GET['genre']) ? trim($_GET['genre']) : '';
    $sort_filter = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

    $search = filter_var($search, FILTER_SANITIZE_STRING);
    $status_filter = filter_var($status_filter, FILTER_SANITIZE_STRING);
    $genre_filter = filter_var($genre_filter, FILTER_SANITIZE_STRING);
    $sort_filter = filter_var($sort_filter, FILTER_SANITIZE_STRING);

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

        <form action="" method="get" class="admin-filter-form">
        <input type="text" name="search" placeholder="Пошук за назвою фільму" value="<?= htmlspecialchars($search); ?>">

        <select name="status">
            <option value="">Усі статуси</option>
            <option value="active" <?= ($status_filter == 'active') ? 'selected' : ''; ?>>Активні</option>
            <option value="deactive" <?= ($status_filter == 'deactive') ? 'selected' : ''; ?>>Неактивні</option>
        </select>

        <input type="text" name="genre" placeholder="Фільтр за жанром" value="<?= htmlspecialchars($genre_filter); ?>">

        <select name="sort">
            <option value="newest" <?= ($sort_filter == 'newest') ? 'selected' : ''; ?>>Новіші спочатку</option>
            <option value="oldest" <?= ($sort_filter == 'oldest') ? 'selected' : ''; ?>>Старіші спочатку</option>
            <option value="year_desc" <?= ($sort_filter == 'year_desc') ? 'selected' : ''; ?>>Рік: новіші</option>
            <option value="year_asc" <?= ($sort_filter == 'year_asc') ? 'selected' : ''; ?>>Рік: старіші</option>
        </select>

        <button type="submit" class="btn">Застосувати</button>
        <a href="view_movie.php" class="btn">Скинути</a>
    </form>

        <div class="box-container" style="overflow-x: auto;">
            <?php
                $query = "SELECT * FROM movies WHERE 1=1";
                $params = [];

                if (!empty($search)) { 
                    $query .= " AND title LIKE ?";
                    $params[] = "%".$search."%";
                }

                if (!empty($status_filter)) {
                    $query .= " AND status = ?";
                    $params[] = $status_filter;
                }

                if (!empty($genre_filter)) {
                    $query .= " AND genre LIKE ?";
                    $params[] = "%".$genre_filter."%";
                }

                if ($sort_filter == 'oldest') {
                    $query .= " ORDER BY id ASC";
                } elseif ($sort_filter == 'year_desc') {
                    $query .= " ORDER BY release_year DESC";
                } elseif ($sort_filter == 'year_asc') {
                    $query .= " ORDER BY release_year ASC";
                } else {
                    $query .= " ORDER BY id DESC";
                }

                $select_movies = $conn->prepare($query);
                $select_movies->execute($params);

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