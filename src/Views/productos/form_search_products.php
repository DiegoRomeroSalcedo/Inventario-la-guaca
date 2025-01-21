<?php

ob_start();

use Proyecto\Utils\Encryption;

$title = "Buscar Producto";

if($_SERVER['REQUEST_METHOD'] == 'GET'){

    if(isset($_GET['data']) && !empty($_GET['data'])) {
        $encriptedData = $_GET['data'];
        $desencrytedData = Encryption::decrypt($encriptedData);

        if($_GET['tipo'] == 'actualizado') {
            $id = $desencrytedData['id'];
            $nombre = $desencrytedData['nombre'];
            $cantidad = $_GET['cantidad'];
            echo '<div class="success_insertion"> Se actualizo la cantidad del producto ' . $nombre . ' con ID: ' . $id . ' a ' . $cantidad . '</div>';
        } elseif($_GET['tipo'] == 'insertado') {
            $id = $desencrytedData['id'];
            $nombre = $desencrytedData['nombre'];
            $cantidadinsertada = $desencrytedData['cantidad'];
            echo '<div class="success_insertion">Se inserto la cantidad de: ' . $cantidadinsertada . ' para el producto ' . $nombre . ' con ID: ' . $id . '</div>';
        }
    }

    if(isset($_SESSION['error'])) {
        echo '<section class="error__duplicatedentry">' . $_SESSION['error'] . '</section>';
        unset($_SESSION['error']);
    }
    
    if(isset($_SESSION['error_sql'])) {
        echo '<section class="error_sql">' . $_SESSION['error_sql'] . '</section>';
        unset($_SESSION['error_sql']);
    }
}


?>

<form id="search_product_form" class="form_insert" action="<?='/get-add-cantidades'?>" method="post">
    <h1>Buscar Productos</h1>
    <div class="container__major">
        <div class="container_inputs">
            <label for="nom_product">Producto: </label>
            <select class="form__inputs" name="producto" id="nom_producto">
                <option value="0">Productos</option>
                <?php foreach($data['productos'] as $p): ?>
                    <option value="<?= $p['id_product'] ?>">
                        <?= htmlspecialchars($p['no_product']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="container_inputs">
            <label for="nom_marca">Marca Producto: </label>
            <select class="form__inputs" name="marca" id="nom_marca">
                <option value="0">Marcas</option>
                <?php foreach($data['marca'] as $m): ?>
                    <option value="<?= $m['id_marca'] ?>" <?= (isset($_POST['marca']) && $_POST['marca'] == $m['id_marca']) ? 'selected' : '' ?>>
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
    <!-- Resultados de la busqueda con JS -->
</div>

<?php 

$content = ob_get_clean();

$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}

$stylesCss = '';

foreach ($this->getStyles() as $style) {
    $stylesCss .= '<link rel="stylesheet" href="' . $style . '">';
}


include __DIR__ . '/../layouts/layout.php';

?>