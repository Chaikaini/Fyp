<?php
session_start();
session_unset();
session_destroy();
header('Location: 888.html');
exit;
?>