<?php 

ob_start();

$title = "Listar Clientes";

?>

<div id="pageIdentifier" data-page="clientes">
    <table id="contable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Id Cliente</th>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Télefono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Compras</th>
                <th>Total Compras</th>
                <th>Fecha Ultima Compra</th>
                <th>Devoluciones</th>
                <th>Fecha Ultima Devolucion</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['clientes'] as $cliente) : ?>
            <tr>
                <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
                <td><?= htmlspecialchars($cliente['identificacion']) ?></td>
                <td><?= !empty($cliente['Nombre']) ? htmlspecialchars($cliente['Nombre']) : 'No reporta' ?></td>
                <td><?= !empty($cliente['telefono']) ? htmlspecialchars($cliente['telefono']) : 'No reporta' ?></td>
                <td><?= !empty($cliente['email']) ? htmlspecialchars($cliente['email']) : 'No reporta' ?></td>
                <td><?= !empty($cliente['direccion']) ? htmlspecialchars($cliente['direccion']) : 'No reporta' ?></td>
                <td><?= !empty($cliente['cantidad_compras']) ? htmlspecialchars($cliente['cantidad_compras']) : 0 ?></td>
                <td><?= !empty($cliente['total_compras']) ? number_format((float) $cliente['total_compras'], 2, '.', ',') : 'No reporta' ?></td>
                <td><?= !empty($cliente['fecha_ultima_compra']) ? htmlspecialchars($cliente['fecha_ultima_compra']) : 'No reporta' ?></td>
                <td><?= !empty($cliente['devoluciones']) ? htmlspecialchars($cliente['devoluciones']) : 'No reporta' ?></td>
                <td><?= !empty($cliente['ultima_devolucion']) ? htmlspecialchars($cliente['ultima_devolucion']) : 'No reporta' ?></td>
                <td><?= htmlspecialchars($cliente['fecha_registro']) ?></td>
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