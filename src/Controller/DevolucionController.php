<?php

namespace Proyecto\Controller;

use Proyecto\Models\Devolucion;
use Proyecto\Models\Facturas;
use Proyecto\Models\Productos;
use Proyecto\Models\Clientes;
use Views\View\View;

class DevolucionController {

    protected $pdo;
    protected $view;
    protected $facturas;
    public $carpeta = "devoluciones";
    //Esta es la cantidad para cada producto
    public $cantidadFacturada = [];
    public $totalFacturado = 0;
    public $dataCliente = '';

    public function __construct($pdo, View $view, Facturas $facturasModel) {
        $this->pdo = $pdo;
        $this->view = $view;
        $this->facturas = $facturasModel;
    }

    public function searchFacturaDevolucion() {

        $this->view->addScripts('add_devolucion.js');

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_factura = $_POST['id_factura'];

            $detalleFactura = $this->facturas->getDetallesFactura($id_factura);

            foreach($detalleFactura as $detalle) {
                $this->cantidadFacturada[$detalle['id_producto']] = $detalle['cantidad'];
                $this->totalFacturado = $detalle['total_venta'];
                $this->dataCliente = $detalle['identificacion'];
            }

            // Guardar en la sesión
            //Php no guarda el estado de las variables entre distintas peticiones, por eso las guardo en sesiones.
            $_SESSION['cantidadFacturada'] = $this->cantidadFacturada;
            $_SESSION['totalFacturado'] = $this->totalFacturado;
            $_SESSION['dataCliente'] = $this->dataCliente;

            header('Content-Type: application/json');
            echo json_encode($detalleFactura);
            exit();
        }

        $this->view->render('insertar_devolucion.php', $this->carpeta);
    }

    public function addDevolucion() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = file_get_contents('php://input');
            $dataArray = json_decode($data, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response = ['success' => false, 'message' => 'Error en JSON: ' . json_last_error_msg()];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
    
            if (!isset($dataArray['productos']) || !is_array($dataArray['productos'])) {
                $response = ['success' => false, 'message' => 'El índice de productos no es un array'];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
    
            $idfacturaValida = (int) $dataArray['idFactura'];
    
            $dataDevo = [
                'id_factura' => $idfacturaValida,
                'motivo' => (string) $dataArray['motivo'],
                'total' => (float) $dataArray['total'],
            ];

    
            $devolucion = new Devolucion($this->pdo);
            $existFactura = $devolucion->existfactur($idfacturaValida);
    
            if ($existFactura) {
                $id_devolucion = $existFactura[0]['id_devolucion'] ?? null;

                //Validamos la cantidad del producto
                foreach($dataArray['productos'] as $producto) {

                    $idProducto = $producto['id_producto'];
                    $cantidadDevuelta = $producto['cantidad'];
                    $totalDevuelto = $dataArray['total'];

                    //Obtenemos las cantidades facturadas

                    $this->cantidadFacturada = $_SESSION['cantidadFacturada'] ?? [];
                    $this->totalFacturado = $_SESSION['totalFacturado'] ?? 0;
                    
                    $cantidadFacturada = $this->cantidadFacturada[$idProducto] ?? 0;
                    $totalFacturado = $this->totalFacturado ?? 0;


                    $cantidadDevueltaAnteriormente  = $devolucion->validateCantidadProductos($id_devolucion, $idProducto);

                    //Validacion principal: Verificar que la cantidad devuelta no exceda la cantidad facturada para el producto
                    if(($cantidadDevueltaAnteriormente + $cantidadDevuelta) > $cantidadFacturada) {
                        $response = ['success' => false, 'message' => "La cantidad devuelta para el producto ID $idProducto supera la cantidad facturada."];
                        header('Content-Type: application/json');
                        echo json_encode($response);
                        exit();
                    }
                }
                //Hcemos la validacion de las cantidades
                if (!$id_devolucion) {
                    $response = ['success' => false, 'message' => 'Error: No se pudo obtener el ID de la devolución existente'];
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit();
                }
    
                $updateDevolucion = $devolucion->updateDevolucion($dataDevo, $id_devolucion);
            } else {
                $id_devolucion = $devolucion->addDevolucion($dataDevo);
                $cliente = new Clientes($this->pdo);
                $this->dataCliente = $_SESSION['dataCliente'] ?? '';
                $clienteDevolucion = $cliente->updateDevoluciones($this->dataCliente);
            }
    
            foreach ($dataArray['productos'] as $producto) {
                if (is_array($producto) && isset($producto['id_producto'])) {
                    // Aquí podrías añadir la validación de la cantidad a devolver vs cantidad vendida
                    if ($existFactura) {
                        $updateDetalleDevolucion = $devolucion->updateDetalleDevolucion($id_devolucion, $producto['id_producto'], $producto['cantidad']);
                    } else {
                        $resultDetalle = $devolucion->addDetalleDevolucion($id_devolucion, $producto);
                    }
    
                    if ($producto['reinsercion'] == 1) {
                        $productosModel = new Productos($this->pdo);
                        $updatecantidad = $productosModel->updateCantidadDevolucion($producto['cantidad'], $producto['id_producto']);
                    }
                    $response = ['success' => true];
                } else {
                    $response = ['success' => false, 'message' => 'Error al insertar detalle o cantidad'];
                    break;
                }
            }
    
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }
    

    public function listDevoluciones() {
        $data = [
            'data' => [],
        ];

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


        $devolucion = new Devolucion($this->pdo);
        $devoluciones = $devolucion->getDataDevolucion();
        
        $data['data'] = $devoluciones;

        $this->view->assign('data', $data);
        $this->view->render('list_devoluciones_data.php', $this->carpeta);
    }
}