<?php
// Cambia 'TuNuevaClave2026!' por la contraseña robusta que quieras
$nueva_clave = "EnDiosconfiamos110709**"; 

echo "Tu nueva clave encriptada es: <br>";
echo password_hash($nueva_clave, PASSWORD_DEFAULT);
?>