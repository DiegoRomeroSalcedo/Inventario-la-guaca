<?php

namespace Proyecto\Controller;

use Proyecto\Models\Productos;
use Proyecto\Models\Marcas;
use Views\View\View;
use Proyecto\Utils\Encryption;

class ProductoController
{

    protected $pdo;
    protected $view;
    protected $productosModel;
    protected $marcasModel;
    public $carpeta = 'productos';

    // Aplicamos la inyeccion de dependencias para evitar instanciar clases y metdodos a cada rato
    public function __construct($pdo, View $view, Productos $productosModel, Marcas $marcasModel)
    {
        $this->pdo = $pdo;
        $this->view = $view;
        $this->productosModel = $productosModel;
        $this->marcasModel = $marcasModel;
    }

    public function list()
    {
        // Obtenemos la lista de los productos, para listar en datatable
        $productos = $this->productosModel->getAll(); // de esta manera nos ahorramos el tener que instanciar con new

        //scripts propios
        $this->view->addScripts('jsdatatables.js');

        //estilos externos
        $this->view->addStylesExternos('https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css');
        $this->view->addStylesExternos('https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css');

        //librerias
        $this->view->addLibraries('https://code.jquery.com/jquery-3.7.1.js');
        $this->view->addLibraries('https://cdn.datatables.net/2.1.3/js/dataTables.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js');
        $this->view->assign('productos', $productos); // No fue necesario el dat, ya que solo pase una variable en concreto.
        $this->view->render('product_list.php', $this->carpeta);
    }

    public function addProducto()
    {

        $marca = $this->marcasModel->getIdNombre();

        $data = [
            'marca' => $marca,
            'producto' => []
        ];

        // Siempre agregamos el script, para que sea en tiempo real
        $this->view->addScripts('insert_product.js');

        $this->view->addStylesExternos('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
        $this->view->addStylesExternos('https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');


        //librearias externas
        $this->view->addLibraries('https://code.jquery.com/jquery-3.7.1.js');
        $this->view->addLibraries('https://code.jquery.com/ui/1.13.2/jquery-ui.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js');


        if ($_SERVER["REQUEST_METHOD"] == 'POST') {

            $descuento = isset($_POST['des_product']) ? $_POST['des_product'] : 0;
            $pre_ventades = isset($_POST['pre_ventades']) ? $_POST['pre_ventades'] : 0;
            $detalle = $_POST['detalle_produc'] ?? null;
            $det_min = str_replace(' ', ',', strtolower(trim($detalle)));
            $aplicaDescuento = isset($_POST['aplica_descuento']) ? $_POST['aplica_descuento'] : 0;

            $datos = [
                ':id_product'            => "",
                ':no_product'            => $_POST['nom_produc'] ?? null,
                ':id_marcapr'            => $_POST['marca_producto'] ?? null,
                ':cost_produ'            => $_POST['cost_produ'] ?? null,
                ':rte_fuente'            => $_POST['porc_rete'] ?? null,
                ':flet_produ'            => $_POST['porc_flete'] ?? null,
                ':iva_produc'            => $_POST['porc_iva'] ?? null,
                ':pre_finpro'            => $_POST['pre_finpro'] ?? null,
                ':uti_produc'            => $_POST['uti_product'] ?? null,
                ':pre_ventap'            => $_POST['pre_ventap'] ?? null,
                ':descuento'             => $aplicaDescuento,
                ':desc_produ'            => $descuento ?? null,
                ':pre_ventades'          => $pre_ventades ?? null,
                ':fec_fin_descu'               => $_POST['endDate'] ?? null,
                ':ren_product'           => $_POST['rentabilidad'] ?? null,
                ':detalle_product'       => $det_min,
                ':usuario_insercion'     => $_SESSION['username'] ?? null,
                ':usuario_actualizacion' => $_SESSION['username'] ?? null,
            ];

            $producto = $this->productosModel->insertarProducto($datos);

            $data['producto'] = $producto;
        }

        // Asignamos los datos a la vista
        $this->view->assign('data', $data);
        $this->view->render('product_form.php', $this->carpeta);
    }

    public function search()
    {
        $productos = $this->productosModel->getIdNombre();
        $marca = $this->marcasModel->getIdNombre();

        // preparamos los datos por defecto para la vista
        $data = [
            'marca' => $marca,
            'productos' => $productos,
        ];

        $obtained = [
            'productos' => []
        ];

        $this->view->addScripts('asincronous_search_products.js');
        $this->view->addStyles('styles_search_products.css');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_producto = $_POST['producto'];

            $id_marca = isset($_POST['marca']) && $_POST['marca'] != '0' ? $_POST['marca'] : '%';

            $productos = $this->productosModel->getProductData($id_producto, $id_marca);

            // Encriptar cada producto individualmente
            foreach ($productos as &$producto) {
                $datosaEncriptar = [
                    'id_product' => $producto['id_product'],
                    'no_product' => $producto['no_product'],
                    'id_marca' => $producto['id_marca'],
                ];
                $producto['encriptados'] = Encryption::encrypt($datosaEncriptar);
            }

            // Actualiza los datos con los productos encontrados
            $obtained['productos'] = $productos;

            // Envía la respuesta como JSON
            header('Content-Type: application/json');
            echo json_encode($obtained);
            exit();
        }

        // Asignamos los datos a la vista
        $this->view->assign('data', $data);
        $this->view->render('form_search_products.php', $this->carpeta);
    }


