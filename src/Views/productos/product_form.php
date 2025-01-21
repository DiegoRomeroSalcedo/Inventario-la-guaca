<?php

ob_start();

$title = "Insertar Productos";

if(isset($_GET['id'])) {
    $nombre = $_GET['nombre'];
    $marcaget = $_GET['marca'];
    $id = intval($_GET['id']);
    echo '<div class="success_insertion">El producto: ' . $nombre . ' marca: ' . $marcaget . ' se registo con Nro. ' . $id . '</div>';
}

if(isset($_SESSION['error'])) {
    echo '<section class="error__duplicatedentry">' . $_SESSION['error'] . '</section>';
    unset($_SESSION['error']);
}

if(isset($_SESSION['error_sql'])) {
    echo '<section class="error_sql">' . $_SESSION['error_sql'] . '</section>';
    unset($_SESSION['error_sql']);
}

?>
<form class="form_insert" action="<?='/add-productos'?>" method="post">
    <h1>Insertar Producto</h1>
    <div class="container__major">
        <div class="container_inputs">
            <label for="nom_produc">Nombre Producto: </label>
            <input class="form__inputs" id="nom_produc" type="text" name="nom_produc" placeholder="Bujía" required>
        </div>
        <div class="container_inputs">
            <label for="nom_marca">Marca Producto: </label>
            <select class="form__inputs" name="marca_producto" id="nom_marca">
                <?php foreach($data['marca'] as $marca): ?>
                <option value="<?= $marca['id_marca'] ?>">
                    <?= $marca['nombre_marca']?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="container_inputs">
            <label for="precio_costo">Precio Costo: </label>
            <input class="form__inputs" id="precio_costo" type="text" name="cost_produ" placeholder="97,860.89" required>
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="retefuente">Retención %: </label>
            <input class="form__inputs" id="retefuente" type="number" min="0" step="0.01" name="porc_rete" placeholder="2.5">
        </div>
        <div class="container_inputs">
            <label for="costo_flete">Flete %: </label>
            <input class="form__inputs" id="costo_flete" type="number" min="0" step="0.01" name="porc_flete" placeholder="3">
        </div>
        <div class="container_inputs">
            <label for="costo_iva">IVA %: </label>
            <input class="form__inputs" id="costo_iva" type="number" min="0" step="0.01" name="porc_iva" placeholder="19">
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="costo_final">Costo Final: </label>
            <input class="form__inputs" id="costo_final" type="text" name="pre_finpro" readonly>
        </div>
        <div class="container_inputs">
            <label for="utilidad">Utilidad: </label>
            <input class="form__inputs" id="utilidad" type="number" name="uti_product" placeholder="50">
        </div>
        <div class="container_inputs">
            <label for="precio_venta">Precio de Venta: </label>
            <input class="form__inputs" id="precio_venta" type="text" name="pre_ventap" readonly>
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="toggleCheckbox">Desea Aplicar Descuento</label>
            <input type="hidden" name="aplica_descuento" value="0">
            <input class="aplica_descuento_input" id="toggleCheckbox" type="checkbox" value="1" name="aplica_descuento">
        </div>
    </div>
    <div class="container__major">
        <div id="extrafileds" class="container_inputs hidden">
            <label for="descuento">Descuento %: </label>
            <input class="form__inputs" id="descuento" type="number" min="0" step="0.01" name="des_product" placeholder="10">
        </div>
        <div id="extrafileds_two" class="container_inputs container__inputs-ydate hidden">
            <div class="container_inputs">
                <label for="precioventa_desc">Precio con Descuento: </label>
                <input class="form__inputs" id="precioventa_desc" type="text" name="pre_ventades" readonly>
            </div>
            <div class="container_inputs">
                <label for="endDate">Fecha Fin:</label>
                <input class="form__inputs" type="text" id="endDate" name="endDate">
            </div>
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="rentabilidad">Rentabilidad %: </label>
            <input class="form__inputs" id="rentabilidad" type="number" min="0" step="0.01" name="rentabilidad">
        </div>
        <div class="container_inputs">
        <label for="detalle">Detalle Producto: </label>
        <textarea class="form__inputs-text-area" name="detalle_produc" id="detalle"></textarea>
        </div>
    </div>
    <div class="container__major-button">
            <button class="search__btn" type="submit">Insertar</button>
    </div>
</form>

<?php

$content = ob_get_clean();

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

include __DIR__ . '/../layouts/layout.php';

?>