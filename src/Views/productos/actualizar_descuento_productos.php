<?php

ob_start();

$title = "Descuentos Vencidos";

?>

<div>
    <h1>Productos con Descuentos Vencidos</h1>
</div>
<div id="pageIdentifier" data-page="descuentos-vencidos">

    <table id="descuento-vencidos"  class="display" style="width:100%">
        <thead>
            <th>Id Producto</th>
            <th>Nombre Producto</th>
            <th>Descuento Actual</th>
            <th>Fecha Vencimiento</th>
            <th>Acciones</th>
        </thead>
        <tbody>
            <?php foreach($data['descuentosVencidos'] as $data): ?>
                <tr>
                    <td><?= htmlspecialchars($data['id_product']) ?></td>
                    <td><?= htmlspecialchars($data['no_product']) ?></td>
                    <td><?= htmlspecialchars($data['desc_produ']) ?></td>
                    <td><?= htmlspecialchars($data['fec_fin_descu']) ?></td>
                    <td>
                        <button type="submit" class="quitar-descuento-btn"
                        data-id="<?= htmlspecialchars($data['id_product']) ?>"
                        data-nombre="<?= htmlspecialchars($data['no_product']) ?>"
                        data-costo-final="<?= htmlspecialchars($data['pre_finpro']) ?>"
                        data-precio-venta="<?= htmlspecialchars($data['pre_ventap']) ?>"
                        fec-vencimiento-descuento="<?= htmlspecialchars($data['fec_fin_descu']) ?>"
                        data-descuento-producto="<?= htmlspecialchars($data['desc_produ']) ?>">
                        Quitar
                        </button>
                    </td>
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

//inclusion de librerias externas
$librariesHtml = '';

foreach($this->getLibraries() as $libraries) {
    $librariesHtml .= '<script src="' .$libraries.'"></script>';
}

//inclusion de scripts
$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}


$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';

