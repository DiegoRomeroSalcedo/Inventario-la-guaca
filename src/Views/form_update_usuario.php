<?php

ob_start();

$title = "Actualizar contraseña";

// print_r($data);

$id_usuario = $data['desencriptado']['id_user'];    

?>

<form action="<?='/form-update-user'?>" method="post">
    <label for="usuario">Nombre Usuario</label>
    <input type="usuario" name="nombre_usuario" required>
    <label for="contraseña">Nueva Contraseña</label>
    <input type="password" name="contraseña" required>
    <input type="hidden" value="<?= $id_usuario ?>" name="id_usuario">
    <button type="submit">Actualizar</button>
</form>


<?php

$content = ob_get_clean();

include __DIR__ . '/layouts/layout.php';