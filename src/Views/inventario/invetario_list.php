<?php

ob_start();

$title = "Tabla Inventario";

?>
<h1>Inventario</h1>
<form id="searchForm" action="<?='/inventario'?>" method="post">
        <div class="container__major">
            <div class="container_inputs">
                <label class="label__input" for="nom_marca">Marca Producto: </label>
                <select class="form__inputs" name="nom_marca" id="nom_marca">
                    <option value="0">Seleccionar Marca</option>
                    <?php foreach($data['idYmarca'] as $marca): ?>
                    <option value="<?= $marca['id_marca'] ?>">
                        <?= $marca['nombre_marca']?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="container_inputs">
                <label for="nom_producto">Nombre Producto: </label>
                <select class="form__inputs" name="marca_producto" id="nom_producto">
                    <option value="0">Seleccionar Producto</option>
                    <?php foreach($data['idYprodu'] as $marca): ?>
                    <option value="<?= $marca['id_product'] ?>">
                        <?= $marca['no_product']?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="container_inputs">
                <label for="det_product">Detalle Producto: </label>
                <textarea class="form__inputs-text-area" name="detalle_pro" id="det_product"></textarea>
            </div>
        </div>
        <div class="container__major">
            <div class="container_inputs">
                <label for="usr_insercion">Usuario Insercion: </label>
                <select class="form__inputs" name="usr_insercion" id="">
                    <option value="0">Usuario Insercion: </option>
                    <?php foreach($data['usr_insact'] as $marca): ?>
                    <option value="<?= $marca['id_user'] ?>">
                        <?= $marca['nombre_usr']?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="container_inputs">
                    <label for="usr_actua">Usuario Actualizacion: </label>
                    <select class="form__inputs" name="usr_actualiza" id="">
                        <option value="0">Usuario Actualizacion</option>
                        <?php foreach($data['usr_insact'] as $marca): ?>
                        <option value="<?= $marca['id_user'] ?>">
                            <?= $marca['nombre_usr']?>
                        </option>
                        <?php endforeach; ?>
                    </select>
            </div>
        </div>
        <div class="container__major">
            <h3>Filtrar por Fecha</h3>
        </div>
        <div class="container__major">
            <div class="container_inputs">
                <label for="startDate">Fecha Inicio:</label>
                <input class="form__inputs" type="text" id="startDate" name="startDate">
            </div>
            <div class="container_inputs">
                <label for="endDate">Fecha Fin:</label>
                <input class="form__inputs" type="text" id="endDate" name="endDate">
            </div>
        </div>
        <div class="container__major-button">
            <button class="search__btn" id="searchBtninventario" type="submit">Buscar</button>
        </div>
        <p id="error-message" style="color: red; display: none;"></p>
</form>

