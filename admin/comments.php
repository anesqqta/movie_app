<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM reviews WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_user = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            $delete_user->execute([$delete_id]);
            $success_msg[] = 'Відгук видалено';
        }else{
            $warning_msg[] = 'Відгук уже видалено';
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
            <h1>Відгуки користувачів</h1>
            <p>Переглядайте відгуки користувачів, аналізуйте їх думки та керуйте коментарями в системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Відгуки користувачів</span>
        </div>
    </div>

    <!-- секція відгуки користувачів-->
    <div class="reviews-container">
        <div class="heading">
            <h1>Відгуки користувачів</h1>
        </div>
        <div class="box-container">
            <?php
                $select_reviews = $conn->prepare("SELECT * FROM reviews");
                $select_reviews->execute();

                if ($select_reviews->rowCount() > 0) {
                    while($fetch_reviews = $select_reviews->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box">
                <?php
                    $select_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $select_user->execute([$fetch_reviews['user_id']]);
                    while($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="user">
                    <?php if($fetch_user['name'] != '') { ?>
                        <img src="../uploaded_files/<?= $fetch_user['image']; ?>">
                    <?php }else{ ?>
                        <h3><?= substr($fetch_user['name'], 0,1) ?></h3>
                    <?php } ?>
                    <div>
                        <p><?= $fetch_user['name']; ?></p>
                        <span><?= $fetch_reviews['date']; ?></span>
                    </div>
                </div>
                <?php } ?>
                <div class="ratings">
                    <?php if($fetch_reviews['rating'] == 1){ ?>
                        <p style="background:red; color: #fff;"><i class="bx bxs-star"></i><span><?= $fetch_reviews['rating']; ?></span></p>
                    <?php } ?>
                    <?php if($fetch_reviews['rating'] == 2){ ?>
                        <p style="background:red; color: #fff;"><i class="bx bxs-star"></i><span><?= $fetch_reviews['rating']; ?></span></p>
                    <?php } ?>
                    <?php if($fetch_reviews['rating'] == 3){ ?>
                        <p style="background:orange; color: #fff;"><i class="bx bxs-star"></i><span><?= $fetch_reviews['rating']; ?></span></p>
                    <?php } ?>
                    <?php if($fetch_reviews['rating'] == 4){ ?>
                        <p style="background:green; color: #fff;"><i class="bx bxs-star"></i><span><?= $fetch_reviews['rating']; ?></span></p>
                    <?php } ?>
                    <?php if(5){ ?>
                        <p style="background:green; color: #fff;"><i class="bx bxs-star"></i><span><?= $fetch_reviews['rating']; ?></span></p>
                    <?php } ?>
                </div>
                <h3 class="title"><?= $fetch_reviews['title']; ?></h3>
                <?php if($fetch_reviews['description'] != ''){ ?>
                    <p class="description"><?= $fetch_reviews['description']; ?></p>
                <?php } ?>
                <form action="" method="post">
                    <input type="hidden" name="delete_id" value="<?= $fetch_reviews['id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Видалити цей відгук?');" class="btn">Видалити</button>
                </form>
            </div>
            <?php
                    }
                }else{
                    echo '
                        <div class="empty">
                            <p>Ще немає відгуків!</p>
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