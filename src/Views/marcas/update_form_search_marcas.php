<?php

use Proyecto\Utils\Encryption;

ob_start();

$title = "Buscar Marcas";

if (isset($_GET['data']) && !empty($_GET['data'])) {
    $encryptedData = $_GET['data'];
    $decryptedData = Encryption::decrypt($encryptedData);
    $marca = $decryptedData['nombre'];
    $idMarca = $decryptedData['id_marca'];
    echo '<div class="success_insertion">Se actualizo la marca: ' . $marca . ' con ID: ' . $idMarca . '</div>';
}

if (isset($_SESSION['error'])) {
    echo '<section class="error__duplicatedentry">' . $_SESSION['error'] . '</section>';
    unset($_SESSION['error']);
}

if (isset($_SESSION['error_sql'])) {
    echo '<section class="error_sql">' . $_SESSION['error_sql'] . '</section>';
    unset($_SESSION['error_sql']);
}


?>
<!-- Contenido HTML -->

<div id="pageIdentifier" data-page="search-update-marca">
    <form id="update_form_marcas" action="<?= '/search-update-marcas' ?>" method="post">
        <h1>Actualizar Marca</h1>
        <div class="container__major">
            <div class="container_inputs">
                <label for="id_marca">Id Marca</label>
                <input class="form__inputs" id="id_marca" type="number" name="id_marca" required>
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

// inclusion de scripts
$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="' . $script . '"></script>';
}

$stylesCss = '';

foreach ($this->getStyles() as $style) {
    $stylesCss .= '<link rel="stylesheet" href="' . $style . '">';
}

$content = ob_get_clean();


include __DIR__ . '/../layouts/layout.php';
