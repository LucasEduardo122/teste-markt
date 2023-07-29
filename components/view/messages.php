<?php

if (isset($_SESSION['MESSAGE']) && !empty($_SESSION['MESSAGE'])) {
?>

    <div style="background-color: <?php echo ($_SESSION['MESSAGE']['type'] == 'error') ? 'red' : 'green' ?>; padding: 15px; font-size:15px; margin-top: 0px; width:100%; text-align:center;max-width: 1000px;">
        <p style="font-weight: bold; font-size: 15px; color: #fff !important"> <?php echo $_SESSION['MESSAGE']['message'] ?></p>
    </div>
<?php }
unset($_SESSION['MESSAGE']) ?>