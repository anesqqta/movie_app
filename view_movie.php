<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    $pid = $_GET['pid'];
    include 'components/add_wishlist.php';
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
            <h1>Деталі фільму</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Деталі фільму</span>
        </div>
    </div>

    <!-- секція деталей фільму -->
     <div class="read-movie">
        <div class="heading">
            <h1>Деталі фільму</h1>
        </div>
        <div class="container">
            <?php
                $search_movies = $conn->prepare("SELECT * FROM movies WHERE id = ? AND status = ?");
                $search_movies->execute([$pid, 'active']);

                if ($search_movies->rowCount() > 0) {
                    while($fetch_movie = $search_movies->fetch(PDO::FETCH_ASSOC)){
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="movie_id" value="<?= $fetch_movie['id']; ?>">
                <div class="big-img">
                    <img src="uploaded_files/<?= $fetch_movie['poster']; ?>" class="poster">
                </div>

                <div class="head">
                    <div class="title">movie : <span><?= $fetch_movie['title']; ?></span></div>
                    <div class="title">language : <span><?= $fetch_movie['language']; ?></span></div>
                    <div class="title"><i class="bx bxs-calendar"></i><span><?= $fetch_movie['release_year']; ?></span></div>
                    <div class="title"><i class="bx bxs-stopwatch"></i><span><?= $fetch_movie['duration']; ?></span></div>
                    <a href="<?= $fetch_movie['trailer_url']; ?>"><img src="image/play-button.png"></a>
                </div>

                <div class="content">
                    <h1>screen shorts</h1>
                    <div class="screenshot">
                        <?php foreach(json_decode($fetch_movie['movie_thumb']) as $image) : ?>
                            <img src="uploaded_files/<?= $image ?>" width="100">
                        <?php endforeach; ?>
                    </div>
                    <h1>synopsis</h1>
                    <p class="description"><?= $fetch_movie['description']; ?></p>
                    <h1>cast</h1>
                    <div class="cast-section">
                        <?php
                            //1. get actors ids (comma separated)
                            $stmt = $conn->prepare("SELECT actor_id FROM movie_actors WHERE movie_id = ?");
                            $stmt->execute([$pid]);
                            $row = $stmt->fetch();

                            $actorIDs = $row['actor_id']; //"3,7,12"

                            //2. convert to array
                            $actorArray = explode(',', $actorIDs);

                            //3. create placeholders for IN()
                            $placeholders = implode(',', array_fill(0, count($actorArray), '?'));

                            //4.fetch actor details
                            $sql = "SELECT * FROM actors WHERE id IN ($placeholders)";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($actorArray);
                            $actors = $stmt->fetchAll();

                            //5. display names
                            foreach ($actors as $actor) { ?>

                                <div class="detail">
                                    <div class="img-box">
                                        <img src="uploaded_files/actors/<?= $actor['image']; ?>">
                                    </div><br>
                                    <h2><?= $actor['name'] ?></h2>
                                    <p><?= $actor['role'] ?></p>
                                </div>
                        <?php } ?>
                    </div>
                    <h1>crew</h1>
                    <div class="cast-section">
                        <?php 
                            //1. fetch comma-separated crew IDs
                            $stmt = $conn->prepare("SELECT director_id FROM movie_directors WHERE movie_id = ?");
                            $stmt->execute([$pid]);
                            $row = $stmt->fetch();

                            $crewIDs = $row['director_id']; //"2,3,4";

                            //2. convert to array
                            $crewArray = explode(',', $crewIDs);

                            //3. prepare IN list placeholders
                            $placeholders = implode(',', array_fill(0, count($crewArray), '?'));

                            //4. fetch crew members
                            $sql = "SELECT * FROM crew_members WHERE id IN ($placeholders)";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($crewArray);
                            $crews = $stmt->fetchAll();

                            foreach ($crews as $crew) { ?>
                                <div class="detail">
                                    <div class="img-box">
                                        <img src="uploaded_files/actors/<?= $crew['image']; ?>">
                                    </div><br>
                                    <h2><?= $crew['name'] ?></h2>
                                    <p><?= $crew['role'] ?></p>
                                </div>
                        <?php } ?>
                    </div>
                </div>
            </form>
            <?php
                    }
                }
            ?>
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