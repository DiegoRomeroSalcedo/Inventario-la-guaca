<?php

ob_start();

$title = "Ingresar Venta";

?>

<div id="pageIdentifier" data-page="search-add-venta">
    <form id="search-add-venta" action="<?= '/search-add-venta' ?>" method="post">
        <h1>Insertar Venta</h1>
        <div class="container__major">
            <div class="container_inputs">
                <label for="id_producto">Id Producto </label>
                <input id="id_producto" type="number" name="id_producto" min="0">
            </div>
        </div>
        <div class="container__major">
            <div class="container__button-form">
                <button class="button_form" type="submit">Buscar Producto</button>
            </div>
        </div>
    </form>
    <div class="container__major">
        <div id="results"></div>
        <div id="carrito"></div>
    </div>
    <h3>Carrito</h3>
    <ul class="data-product-carrito" id="carrito-list"></ul>
    <form action="<?= '/search-add-venta' ?>" method="post" id="data-client">
        <div class="container__major">
            <div class="container_inputs" id="container-data">
                <label for="id-client">Cédula Cliente:</label>
                <input class="form__inputs" class="required" id="id-client" type="number" name="dni_client" required value="222222222">
            </div>
            <div class="container_inputs" id="container-data">
                <label for="nombre">Nombre:</label>
                <input class="form__inputs" class="required" id="nombre" type="text" name="nom_client" required value="Cliente Genérico">
            </div>
            <div class="container_inputs" id="container-data">
                <label for="nro-celular">Nro. Celular:</label>
                <input class="form__inputs" id="nro-celular" type="number" name="celular" required value="12121212">
            </div>
        </div>
        <div class="container__major">
            <div class="container_inputs" id="container-data">
                <label for="email">Email:</label>
                <input class="form__inputs" id="email-cliente" type="email" name="email_client" value="laguacasahagun@gmail.com">
            </div>
            <div class="container_inputs" id="container-data">
                <label for="direccion">Direccion:</label>
                <input class="form__inputs" id="direccion-cliente" type="text" name="dir_client" value="samurái">
            </div>
        </div>
    </form>
    <div class="container__major">
        <div class="container_inputs">
            <label for="total-input-value">Total: </label>
            <input class="form__inputs" id="total-input-value" type="text" min="0" readonly>
        </div>
    </div>
    <form action="<?= '/search-add-venta' ?>" method="post" id="form-valor-recibido-devuelto">
        <div class="container__major">
            <div class="container_inputs">
                <label for="valor-recibido">Valor recibido: </label>
                <input class="form__inputs" id="valor-recibido" type="text" name="valor_recibido">
            </div>
            <div class="container_inputs">
                <label for="valor-devuelto">Valor devuelto: </label>
                <input class="form__inputs" id="valor-devuelto" type="text" name="valor-devuelto" readonly>
            </div>
            <div class="container_inputs">
                <label for="tipo-pago">Selcceionar Tipo de Pago: </label>
                <select class="form__inputs" name="tipo_pago" id="tipo-pago">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta-Débito">Tarjeta Débito</option>
                    <option value="Tarjeta-Crédito">Tarjeta Crédito</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Transferencia-Electrónica">Transferencia Electrónica</option>
                </select>
            </div>
        </div>
    </form>
    <div class="container__major">
        <div class="container__major-button">
            <button class="search__btn" type="submit" id="finalizar-venta">Finalizar Venta</button>
        </div>
    </div>
</div>
</div>

<?php


//inclusion de scripts
$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="' . $script . '"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';
