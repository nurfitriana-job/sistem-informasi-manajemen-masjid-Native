<?php
session_start();
if (isset($_POST['clear'])) {
    $_SESSION['activePrayer'] = '';
    echo 'OK';
}