<div id="pageIdentifier" data-page="invenatrio">
    <table id="inventoryTable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Id Producto</th>
                <th>Nombre Producto</th>
                <th>Id Marca</th>
                <th>Nombre Marca</th>
                <th>Cantidad Producto</th>
                <th>Costo Producto</th>
                <th>Retención Producto</th>
                <th>Flete Producto</th>
                <th>Iva Producto</th>
                <th>Costo Final Producto</th>
                <th>Utilidad Producto</th>
                <th>Precio De Venta</th>
                <th>Descuento Producto</th>
                <th>Precio Venta Descuento</th>
                <th>Rentabilidad Producto</th>
                <th>Detalle Producto</th>
                <th>Total Costo Final</th>
                <th>Total Precio Venta</th>
                <th>Dif Cos/Final Pre/Venta</th>
                <th>Total Precio Vent/Desc</th>
                <th>Dif Cos/Final Vent/Desc</th>
                <th>Usuario Inserción</th>
                <th>Fecha Inserción</th>
                <th>Usuario Actualización</th>
                <th>Fecha Actualización</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['defaultData'] as $data): ?>
                <?php 

                    // Elimina las comas del costo del producto y convierte a número flotante
                    $precioFinProstr = (float) str_replace(',', '', $data['pre_finpro']);
                    $precioVentaString = (float) str_replace(',', '', $data['pre_ventap']);
                    $precioVentaDescue = (float) str_replace(',', '', $data['pre_ventades']);

                    // Convierte la cantidad a número flotante, manejando posibles valores nulos o vacíos
                    $cantidad = isset($data['cantidad']) ? (float) $data['cantidad'] : 0;

                    if ($cantidad === 0) {
                        $totalprecioFinPro = $precioFinProstr;
                        $totalPrecioVenPro = $precioVentaString;
                        $difeCostoFinPrecioVen = $totalPrecioVenPro - $totalprecioFinPro;
                        $totalPrecioVentDescue = $precioVentaDescue;
                        $difCostFinPrecVenDesc = $totalPrecioVentDescue - $totalprecioFinPro;
                    } else {
                        $totalprecioFinPro = $precioFinProstr * $cantidad;
                        $totalPrecioVenPro = $precioVentaString * $cantidad;
                        $difeCostoFinPrecioVen = $totalPrecioVenPro - $totalprecioFinPro;
                        $totalPrecioVentDescue = $precioVentaDescue * $cantidad;
                        $difCostFinPrecVenDesc = $totalPrecioVentDescue - $totalprecioFinPro;
                    }

                    // Formatea el total con comas y dos decimales
                    $formattedTotalprecioFinPro = number_format($totalprecioFinPro, 2, '.', ',');
                    $formatttedTotalPrecioVenPro = number_format($totalPrecioVenPro, 2, '.', ',');
                    $formttedDifCostFinPrecVenta = number_format($difeCostoFinPrecioVen, 2, '.', ',');
                    $formattedTotalPrecioVentDescue = number_format($totalPrecioVentDescue, 2, '.', ',');
                    $formattedDifCostFinPrecVenDesc = number_format($difCostFinPrecVenDesc, 2, '.', ',');
                ?>
                <tr>
                    <td><?= htmlspecialchars($data['id_product'])?></td>
                    <td><?= htmlspecialchars($data['no_product'])?></td>
                    <td><?= htmlspecialchars($data['id_marca'])?></td>
                    <td><?= htmlspecialchars($data['nombre_marca'])?></td>
                    <td><?= htmlspecialchars($data['cantidad'])?></td>
                    <td><?= htmlspecialchars(number_format((float) str_replace(',', '', $data['cost_produ']), 2, '.', ',')) ?></td>
                    <td><?= htmlspecialchars($data['rte_fuente'])?></td>
                    <td><?= htmlspecialchars($data['flet_produ'])?></td>
                    <td><?= htmlspecialchars($data['iva_produc'])?></td>
                    <td><?= htmlspecialchars(number_format((float) str_replace(',', '', $data['pre_finpro']), 2, '.', ',')) ?></td>
                    <td><?= htmlspecialchars($data['uti_produc'])?></td>
                    <td><?= htmlspecialchars(number_format((float) str_replace(',', '', $data['pre_ventap']), 2, '.', ',')) ?></td>
                    <td><?= htmlspecialchars($data['desc_produ'])?></td>
                    <td><?= htmlspecialchars(number_format((float) str_replace(',', '', $data['pre_ventades']), 2, '.', ',')) ?></td>
                    <td><?= htmlspecialchars($data['ren_product'])?></td>
                    <td><?= htmlspecialchars($data['detalle_product'] ?? "No reporta")?></td>
                    <th><?= htmlspecialchars($formattedTotalprecioFinPro) ?></th>
                    <th><?= htmlspecialchars($formatttedTotalPrecioVenPro) ?></th>
                    <th><?= htmlspecialchars($formttedDifCostFinPrecVenta) ?></th>
                    <th><?= htmlspecialchars($formattedTotalPrecioVentDescue) ?></th>
                    <th><?= htmlspecialchars($formattedDifCostFinPrecVenDesc) ?></th>
                    <td><?= htmlspecialchars($data['usuario_insercion'])?></td>
                    <td><?= htmlspecialchars($data['feha_insercion'])?></td>
                    <td><?= htmlspecialchars($data['user_actual'])?></td>
                    <td><?= htmlspecialchars($data['fech_actual'])?></td>
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