<?php

ob_start();

$title = "Actualizar Marca";

$id_marca = $data['decrypted']['id_marca'];
$nombre = $data['decrypted']['nombre_marca'];

?>

<form id="update_form" action="<?= '/update-form-marca' ?>" method="post">
    <h1>Actualizar Marca</h1>
    <div class="container__major">
        <div class="container_inputs">
            <label for="nom_marca">Nombre Marca</label>
            <input class="form__inputs" id="nom_marca" type="text" name="nombre_marca" value="<?= $nombre ?>">
        </div>
    </div>
    <div class="container_inputs">
        <input id="id_marca" type="hidden" name="id_marca" value="<?= $id_marca ?>">
    </div>
    <div class="container__major-button">
        <button class="search__btn" type="submit">Actualizar</button>
    </div>
</form>

</div>

<?php

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';
