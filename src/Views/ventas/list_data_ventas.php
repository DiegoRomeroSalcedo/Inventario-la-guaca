<?php

ob_start();

$title = "Listado de Ventas";

// echo "<pre>";
// print_r($data['ventas'][0]);
// echo "</pre>";

?>

<div id="pageIdentifier" data-page="ventas">
    <table id="contable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Id Venta</th>
                <th>Factura</th>
                <th>Nombre Producto</th>
                <th>Nombre Marca</th>
                <th>Cantidad Unidades</th>
                <th>Precio Unitario</th>
                <th>Monto Venta</th>
                <th>Identificación Cliente</th>
                <th>Nombre Cliente</th>
                <th>Usuario Inserción</th>
                <th>Fecha Inserción</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                
                function formatFacturNumber($id) {
                    return 'FAC' . str_pad($id, 6, '0', STR_PAD_LEFT);
                }
            ?>
            <?php foreach ($data['ventas'] as $ventas) : ?>
            <?php

            $id = $ventas['id_factura'];

            $formatIdFactur = formatFacturNumber($id);

            ?>
            <tr>
                <td><?= htmlspecialchars($ventas['id_venta']) ?></td>
                <td><?= htmlspecialchars($formatIdFactur) ?></td>
                <td><?= htmlspecialchars($ventas['no_product']) ?></td>
                <td><?= htmlspecialchars($ventas['nombre_marca']) ?></td>
                <td><?= htmlspecialchars($ventas['cantidad']) ?></td>
                <td><?= htmlspecialchars(number_format((float) $ventas['precio_unitario'], 2, '.', ',')) ?></td>
                <td><?= htmlspecialchars(number_format( (float) $ventas['monto_venta'], 2, '.', ',')) ?></td>
                <td><?= !empty($ventas['identificacion']) ? htmlspecialchars($ventas['identificacion']) : 'No reporta'?></td>
                <td><?= !empty($ventas['Nombre']) ? htmlspecialchars($ventas['Nombre']) : 'No reporta' ?></td>
                <td><?= !empty($ventas['usuario_insercion']) ? htmlspecialchars($ventas['usuario_insercion']) : 'No reporta' ?></td>
                <td><?= !empty($ventas['fecha']) ? htmlspecialchars($ventas['fecha']) : 'No reporta' ?></td>
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