<?php 

ob_start();

$title = "Listar Clientes";

// print_r($data);

?>

<div id="pageIdentifier" data-page="facturas">
    <table id="contable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Id factura</th>
                <th>Factura</th>
                <th>Productos</th>
                <th>Total</th>
                <th>Valor Recibido</th>
                <th>Valor Devuelto</th>
                <th>Método de Pago</th>
                <th>Id Cliente</th>
                <th>Identificación</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Fecha Inserción</th>
                <th>Usuario Inserción</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                
                function formatFacturNumber($id) {
                    return 'FAC' . str_pad($id, 6, '0', STR_PAD_LEFT);
                }

                function formatMetodoDePago($metodo) {
                    return mb_strtoupper(str_replace('-', ' ', $metodo), 'UTF-8');
                }
            ?>
            <?php foreach ($data['facturas'] as $facturas) : ?>
            <?php

            $id = $facturas['id_factura'];
            $metodo = $facturas['tipo_pago'];

            $formatIdFactur = formatFacturNumber($id);
            $formatTipoPago = formatMetodoDePago($metodo);

            ?>
            <tr>
                <td><?= htmlspecialchars($facturas['id_factura']) ?></td>
                <td><?= htmlspecialchars($formatIdFactur) ?></td>
                <td><?= htmlspecialchars($facturas['total_productos']) ?></td>
                <td><?= !empty($facturas['total_venta']) ? htmlspecialchars(number_format((float) str_replace(',', '', $facturas['total_venta']), 2, '.', ',')) : 0?></td>
                <td><?= !empty($facturas['valor_recibido']) ? htmlspecialchars(number_format((float) str_replace(',', '', $facturas['valor_recibido']), 2, '.', ',')) : 0?></td>
                <td><?= !empty($facturas['valor_devuelto']) ? htmlspecialchars(number_format((float) str_replace(',', '', $facturas['valor_devuelto']), 2, '.', ',')) : 0?></td>
                <td><?= !empty($formatTipoPago) ? htmlspecialchars($formatTipoPago) : 'No reporta' ?></td>
                <td><?= !empty($facturas['id_cliente']) ? htmlspecialchars($facturas['id_cliente']) : 'No reporta' ?></td>
                <td><?= !empty($facturas['identificacion']) ? htmlspecialchars($facturas['identificacion']) : 'No reporta' ?></td>
                <td><?= !empty($facturas['Nombre']) ? htmlspecialchars($facturas['Nombre']) : 'No reporta' ?></td>
                <td><?= !empty($facturas['telefono']) ? htmlspecialchars($facturas['telefono']) : 'No reporta' ?></td>
                <td><?= !empty($facturas['fecha']) ? htmlspecialchars($facturas['fecha']) : 'No reporta'?></td>
                <td><?= !empty($facturas['usuario_insercion']) ? htmlspecialchars($facturas['usuario_insercion']) : 'No reporta' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php

//Inclusion de estilos externos
$stylesLibraries = '';

foreach ($this->getStylesLibraries() as $style) {
    $stylesLibraries .= '<link rel="stylesheet" href="' . $style . '">';
}

//inclusion de librerias
$librariesHtml = '';

foreach($this->getLibraries() as $library) {
    $librariesHtml  .= '<script src="' .$library.'"></script>';
}

//inclusion de scripts
$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';