<?php
session_start();
// Destruir sesión
session_destroy();
// Redirigir a la página de inicio de sesión
header("Location: login.php");
exit();
?> 