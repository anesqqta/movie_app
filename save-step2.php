<?php

    $show_id = $_POST['show_id'];

    header("location: movie-seat-plan.php?show_id=" . urldecode($show_id));

    exit();
?>