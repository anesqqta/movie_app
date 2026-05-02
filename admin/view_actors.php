<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //видалення актора з бази даниих
    if (isset($_POST['delete'])) {
        $delete_id = $_POST['actor_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM actors WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $select_images = $conn->prepare("SELECT * FROM actors WHERE id = ?");
            $select_images->execute([$delete_id]);

            while($fetch_image = $select_images->fetch(PDO::FETCH_ASSOC)){
                $image = $fetch_image['image'];

                unlink('../uploaded_files/actors/'.$image);

            }
            $delete_actor = $conn->prepare("DELETE FROM actors WHERE id = ?");
            $delete_actor->execute([$delete_id]);
            $success_msg[] = 'Актора видалено';
        }else{
            $warning_msg[] = 'Актора уже видалено';
        }
    }

    //редагування 
    if (isset($_POST['edit_actor'])) {
        $update_id = $_POST['update_id'];
        $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);


        $actor_name = $_POST['actor_name'];
        $actor_name = filter_var($actor_name, FILTER_SANITIZE_STRING);
        
        $actor_role = $_POST['role'];
        $actor_role = filter_var($actor_role, FILTER_SANITIZE_STRING);

        $actor_bio = $_POST['bio'];
        $actor_bio = filter_var($actor_bio, FILTER_SANITIZE_STRING);

        if(!empty($_FILES['image']['name'])){
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $rename = unique_id().'.'.$ext;
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = '../uploaded_files/actors/'.$rename;

            move_uploaded_file($image_tmp_name, $image_folder);
        }else{
            $select_old = $conn->prepare("SELECT image FROM actors WHERE id = ?");
            $select_old->execute([$update_id]);
            $rename = $select_old->fetchColumn();
        }

        $update_actor = $conn->prepare("UPDATE actors SET name = ?, role = ?, bio = ?, image = ? WHERE id = ?");
        $update_actor->execute([$actor_name, $actor_role, $actor_bio, $rename, $update_id]);

        move_uploaded_file($image_tmp_name, $image_folder);

        $success_msg[] = 'Актора оновлено!';
        
        header('location:view_actors.php');
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
            <h1>Переглянути акторів</h1>
            <p>Переглядайте список акторів, їхню інформацію та керуйте даними у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути акторів</span>
        </div>
    </div>

    <!-- секція перегляду акторів-->
    <div class="gride">
        <div class="heading">
            <h1>Всі актори</h1>
            <a href="add_actors.php" class="btn">+</a>
        </div>
        <div class="box-container">
            <?php
                $select_actors = $conn->prepare("SELECT * FROM actors");
                $select_actors->execute();

                if ($select_actors->rowCount() > 0) {
                    while($fetch_actors = $select_actors->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="actor_id" value="<?= $fetch_actors['id']; ?>">
                <img src="../uploaded_files/actors/<?= $fetch_actors['image']; ?>">
                <h2><?= $fetch_actors['name']; ?></h2>
                <span><?= $fetch_actors['role']; ?></span>
                <div class="flex-btn">
                    <button type="submit" name="delete" onclick="return confirm('Видалити цього актора?');" class="btn">Видалити</button>
                    <a href="view_actors.php?get_id=<?= $fetch_actors['id']; ?>" class="btn">Редагувати</a>
                </div>
            </form>
            <?php 
                    }
                }else{
                    echo '
                    <div class="empty">
                        <p>Актора ще не додано!<br><a href="add_actors.php" class="btn">Додати актора</a></p>
                    </div>
                    ';
                }
            ?>
        </div>
    </div>

    <div class="update-container">
        <?php
            if (isset($_GET['get_id'])) {
                $edit_id = $_GET['get_id'];
                $edit_query = $conn->prepare("SELECT * FROM actors WHERE id = ?");
                $edit_query->execute([$edit_id]);

                if ($edit_query->rowCount() > 0) {
                    while($fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="update-container">
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="login">
                    <h3>Редагувати деталі актора</h3>
                    <input type="hidden" name="update_id" value="<?= $fetch_edit['id']; ?>">
                    <div class="img-box">
                        <img src="../uploaded_files/actors/<?= $fetch_edit['image']; ?>">
                    </div>
                    <div class="input-field">
                        <p>Ім'я актора<span>*</span></p>
                        <input type="text" name="actor_name" maxlength="50" value="<?= $fetch_edit['name']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Роль актора<span>*</span></p>
                        <input type="text" name="role" maxlength="50" value="<?= $fetch_edit['role']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Біографія актора<span>*</span></p>
                        <textarea name="bio" required style="height:10rem;" class="box"><?= $fetch_edit['bio']; ?></textarea>
                    </div>
                    <div class="input-field">
                        <p>Фото актора<span>*</span></p>
                        <input type="file" name="image" required accept="image/*" class="box">
                    </div>
                    <button type="submit" name="edit_actor" class="btn">Редагувати актора</button>
                </form>
            </div>
        </div>
        <?php 
                    }
                }
                echo "<script>document.querySelector('.update-container').style.display='block'</script>";
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