<?php
use Proyecto\Utils\Encryption;

ob_start();

$title = "Buscar Producto";

if(isset($_GET['data']) && !empty($_GET['data'])) {
    $encryptedData = $_GET['data'];
    $decryptedData = Encryption::decrypt($encryptedData);
    $idProducto = $decryptedData['id_product'];
    $nomProduct = $decryptedData['nom_produc'];
    echo '<div class="success_insertion">Se actualizo el producto: ' . $nomProduct . ' con id: ' . $idProducto . '</div>';
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

<div id="pageIdentifier" data-page="search-update-producto">
    <form id="search-update-product" action="<?='/search-update-productos'?>" method="post">
        <h1>Actualizar Productos</h1>
        <div class="container__major">
            <div class="container_inputs">
                <label for="idProduct">Id Producto: </label>
                <input class="form__inputs" id="idProduct" type="number" name="id_producto">
            </div>
            <div class="container_inputs">
                <label for="nom_marca">Marca Producto: </label>
                <select class="form__inputs" name="marca_producto" id="nom_marca">
                    <option value="0">Marcas</option>
                    <?php foreach($data['marcas'] as $m): ?>
                        <option value="<?= $m['id_marca'] ?>">
                            <?= htmlspecialchars($m['nombre_marca'])?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="container__major-button">
            <button class="search__btn" type="submit">Buscar</button>
        </div>
    </form>
    <div id="results">
            <!-- Contenido del resultado -->
    </div>
</div>

<?php  

$stylesCss = '';

foreach ($this->getStyles() as $style) {
    $stylesCss .= '<link rel="stylesheet" href="' . $style . '">';
}

$stylesLibraries = '';

foreach ($this->getStylesLibraries() as $style) {
    $stylesLibraries .= '<link rel="stylesheet" href="' . $style . '">';
}

$librariesHtml = '';

foreach($this->getLibraries() as $library) {
    $librariesHtml  .= '<script src="' .$library.'"></script>';
}

$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';

?>