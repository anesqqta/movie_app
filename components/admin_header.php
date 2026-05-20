<header>
    <div class="logo">
        <img src="../image/logo.png">
    </div>
    <div class="right">
        <div class="bx bxs-user" id="user-btn"></div>
        <div class="toggle-btn"><i class="bx bx-menu"></i></div>
    </div>
    <div class="profile">
        <?php
            $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
            $select_profile->execute([$admin_id]);

            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <img src="../uploaded_files/<?= $fetch_profile['image']; ?>">
        <h3 style="margin-bottom: .5rem"><?= $fetch_profile['name']; ?></h3>
        <div class="flex-btn">
            <a href="profile.php" class="btn">Профіль</a>
            <a href="../components/admin_logout.php" onclick="return confirm('Вийти з профілю');" class="btn">Вийти</a>
        </div>
        <?php }else{ ?>
        <img src="../image/user.png">
        <h3 style="margin-bottom: .5rem;">Увійдіть або зареєструйтесь</h3>
        <div class="flex-btn">
            <a href="login.php" class="btn">Увійти</a>
            <a href="register.php" class="btn">зареєструватись</a>
        </div>
        <?php } ?>
    </div>
</header>
<div class="sidebar">
    <?php
        $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
        $select_profile->execute([$admin_id]);

        if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="profile">
        <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" class="logo-img">
        <h3 style="margin-bottom: .5rem"><?= $fetch_profile['name']; ?></h3>
    </div>
    <?php } ?>
    <h5>Меню</h5>
    <div class="navbar">
        <ul>
            <li><a href="dashboard.php"><i class="bx bxs-home-smile"></i>Панель керування</a></li>
            <li><a href="view_movie.php"><i class="bx bxs-food-menu"></i>Переглянути фільми</a></li>
            <li><a href="view_show.php"><i class="bx bxs-food-menu"></i>Переглянути сеанси</a></li>
            <li><a href="view_hall.php"><i class="bx bxs-home-smile"></i>Переглянути зали</a></li>
            <li><a href="view_booking.php"><i class="bx bxs-home-smile"></i>Переглянути бронювання</a></li>
            <li><a href="view_actors.php"><i class="bx bxs-user"></i>Переглянути акторів</a></li>
            <li><a href="view_crew.php"><i class="bx bxs-user"></i>Переглянути знімальну групу</a></li>
            <li><a href="message.php"><i class="bx bxs-envelope"></i>Повідомлення</a></li>
            <li><a href="user_account.php"><i class="bx bxs-user"></i>Клієнти</a></li>
            <li><a href="../components/admin_logout.php" onclick="return confirm('Вийти з профілю');"><i class="bx bxs-log-out"></i>Вийти</a>
        </ul>
    </div>
    <h5>Знайдіть нас</h5>
    <div class="social-links">
        <i class="bx bxl-facebook"></i>
        <i class="bx bxl-instagram-alt"></i>
        <i class="bx bxl-linkedin"></i>
        <i class="bx bxl-twitter"></i>
        <i class="bx bxl-pinterest-alt"></i>
    </div>
</div>