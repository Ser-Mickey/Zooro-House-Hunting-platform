<?php
session_start();
session_unset();
session_destroy();
header("Location: landlord_login.php");
exit();
?>