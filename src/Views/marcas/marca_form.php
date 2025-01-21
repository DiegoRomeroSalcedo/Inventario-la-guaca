<?php

ob_start();

$title = "Insertar Marca";

if(isset($_GET['id'])) {
    $nombre = $_GET['nombre'];
    $id = intval($_GET['id']);
    echo '<div class="success_insertion">La marca ' . $nombre . ' se registro con Nro. ' . $id . '</div>';
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
<form class="form_insert" action="<?='/add-marcas'?>" method="post">
    <h1>Insertar Marca</h1>
    <div class="container__major">
        <div class="container_inputs">
            <label class="label__input" for="nom_marca">Nombre: </label>
            <input class="form__inputs" type="text" name="nombre_marca" placeholder="Everestt" required>
        </div>
    </div>
    <div class="container__major-button">
            <button class="search__btn" type="submit">Insertar</button>
    </div>
</form>

<?php

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';

?>

