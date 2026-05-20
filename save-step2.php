<?php
    session_start();

    if (isset($_POST['show_id'])) {
        $_SESSION['booking']['show_id'] = $_POST['show_id'];
        $_SESSION['booking']['date'] = $_POST['show_date'];
        $_SESSION['booking']['time'] = $_POST['show_time'];

        header("location: movie-seat-plan.php?show_id=" . urlencode($_POST['show_id']));
        exit();
    }else{
        header("location: select-show.php");
        exit();
    }
?>