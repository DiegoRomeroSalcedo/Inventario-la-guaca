<?php 

ob_start();

$title = "Insertar Cantidad Producto";

$idProducto = $data['decrypted']['id_product'];
$nomProduct = $data['decrypted']['no_product'];

?>

<form class="form_insert" action="<?='/add-cantidad-product'?>" method="post">
    <h1>Insertar Cantidad</h1>
    <div class="container__major">
            <div class="container_inputs">
                <label for="id_producto">Id Producto: </label>
                <input class="form__inputs" type="int" name="id_producto" value="<?= $idProducto?>" readonly>
            </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="cantidad">Cantidad Producto: </label>
            <input class="form__inputs" id="cantidad" type="int" name="cantidad" min="0" max="1000">
            <input id="rango_cantidad" type="range" min="0" max="5000" name="rango_cantidad">
            <input class="form__inputs" type="hidden" name="nom_product" value="<?= $nomProduct ?>">
        </div>  
    </div>
    <div class="container__major">
        <div class="container__major-button">
                <button class="search__btn" type="submit">Insertar</button>
        </div>
    </div>
</form>

<?php

$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';