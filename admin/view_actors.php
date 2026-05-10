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
    <div class="hall-container">
        <div class="heading">
            <h1>Всі актори</h1>
            <a href="add_actors.php" class="btn">+</a>
        </div>

        <div class="box-container" style="overflow-x: auto;">
            <?php
                $select_actors = $conn->prepare("SELECT * FROM actors ORDER BY id DESC");
                $select_actors->execute();

                if ($select_actors->rowCount() > 0) {
            ?>

            <table cellspacing="0" style="width: 100%;">
                <tr>
                    <th>Фото</th>
                    <th>Ім’я</th>
                    <th>Роль</th>
                    <th>Біографія</th>
                    <th>Дія</th>
                </tr>

                <?php while($fetch_actors = $select_actors->fetch(PDO::FETCH_ASSOC)){ ?>
                <tr>
                    <td>
                        <img src="../uploaded_files/actors/<?= $fetch_actors['image']; ?>" 
                        style="width: 70px; height: 90px; object-fit: cover; border-radius: .5rem;">
                    </td>

                    <td><?= $fetch_actors['name']; ?></td>

                    <td><?= $fetch_actors['role']; ?></td>

                    <td style="max-width: 350px;">
                        <?= mb_strimwidth($fetch_actors['bio'], 0, 120, '...'); ?>
                    </td>

                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="actor_id" value="<?= $fetch_actors['id']; ?>">

                            <a href="edit_actor.php?get_id=<?= $fetch_actors['id']; ?>" class="btn">
                                Редагувати
                            </a>

                            <button type="submit" name="delete" 
                            onclick="return confirm('Видалити цього актора?');" class="btn">
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
                        <p>Актора ще не додано!<br><a href="add_actors.php" class="btn">Додати актора</a></p>
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