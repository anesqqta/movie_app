<?php
    session_start();

    $_SESSION['booking']['language'] = $_POST['language'];
    $_SESSION['booking']['formate'] = $_POST['formate'];
    $_SESSION['booking']['time'] = $_POST['time'];
    $_SESSION['booking']['date'] = $_POST['date'];

    header("location: select-show.php?movie_id=" . $_SESSION['booking']['movie_id']);

    exit();
?>