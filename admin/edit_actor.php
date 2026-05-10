<?php
    include '../components/connect.php';

    if (isset($_COOKIE['admin_id'])){
        $admin_id = $_COOKIE['admin_id'];
    }else{
        $admin_id = '';
        header('location:login.php');
        exit();
    }

    if(isset($_GET['get_id'])){
        $edit_id = $_GET['get_id'];
        $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
    }else{
        header('location:view_actors.php');
        exit();
    }

    if (isset($_POST['edit_actor'])) {

        $actor_name = filter_var($_POST['actor_name'], FILTER_SANITIZE_STRING);
        $actor_role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);
        $actor_bio = filter_var($_POST['bio'], FILTER_SANITIZE_STRING);

        if(!empty($_FILES['image']['name'])){

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $rename = unique_id().'.'.$ext;

            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = '../uploaded_files/actors/'.$rename;

            move_uploaded_file($image_tmp_name, $image_folder);

        }else{

            $select_old = $conn->prepare("SELECT image FROM actors WHERE id = ?");
            $select_old->execute([$edit_id]);

            $rename = $select_old->fetchColumn();
        }

        $update_actor = $conn->prepare("
            UPDATE actors 
            SET name = ?, role = ?, bio = ?, image = ?
            WHERE id = ?
        ");

        $update_actor->execute([
            $actor_name,
            $actor_role,
            $actor_bio,
            $rename,
            $edit_id
        ]);

        $success_msg[] = 'Актора оновлено!';
    }

    $edit_query = $conn->prepare("SELECT * FROM actors WHERE id = ?");
    $edit_query->execute([$edit_id]);

    if($edit_query->rowCount() > 0){
        $fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC);
    }else{
        header('location:view_actors.php');
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">

    <title>BOLETO</title>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<div class="banner">
    <div class="detail">
        <h1>Редагувати актора</h1>
        <p>Оновіть інформацію про актора у системі</p>
        <span><a href="dashboard.php">Адмін</a><i class="bx bxs-right-arrow-alt"></i>Редагувати актора</span>
    </div>
</div>

<div class="form-container">
    <form action="" method="post" enctype="multipart/form-data" class="login">
        <h3>Редагувати актора</h3>
        <div class="img-box">
            <img src="../uploaded_files/actors/<?= $fetch_edit['image']; ?>">
        </div>
        <div class="input-field">
            <p>Ім'я актора <span>*</span></p>
            <input type="text" name="actor_name" maxlength="50"
            value="<?= $fetch_edit['name']; ?>" class="box" required>
        </div>
        <div class="input-field">
            <p>Роль актора <span>*</span></p>
            <input type="text" name="role" maxlength="50"
            value="<?= $fetch_edit['role']; ?>" class="box" required>
        </div>
        <div class="input-field">
            <p>Біографія актора <span>*</span></p>
            <textarea name="bio" required class="box"
            style="height: 10rem;"><?= $fetch_edit['bio']; ?></textarea>
        </div>
        <div class="input-field">
            <p>Фото актора</p>
            <input type="file" name="image" accept="image/*" class="box">
        </div>
        <div class="flex-btn">
            <button type="submit" name="edit_actor" class="btn">Зберегти зміни</button>
            <a href="view_actors.php" class="btn">Повернутись назад</a>
        </div>

    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script type="text/javascript">
    <?php include '../js/admin_script.js'; ?>
</script>

<?php include '../components/alert.php'; ?>

</body>
</html>