    public function addCantidadProducto()
    {

        $data = [
            'decrypted' => [],
            'insertion' => []
        ];

        $this->view->addScripts('insert_cantidad_productos.js');

        if (isset($_GET['data']) && !empty($_GET['data'])) {
            $encriptedData = $_GET['data'];

            $decryptedData = Encryption::decrypt($encriptedData);

            $data['decrypted'] = $decryptedData;
        }

        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            $username = $_SESSION['username'];
            $idProducto = $_POST['id_producto'];
            $cantidadPro = $_POST['cantidad'];
            $nom_product = $_POST['nom_product'];

            $dataInsertion = [
                ':id_producto'  => $idProducto,
                ':cantidad'     => $cantidadPro,
                ':user_insert'  => $username,
                ':user_actual'  => $username
            ];

            $datosEncriptar = [
                'id' => $idProducto,
                'nombre' => $nom_product,
                'cantidad' => $cantidadPro,
            ];

            $encriptado = Encryption::encrypt($datosEncriptar);

            $insertProduct = $this->productosModel->insertCantidadProducto($dataInsertion, $encriptado);

            $data['insertion'] = $insertProduct;
        }

        $this->view->assign('data', $data);
        $this->view->render('añadir_cantidad_producto.php', $this->carpeta);
    }

    public function searchUpdateProducto()
    {

        $data = [
            'data' => [],
            'marcas' => [],
        ];

        $this->view->addStyles('styles_search_products.css');
        $this->view->addScripts('search_update_productos.js');

        $marcas = $this->marcasModel->getIdNombre();
        $data['marcas'] = $marcas;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_producto = $_POST['id_producto'];
            $id_marca = $_POST['marca_producto'];

            $productos = new Productos($this->pdo);
            $producto = $productos->searchUpdateProducto($id_producto, $id_marca);

            foreach ($producto as &$produc) {
                $datosEncriptar = [
                    'id_product' => $produc['id_product'],
                    'no_product' => $produc['no_product'],
                    'id_marca' => $produc['id_marca'],
                    'nombre_marca' => $produc['nombre_marca'],
                    'cantidad' => $produc['cantidad'],
                    'cost_produ' => $produc['cost_produ'],
                    'rte_fuente' => $produc['rte_fuente'],
                    'flet_produ' => $produc['flet_produ'],
                    'iva_produc' => $produc['iva_produc'],
                    'pre_finpro' => $produc['pre_finpro'],
                    'uti_produc' => $produc['uti_produc'],
                    'pre_ventap' => $produc['pre_ventap'],
                    'desc_produ' => $produc['desc_produ'],
                    'pre_ventades' => $produc['pre_ventades'],
                    'fec_fin_descu' => $produc['fec_fin_descu'],
                    'ren_product' => $produc['ren_product'],
                    'detalle_product' => $produc['detalle_product'],
                    'fech_actual' => $produc['fech_actual'],
                ];

                $produc['encrypted'] = Encryption::encrypt($datosEncriptar);
            }

            $data['data'] = $producto;
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }

        $this->view->assign('data', $data);
        $this->view->render('update_form_search_productos.php', $this->carpeta);
    }

    public function updateProduct()
    {

        $data = [
            'marcas' => [],
            'decryptedData' => [],
        ];

        // Siempre agregamos el script, para que sea en tiempo real
        $this->view->addScripts('insert_product.js');

        $this->view->addStylesExternos('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
        $this->view->addStylesExternos('https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');


        //librearias externas
        $this->view->addLibraries('https://code.jquery.com/jquery-3.7.1.js');
        $this->view->addLibraries('https://code.jquery.com/ui/1.13.2/jquery-ui.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js');

        $marcas = $this->marcasModel->getIdNombre();
        $data['marcas'] = $marcas;

        if (isset($_GET['data']) && !empty($_GET['data'])) {
            $encryptedData = $_GET['data'];
            $decryptedData = Encryption::decrypt($encryptedData);

            $data['decryptedData'] = $decryptedData;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $idProducto = $_POST['id_producto'];

            $dataInsertion = [
                'id_product' => $_POST['id_producto'],
                'nom_produc' => $_POST['nom_produc'],
                'marca_producto' => $_POST['marca_producto'],
                'cost_produ' => $_POST['cost_produ'],
                'porc_rete' => $_POST['porc_rete'],
                'porc_flete' => $_POST['porc_flete'],
                'porc_iva' => $_POST['porc_iva'],
                'pre_finpro' => $_POST['pre_finpro'],
                'uti_product' => $_POST['uti_product'],
                'pre_ventap' => $_POST['pre_ventap'],
                'des_product' => $_POST['des_product'],
                'pre_ventades' => $_POST['pre_ventades'],
                'fec_fin_descu' => $_POST['endDate'] ?? null,
                'rentabilidad' => $_POST['rentabilidad'],
                'detalle_produc' => $_POST['detalle_produc'],
                'cantidad' => $_POST['cantidad'],
            ];

            $dataEncrypted = [
                'id_product' => $_POST['id_producto'],
                'nom_produc' => $_POST['nom_produc'],
                'marca_producto' => $_POST['marca_producto'],
            ];

            $encrypted = Encryption::encrypt($dataEncrypted);

            $producto = $this->productosModel->updateProducts($dataInsertion, $encrypted);
        }

        $this->view->assign('data', $data);
        $this->view->render('update_products.php', $this->carpeta);
    }
}
