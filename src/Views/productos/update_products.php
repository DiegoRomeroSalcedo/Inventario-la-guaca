<?php

ob_start();

$title = "Actualizar Producto";

$marcas = $data['marcas'];
$data = $data['decryptedData'];
$marcaTraida = $data['id_marca'];
$showExtraFields = $data['desc_produ'] == 0;
?>

<form class="form_insert" action="<?='/update-form-product'?>" method="post">
    <h1>Actualizar Producto</h1>
    <div class="container__major">
        <div class="container_inputs">
            <label for="nom_produc">Nombre Producto: </label>
            <input class="form__inputs" id="nom_produc" type="text" name="nom_produc" value="<?= $data['no_product'] ?>" required>
        </div>
        <div class="container_inputs">
            <label for="nom_marca">Marca Producto: </label>
            <select class="form__inputs" name="marca_producto" id="nom_marca">
                <?php foreach($marcas as $marca): ?>
                <option value="<?= htmlspecialchars($marca['id_marca']) ?>" <?= $marca['id_marca'] == $marcaTraida ? 'selected' : '' ?>>
                    <?= $marca['nombre_marca']?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="container_inputs">
            <label for="precio_costo">Precio Costo: </label>
            <input class="form__inputs" id="precio_costo" type="text" name="cost_produ" value="<?= htmlspecialchars(number_format((float) str_replace(',', '', $data['cost_produ']), 2, '.', ','))?>">
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="retefuente">Retención %: </label>
            <input class="form__inputs" id="retefuente" type="number" min="0" step="0.01" name="porc_rete" value="<?= htmlspecialchars($data['rte_fuente'])?>">
        </div>
        <div class="container_inputs">
            <label for="costo_flete">Flete %: </label>
            <input class="form__inputs" id="costo_flete" type="number" min="0" step="0.01" name="porc_flete" value="<?= htmlspecialchars($data['flet_produ'])?>">
        </div>
        <div class="container_inputs">
            <label for="costo_iva">IVA %: </label>
            <input class="form__inputs" id="costo_iva" type="number" min="0" step="0.01" name="porc_iva" value="<?= htmlspecialchars($data['iva_produc'])?>">
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="costo_final">Costo Final: </label>
            <input class="form__inputs" id="costo_final" type="text" name="pre_finpro"  value="<?= htmlspecialchars(number_format((float) str_replace(',', '', $data['pre_finpro']), 2, '.', ','))?>">
        </div>
        <div class="container_inputs">
            <label for="utilidad">Utilidad: </label>
            <input class="form__inputs" id="utilidad" type="number" name="uti_product" value="<?= htmlspecialchars($data['uti_produc'])?>">
        </div>
        <div class="container_inputs">
            <label for="precio_venta">Precio de Venta: </label>
            <input class="form__inputs" id="precio_venta" type="text" name="pre_ventap"  value="<?= htmlspecialchars(number_format((float) str_replace(',', '', $data['pre_ventap']), 2, '.', ','))?>">
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="toggleCheckbox">Desea Aplicar descuento</label>
            <input class="aplica_descuento_input" id="toggleCheckbox" type="checkbox" value="1" name="aplica_descuento" <?= $data['desc_produ'] == 0 ? '' : 'checked' ?>>
        </div>
    </div>
    <div class="container__major">
        <div id="extrafileds" class="container_inputs <?= $showExtraFields ? 'hidden' : '' ?>">
            <label for="descuento">Descuento %: </label>
            <input class="form__inputs" id="descuento" type="number" min="0" step="0.01" name="des_product" value="<?= htmlspecialchars($data['desc_produ']) ?>">
        </div>
        <div id="extrafileds_two" class="container_inputs container__inputs-ydate<?= $showExtraFields ? 'hidden' : '' ?>">
            <div class="container_inputs">
                <label for="precioventa_desc">Precio con Descuento: </label>
                <input class="form__inputs" id="precioventa_desc" type="text" name="pre_ventades" value="<?= $data['desc_produ'] == 0 ? '' : htmlspecialchars(number_format((float) str_replace(',', '', $data['pre_ventades']), 2, '.', ',')) ?>">
            </div>
            <div class="container_inputs">
                <label for="endDate">Fecha Fin:</label>
                <input class="form__inputs" type="text" id="endDate" name="endDate" value="<?= $data['fec_fin_descu']?>">
            </div>
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="rentabilidad">Rentabilidad %: </label>
            <input class="form__inputs" id="rentabilidad" type="number" min="0" step="0.01" name="rentabilidad"  value="<?= htmlspecialchars($data['ren_product'])?>">
        </div>
        <div class="container_inputs">
            <label for="detalle">Detalle Producto: </label>
            <textarea class="form__inputs-text-area" name="detalle_produc" id="detalle"> <?= htmlspecialchars(isset($data['detalle_product']) ? $data['detalle_product'] : '' ) ?> </textarea>
        </div>
        <div class="container_inputs">
            <label for="cantidad">Cantidad: </label>
            <input class="form__inputs" id="cantidad" type="number" min="0" step="0.01" name="cantidad"  value="<?= htmlspecialchars($data['cantidad'])?>">
        </div>
    </div>
    <input type="hidden" name="id_producto" value="<?= $data['id_product']?>">
    <div class="container__major-button">
            <button class="search__btn" type="submit">Actualizar</button>
    </div>
</form>
<?php


//Inclusion de estilos externos
$stylesLibraries = '';

foreach ($this->getStylesLibraries() as $style) {
    $stylesLibraries .= '<link rel="stylesheet" href="' . $style . '">';
}

//inclusion de librerias externas
$librariesHtml = '';

foreach($this->getLibraries() as $libraries) {
    $librariesHtml .= '<script src="' .$libraries.'"></script>';
}

//inclusion de scripts
$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';