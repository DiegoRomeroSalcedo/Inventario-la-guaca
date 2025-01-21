<?php

ob_start();

$title = "Listar devoluciones";

?>

<div id="pageIdentifier" data-page="facturas">
    <table id="contable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Id Detalle</th>
                <th>Id Factura</th>
                <th>Nombre Producto</th>
                <th>Nombre Marca</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>Motivo</th>
                <th>Cédula Cliente</th>
                <th>Nombre</th>
                <th>Usuario Insercion</th>
            </tr>
        </thead>
        <tbody>
                <?php 
                    //Formateamos el id de la factura para que muestre FAC
                    function formattedFactura($id_factura) {
                        return 'FAC' . str_pad($id_factura, 6, '0', STR_PAD_LEFT);
                    }
                ?>
            <?php foreach($data['data'] as $devo): ?>
                <?php 
                    $id_factura = $devo['id_factura'];
                    $formarFactur = formattedFactura($id_factura);
                ?>
                <tr>
                    <td><?= htmlspecialchars($devo['id_detalle']) ?></td>
                    <td><?= htmlspecialchars($formarFactur) ?></td>
                    <td><?= htmlspecialchars($devo['no_product']) ?></td>
                    <td><?= htmlspecialchars($devo['nombre_marca']) ?></td>
                    <td><?= htmlspecialchars($devo['cantidad']) ?></td>
                    <td><?= !empty($devo['precio_unitario']) ? htmlspecialchars(number_format( (float) str_replace(',', '', $devo['precio_unitario']), 2, '.', ',')) : 0 ?></td>
                    <td><?= !empty($devo['subtotal']) ? htmlspecialchars( number_format((float) str_replace(',', '', $devo['subtotal']), 2, '.', ',')) : 0 ?></td>
                    <td><?= htmlspecialchars($devo['motivo']) ?? 'No reporta' ?></td>
                    <td><?= htmlspecialchars($devo['identificacion']) ?? 'No reporta' ?></td>
                    <td><?= htmlspecialchars($devo['Nombre']) ?? 'No reporta' ?></td>
                    <td><?= htmlspecialchars($devo['usuario_insercion']) ?></td>
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

?>