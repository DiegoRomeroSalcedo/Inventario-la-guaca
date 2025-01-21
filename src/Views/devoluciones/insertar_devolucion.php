<?php

ob_start();

$title = "Insertar Devolucion";

?>

<div class="input-container">
    <form id="form-seacrh-factura-devolucion" action="<?='/search-factura-devolucion' ?>" method="post">
        <h1>Insertar Devolucion</h2>
            <div class="container__major">
                <div class="container_inputs">
                    <label for="input-factura">Nro. Factura</label>
                    <input class="form__inputs" id="input-factura" type="number" name="id_factura" required>
                </div>
            </div>
            <div class="container__major">
                <button id="searhc-factur-btn" type="submit">Buscar</button>
            </div>
    </form>
</div>
<div id="results">
    <!-- Resultados de la busqueda -->
    <h2>Detalles Factura</h2>
    <ul class="list-data-product-devo" id="listado-productos-factura"></ul>
    <div class="detalles-factura">
        <div id="factura-total"></div>
        <div id="factura-valor-recibido"></div>
        <div id="factura-valor-devuelto"></div>
        <div id="factura-tipo-pago"></div>
        <div id="factura-cliente"></div>
        <div id="factura-nombre"></div>
        <div id="factura-telefono"></div>
    </div>
    <div style="padding-left: 10px; margin-top: 15px; border: 2px solid #888; border-radius: 6px;">
        <div style="padding-left: 2px;" id="input-factura-div"></div>
        <table id="datos-tributarios" cellspacing="2px"></table>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="total-devolucion">Total Devolucion:</label>
            <input class="form__inputs" type="text" name="total_devolucion" id="total-devolucion">
        </div>
    </div>
    <div class="container__major">
        <div class="container_inputs">
            <label for="motivo-devolucion">Motivo Devolucion: </label>
            <textarea class="form__inputs-text-area text-areatwo" name="motivo_devolucion" id="motivo-devolucion"></textarea>
        </div>
    </div>
    <div class="container__major-button">
        <button class="search__btn" id="finalizar-devolucion-btn" type="submit">Finalizar Devolucion</button>
    </div>
</div>

<?php

$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="' . $script . '"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';
