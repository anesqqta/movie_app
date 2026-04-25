<?php
    include 'components/connect.php';
    if (isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    session_start();

    $language = $_SESSION['booking']['language'];
    $formate = $_SESSION['booking']['formate'];
    $time = $_SESSION['booking']['time'];
    $date = $_SESSION['booking']['date'];

    $movie_id = $_SESSION['booking']['movie_id'];
    
    if (isset($_GET['show_id'])) {
        $show_id = $_GET['show_id'];
    }elseif (isset($_SESSION['booking']['show_id'])) {
        $show_id = $_SESSION['booking']['show_id'];
    }else{
        die("Сеанс не вибрано");
    }

    //отримати назву фільму та банер
    $movie_stmt = $conn->prepare("SELECT title FROM movies WHERE id = ?");
    $movie_stmt->execute([$movie_id]);
    $movie_title = $movie_stmt->fetchColumn();   
    
    if (isset($_POST['select_seat'])) {
        if ($user_id != '') {
            $id = unique_id();

            $total_seats = $_POST['total_seats'];
            $total_seats = filter_var($total_seats, FILTER_SANITIZE_STRING);

            $total_price = $_POST['total_price'];
            $total_price = filter_var($total_price, FILTER_SANITIZE_STRING);

            $selected_seats = $_POST['selected_seats'];
            $selected_seats = filter_var($selected_seats, FILTER_SANITIZE_STRING);

            //вставити деталі сидінь
            $stmt = $conn->prepare("INSERT INTO seat_details (id, user_id, show_id, total_seat, selected_seats, amount) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $user_id, $show_id, $total_seats, $selected_seats, $total_price]);

            if ($stmt) {
                header('location:booking.php?show_id='.$show_id);
            }else{
                $warning_msg[] = 'Не вдалося забронювати місце, спробуйте ще раз';
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
            <h1>Виберіть місце</h1>
            <p>Оберіть зручні місця у кінозалі та завершіть бронювання квитків для комфортного перегляду фільму</p>
            <span><a href="home.php">Головна</a><i class="bx bxs-right-arrow-alt"></i>виберіть місце</span>
        </div>
    </div>
        
        <!-- секція вибору місця -->
        <div class="select-seat">
            <div class="heading">
                <h1>Екран</h1>
            </div>
            <img src="image/screen-thumb.png">
            <div class="seat-map">
                <?php
                    //отримати всі заброньовані місця на цей сеанс
                    $stmt = $conn->prepare("SELECT selected_seats FROM seat_details WHERE show_id = :show_id");
                    $stmt->bindParam(':show_id', $show_id);
                    $stmt->execute();
                    $reservedSeats = [];

                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        $seats = explode(',', $row['selected_seats']);
                        $reservedSeats = array_merge($reservedSeats, $seats);
                    }
                ?>
                <form action="" method="post">
                    <div id="seat-chart">
                        <?php
                            $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                            $cols = 5;

                            foreach($rows as $row){
                                for($col = 1; $col <= $cols; $col++){
                                    $seatNo = $row . $col;
                                    $isReserved = in_array($seatNo, $reservedSeats);

                                    $class = $isReserved ? "seat booked" : "seat";
                                    $disabled = $isReserved ? "style='pointer-events:none'" : "";

                                    echo "<div class='$class' data-seat='$seatNo' $disabled> $seatNo</div>";
                                }
                            }
                        ?>
                        <input type="hidden" name="selected_seats" id="selected-seats">
                        <input type="hidden" name="total_seats" id="total-seats" readonly value="0">
                        <input type="hidden" name="total_price" id="total-price-input" value="0">
                        <div class="detail">
                            <div id="selected-info">Місця не вибрано : 0</div>
                            <div id="total-price">Загальна ціна : 0</div>
                            <button type="submit" name="select_seat" class="btn">Продовжити</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'components/user_footer.php'; ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

        <script type="text/javascript">
            const userBtn = document.querySelector('#user-btn');
            const userBox = document.querySelector('.profile');

            userBtn.addEventListener('click', function() {
                userBox.classList.toggle('active');
            });

            const toggle = document.querySelector('#menu-btn');
            toggle.addEventListener('click', function() {
                const navbar = document.querySelector('.navbar');
                navbar.classList.toggle('active');
            })

            let searchFrom = document.querySelector('.header .flex .search_form');
            document.querySelector('#search_btn').onclick = () =>{
                searchFrom.classList.toggle('active');
                profile.classList.remove('active')
            }

            const seats = document.querySelectorAll('.seat');
            const selectedInput = document.getElementById('selected-seats');
            const totalSeatInput = document.getElementById('total-seats');
            const selectedInfo = document.getElementById('selected-info');
            const totalPriceDisplay = document.getElementById('total-price');

            const seatPrice = 150 //ціна за місце

            seats.forEach(seat => {
                seat.addEventListener('click', () => {
                    if (!seat.classList.contains('booked')) {
                        seat.classList.toggle('selected');
                        updateSelectedSeats();
                    }
                })
            })

            function updateSelectedSeats() {
                const selectedSeats = [...document.querySelectorAll('.seat.selected')]
                    .map(s => s.dataset.seat);

                selectedInput.value = selectedSeats.join(',');
                totalSeatInput.value = selectedSeats.length;

                //оновити текст деталей місця
                if (selectedSeats.length > 0) {
                    selectedInfo.innerHTML = "Ви обрали місце : <br> <span>" + selectedSeats.join(', ')+"</span";
                }else{
                    selectedInfo.textContent = "Місця не вибрано";
                }
                //обчислення загальної вартості
                const totalPrice = selectedSeats.length * seatPrice;
                totalPriceDisplay.innerHTML = "Загальна вартість : <br> <span>$" + totalPrice.toLocaleString() + "</span";
                document.getElementById('total-price-input').value =totalPrice;
            }
        </script>

        <?php include 'components/alert.php'; ?>
    </body>
</html>