<?php
    session_start();

    if (!isset($_SESSION['booking']['movie_id'])) {
        die('Фільм не вибрано');
    }

    if (isset($_POST['language']) && isset($_POST['formate'])) {
        $_SESSION['booking']['language'] = $_POST['language'];
        $_SESSION['booking']['formate'] = $_POST['formate'];

        header("location: select-show.php");
        exit();
    }else{
        header("location: select-language.php");
        exit();
    }
?>