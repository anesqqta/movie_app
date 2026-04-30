<div class="dashboard-movies">
    <div class="heading">
        <h1>Активні фільми</h1>
    </div>
    <div class="box-container">
        <?php
            $select_movies = $conn->prepare("SELECT * FROM movies");
            $select_movies->execute();

            if ($select_movies->rowCount() > 0) {
                while($fetch_movies = $select_movies->fetch(PDO::FETCH_ASSOC)){
        ?>
        <form action="" method="post" class="box">
            <div class="img-box">
                <img src="../uploaded_files/<?= $fetch_movies['thumbnail']; ?>" class="img1">
            </div>
            <div><h3><?= $fetch_movies['title']; ?></h3></div>
            <div>
                <a href="<?= $fetch_movies['trailer_url']; ?>" class="bx bx-play btn"></a>
                <a href="read_movie.php?get_id=<?= $fetch_movies['id']; ?>" class="bx bxs-show btn"></a>
            </div>
        </form>
        <?php
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Фільм ще не додано!</p>
                    </div>
                    ';
                }
            ?>
    </div>
</div>