<?php
    include '../components/connect.php';
    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
    }

    //видалення члена знімальної групи з бази даниих
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
            $success_msg[] = 'Члена знімальної групи видалено';
        }else{
            $warning_msg[] = 'Члена знімальної групи уже видалено';
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
            <h1>Переглянути членів знімальної групи</h1>
            <p>Переглядайте список членів знімальної групи, їхню інформацію та керуйте даними у системі</p>
            <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Переглянути членів знімальної групи</span>
        </div>
    </div>

    <!-- секція перегляду членів знімальної групи-->
    <div class="hall-container">
        <div class="heading">
            <h1>Всі члени знімальної групи</h1>
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
                    <td style="max-width: 350px;"><?= mb_strimwidth($fetch_crew['bio'], 0, 120, '...'); ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="crew_id" value="<?= $fetch_crew['id']; ?>">
                            <a href="edit_crew.php?get_id=<?= $fetch_crew['id']; ?>" class="btn">Редагувати</a>
                            <button type="submit" name="delete" onclick="return confirm('Видалити цього члена знімальної групи?');" class="btn">Видалити</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <?php
                }else{
                    echo '
                    <div class="empty">
                        <p>Члена знімальної групи ще не додано!<br><a href="add_crew.php" class="btn">Додати члена знімальної групи</a></p>
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