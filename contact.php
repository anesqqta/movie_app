<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    if (isset($_POST['send_message'])) {
        if ($user_id != '') {
            $id = unique_id();

            $name = $_POST['name'];
            $name = filter_var($name, FILTER_SANITIZE_STRING);

            $email = $_POST['email'];
            $email = filter_var($email, FILTER_SANITIZE_STRING);

            $subject = $_POST['subject'];
            $subject = filter_var($subject, FILTER_SANITIZE_STRING);

            $message = $_POST['message'];
            $message = filter_var($message, FILTER_SANITIZE_STRING);

            $verify_message = $conn->prepare("SELECT * FROM message WHERE user_id = ? AND name = ? AND email = ? AND subject = ? AND message = ?");
            $verify_message->execute([$user_id, $name, $email, $subject, $message]);

            if ($verify_message->rowCount() > 0) {
                $warning_msg[] = 'Повідомлення вже надіслано';
            }else{
                $insert_message = $conn->prepare("INSERT INTO message (id, user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_message->execute([$id, $user_id, $name, $email, $subject, $message]);
                $success_msg[] = 'Повідомлення надіслано';
            }
        }else{
            $warning_msg[] = 'Увійдіть спочатку';
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
        <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
        <title>BOLETO</title>
    </head>
    <body>

    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Наші контакти</h1>
            <p>Написати про нас</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>Контакти</span>
        </div>
    </div>

    <!-- секція сервісу -->
    <div class="service">
        <div class="heading">
            <h1>Наш сервіс</h1>
            <p>написати щось</p>
        </div>
        <div class="box-container">
            <div class="box">
                <img src="image/s-icon (2).png">
                <div>
                    <h1>Легке скасування</h1>
                    <p>написати щось</p>
                </div>
            </div>

            <div class="box">
                <img src="image/s-icon (3).png">
                <div>
                    <h1>Гарантія повернення грошей</h1>
                    <p>написати щось</p>
                </div>
            </div>

            <div class="box">
                <img src="image/s-icon (1).png">
                <div>
                    <h1>Онлайн-підтримка 24/7</h1>
                    <p>написати щось</p>
                </div>
            </div>
        </div>
    </div>

    <!-- секція контактів -->
    <div class="contact">
        <div class="heading">
            <h1>Зв'яжіться</h1>
            <p>написати щось</p>
        </div>
        <div class="box-container">
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="register">
                    <div class="input-field">
                        <p>Ваше ім'я <span>*</span></p>
                        <input type="text" name="name" required maxlength="50" placeholder="Введіть ім'я" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваш email <span>*</span></p>
                        <input type="email" name="email" required maxlength="50" placeholder="Введіть email" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваша тема <span>*</span></p>
                        <input type="text" name="subject" required maxlength="50" placeholder="Введіть тему" class="box">
                    </div>
                    <div class="input-field">
                        <p>Ваше повідомлення <span>*</span></p>
                        <textarea name="message" class="box"></textarea>
                    </div>
                    <button type="submit" name="send_message" class="btn">Надіслати повідомлення</button>
                </form>
            </div>
            <div class="box">
                <img src="image/contact.jpg">
            </div>
        </div>
    </div>

    <!-- секція адреси -->
    <div class="address">
        <div class="heading">
            <h1>Наші контактні деталі</h1>
            <p>написати щось</p>
        </div>
        <div class="box-container">
            <div class="box">
                <img src="image/ip-adress.png">
                <div>
                    <h4>Адреса</h4>
                    <p>Едельвейсів 495, Коломия <br>Україна, 78203</p>
                </div>
            </div>
            <div class="box">
                <img src="image/contact01.png">
                <div>
                    <h4>Номер телефону</h4>
                    <p>+380 98 67 08 240</p>
                    <p>+380 66 52 98 691</p>
                </div>
            </div>
            <div class="box">
                <img src="image/contact02.png">
                <div>
                    <h4>email</h4>
                    <p>boleto@gmail.com</p>
                    <p>boleto@kpk-lp.com.ua</p>
                </div>
            </div>
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