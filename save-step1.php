<?php
    session_start();

    if (!isset($_SESSION['booking']['movie_id'])) {
        die('Фільм не вибрано');
    }

    $_SESSION['booking']['language'] = $_POST['language'];
    $_SESSION['booking']['formate'] = $_POST['formate'];
    $_SESSION['booking']['time'] = $_POST['time'];
    $_SESSION['booking']['date'] = $_POST['date'];

    header("location: select-show.php");
    exit();
?>