<?php

ob_start();

use Proyecto\Utils\Encryption;

$title = "Actualizar usuario";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    echo '<div class="success_insertion">' . $message . '</div>';
}


?>

<table style="border-spacing: 0;padding: 10px; margin: 0 auto;">
    <thead>
        <tr>
            <th style="border-bottom: 1px solid black;padding: 15px;">Usuario</th>
            <th style="border-bottom: 1px solid black;padding: 15px;">Rol Usuario</th>
            <th style="border-bottom: 1px solid black;padding: 15px;">Accion</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['data'] as $data): ?>
            <?php
            $ecripted_data = Encryption::encrypt($data);
            if ($_SESSION['username'] !== 'Diegosalcedo') {
                if ($data['username'] == 'Diegosalcedo') {
                    continue; //Omite el resto del codigo y pasa al siguiente elemento
                }
            }
            ?>
            <tr>
                <th style="border-bottom: 1px solid black;padding: 15px;" style=""><?= htmlspecialchars($data['username']) ?></th>
                <th style="border-bottom: 1px solid black;padding: 15px;"><?= htmlspecialchars($data['role_id'] == 1 ? 'Administrador' : 'Usuario') ?></th>
                <th style="border-bottom: 1px solid black;padding: 15px;">
                    <a class="update__user_link" href="<?= '/form-update-user?data=' . $ecripted_data ?>">Actualizar</a>
                </th>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php

$content = ob_get_clean();

include __DIR__ . '/layouts/layout.php';
