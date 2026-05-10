<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //видалення члена команди з бази даниих
    if (isset($_POST['delete'])) {
        $delete_id = $_POST['crew_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM crew_members WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $select_images = $conn->prepare("SELECT * FROM crew_members WHERE id = ?");
            $select_images->execute([$delete_id]);

            while($fetch_image = $select_images->fetch(PDO::FETCH_ASSOC)){
                $image = $fetch_image['image'];

                unlink('../uploaded_files/actors/'.$image);

            }
            $delete_crew = $conn->prepare("DELETE FROM crew_members WHERE id = ?");
            $delete_crew->execute([$delete_id]);
            $success_msg[] = 'Члена команди видалено';
        }else{
            $warning_msg[] = 'Члена команди уже видалено';
        }
    }

    //редагування 
    if (isset($_POST['edit_crew'])) {
        $update_id = $_POST['update_id'];
        $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);


        $crew_name = $_POST['crew_name'];
        $crew_name = filter_var($crew_name, FILTER_SANITIZE_STRING);
        
        $crew_role = $_POST['role'];
        $crew_role = filter_var($crew_role, FILTER_SANITIZE_STRING);

        $crew_bio = $_POST['bio'];
        $crew_bio = filter_var($crew_bio, FILTER_SANITIZE_STRING);

        if(!empty($_FILES['image']['name'])){
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $rename = unique_id().'.'.$ext;
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = '../uploaded_files/actors/'.$rename;

            move_uploaded_file($image_tmp_name, $image_folder);
        }else{
            $select_old = $conn->prepare("SELECT image FROM crew_members WHERE id = ?");
            $select_old->execute([$update_id]);
            $rename = $select_old->fetchColumn();
        }

        $update_crew = $conn->prepare("UPDATE crew_members SET name = ?, role = ?, bio = ?, image = ? WHERE id = ?");
        $update_crew->execute([$crew_name, $crew_role, $crewr_bio, $rename, $update_id]);

        move_uploaded_file($image_tmp_name, $image_folder);

        $success_msg[] = 'Члена команди оновлено!';
        
        header('location:view_crew.php');
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
            <h1>Переглянути членів команди</h1>
            <p>Переглядайте список членів команди, їхню інформацію та керуйте даними у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути членів команди</span>
        </div>
    </div>

    <!-- секція перегляду членів команди-->
    <div class="hall-container">
        <div class="heading">
            <h1>Всі члени команди</h1>
            <a href="add_crew.php" class="btn">+</a>
        </div>

        <div class="box-container" style="overflow-x: auto;">
            <?php
                $select_crew = $conn->prepare("SELECT * FROM crew_members ORDER BY id DESC");
                $select_crew->execute();

                if ($select_crew->rowCount() > 0) {
            ?>

            <table cellspacing="0" style="width: 100%;">
                <tr>
                    <th>Фото</th>
                    <th>Ім’я</th>
                    <th>Посада</th>
                    <th>Біографія</th>
                    <th>Дія</th>
                </tr>

                <?php while($fetch_crew = $select_crew->fetch(PDO::FETCH_ASSOC)){ ?>
                <tr>

                    <td>
                        <img src="../uploaded_files/actors/<?= $fetch_crew['image']; ?>" 
                        style="width: 70px; height: 90px; object-fit: cover; border-radius: .5rem;">
                    </td>

                    <td><?= $fetch_crew['name']; ?></td>

                    <td><?= $fetch_crew['role']; ?></td>

                    <td style="max-width: 350px;">
                        <?= mb_strimwidth($fetch_crew['bio'], 0, 120, '...'); ?>
                    </td>

                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="crew_id" value="<?= $fetch_crew['id']; ?>">

                            <a href="edit_crew.php?get_id=<?= $fetch_crew['id']; ?>" class="btn">
                                Редагувати
                            </a>

                            <button type="submit" name="delete" 
                            onclick="return confirm('Видалити цього члена команди?');" class="btn">
                                Видалити
                            </button>
                        </form>
                    </td>

                </tr>
                <?php } ?>

            </table>

            <?php
                }else{
                    echo '
                    <div class="empty">
                        <p>Члена команди ще не додано!<br><a href="add_crew.php" class="btn">Додати члена команди</a></p>
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
                $edit_query = $conn->prepare("SELECT * FROM crew_members WHERE id = ?");
                $edit_query->execute([$edit_id]);

                if ($edit_query->rowCount() > 0) {
                    while($fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="update-container">
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="login">
                    <h3>Редагувати деталі члена команди</h3>
                    <input type="hidden" name="update_id" value="<?= $fetch_edit['id']; ?>">
                    <div class="img-box">
                        <img src="../uploaded_files/actors/<?= $fetch_edit['image']; ?>">
                    </div>
                    <div class="input-field">
                        <p>Ім'я члена команди<span>*</span></p>
                        <input type="text" name="crew_name" maxlength="50" value="<?= $fetch_edit['name']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Посада члена команди<span>*</span></p>
                        <input type="text" name="role" maxlength="50" value="<?= $fetch_edit['role']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>Біографія члена команди<span>*</span></p>
                        <textarea name="bio" required style="height:10rem;" class="box"><?= $fetch_edit['bio']; ?></textarea>
                    </div>
                    <div class="input-field">
                        <p>Фото члена команди<span>*</span></p>
                        <input type="file" name="image" required accept="image/*" class="box">
                    </div>
                    <button type="submit" name="edit_crew" class="btn">Редагувати члена команди</button>
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