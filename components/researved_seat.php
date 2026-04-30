<div class="researved-seat">
    <div class="heading">
        <span>Заброньовані місця</span>
        <h1>Місця зарезервовано</h1>
    </div>
    <div class="box-container">
        <?php
            $select_reserved = $conn->prepare("SELECT * FROM booking");
            $select_reserved->execute();

            if ($select_reserved->rowCount() > 0) {
                while($fetch_reserved = $select_reserved->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <table cellspacing="0">
            <tr>
                <th>Деталі місця</th>
                <th>Статус</th>
                <th>Дія</th>
            </tr>
            <tr>
                <td><?= $fetch_reserved['seat_details']; ?></td>
                <td style="color: <?php if($fetch_reserved['status'] == 'підтверджено'){echo "green";}else{echo "red";} ?>;"><?= $fetch_reserved['status']; ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="seat_id" value="<?= $fetch_reserved['seat_detail_id']; ?>">
                        <input type="hidden" name="booking_id" value="<?= $fetch_reserved['id']; ?>">
                        <button type="submit" name="delete" class="btn">Видалити</button>
                    </form>
                </td>
            </tr>
        </table>
        <?php
                }
            }else{
                echo '
                <div class="empty">
                    <p>Місця ще не зарезервовано!</p>
                </div>
                ';
            }
        ?>
    </div>
</div>