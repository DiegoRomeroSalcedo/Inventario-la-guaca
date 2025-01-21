<?php

namespace Proyecto\Controller;

use Exception;
use Proyecto\Models\Marcas;
use Proyecto\Models\Productos;
use Views\View\View;
use Proyecto\Models\Ventas;
use Proyecto\Models\Clientes;

class VentasController {

    protected $pdo;
    protected $view;
    protected $productos;
    protected $marcas;
    public $carpeta = 'ventas';

    public function __construct($pdo, View $view, Productos $productosModel, Marcas $marcasModel) {
        $this->pdo = $pdo;
        $this->view = $view;
        $this->productos = $productosModel;
        $this->marcas = $marcasModel;
    }

    public function listVentas() {
        $data = [
            'ventas' => [],
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

        $venta = new Ventas($this->pdo);
        $ventas = $venta->getDataVentas();

        $data['ventas'] = $ventas;

        $this->view->assign('data', $data);
        $this->view->render('list_data_ventas.php', $this->carpeta);
    }

    public function searchAddVenta() {

        $data = [
            'dataVenta' => [],
        ];

        $this->view->addScripts('add_venta.js');

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idProducto = $_POST['id_producto'];
            $searchPro = $this->productos->serchVentaProducto($idProducto);
            $data['dataVenta'] = $searchPro;

            header('Content-Type: application/json');
            echo json_encode($data['dataVenta']);
            exit();
        }

        $this->view->assign('data', $data);
        $this->view->render('search_add_venta.php', $this->carpeta);
    }

    public function finalizarVenta() {

        $data = [
            'id_cliente' => [],
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $carrito = $data['carrito'] ?? [];
            $total = (float) $data["total"];
            $cliente_id = $data['cliente_id'] ?? 0; //quitar el 1 esto por ahora es de prueba
            $clienteData = $data['clienteData'] ?? null;
            $totalRecibido = (float) $data['totalesRecibidoDevuelto']['valorRecibido'];
            $valorDevuelto = (float) $data['totalesRecibidoDevuelto']['valorDevuelto'];
            $tipoPago = (string) $data['tipoPagoData']['tipoDePago'];

            if($clienteData) {
                $cedulaCliente = (string) $data['clienteData']['cedulaCliente'];
                $nroCelular = (string) $data['clienteData']['nroCelular'];
                $nomCliente = $data['clienteData']['nomCliente'];
                $emailCliente = $data['clienteData']['emailCliente'];
                $dirCliente = $data['clienteData']['dirCliente'];
            } else {
                $cedulaCliente = '';
                $nroCelular = '';
                $nomCliente = '';
                $emailCliente = '';
                $dirCliente = '';
            }

            try{

                $clienteModel = new Clientes($this->pdo);

                if($clienteData) {

                    $existCliente = $clienteModel->getClienteCedula($cedulaCliente);

                    if($existCliente && is_array($existCliente)) {
                        $compra = 1;
                        $compCliente = $existCliente['cantidad_compras'];
                        $nueCompra = $compra + $compCliente;
                        $updateCompra = $clienteModel->updateCompras($nueCompra, $cedulaCliente);
                        $data['id_cliente'] = $existCliente['id_cliente'];
                    } else {
                        $insertCliente = $clienteModel->insertCliente($cedulaCliente, $nomCliente, $nroCelular, $emailCliente, $dirCliente);
                        $data['id_cliente'] = $insertCliente;
                    }
                } else {
                    $data['id_cliente'] = null;
                } 
                
                $ventasModel = new Ventas($this->pdo);
                $idFactura = $ventasModel->addFactura($total, $data['id_cliente'], $totalRecibido, $valorDevuelto, $tipoPago); //Recuperamos el id de la factura
                
                foreach($carrito as $producto) {
                    $preUnitario = (float) str_replace(',', '', $producto['pre_ventades']);
                    $ventasModel->addVenta(
                        $idFactura,
                        $producto['id_product'],
                        $producto['cantidad'],
                        $preUnitario,
                        $producto['totalIndCarrito'],
                        $producto['desc_produ'],
                    );//Metodo para insertar la venta de cada producto

                    $cantidadActual = $this->productos->getCantidadStock($producto['id_product'],);
                    $cantidadStock = $cantidadActual['cantidad'];
                    $cantidad = $producto['cantidad'];
                    $totalStock = $cantidadStock - $cantidad;
                    $updateCantidad = $this->productos->updateCantidadVenta($totalStock, $producto['id_product'],);
                }

                if($updateCantidad) {
                    echo json_encode(['succes' => true, 'invoiceId' => $idFactura]);
                }
            }catch(Exception $e) {
                echo json_encode(['Error' => $e->getMessage()]);
            }
            exit();
        }
    }

    public function renderDasboard() {

        $this->view->addScripts('dashboard_ventas.js');

        $this->view->addLibraries('https://cdn.jsdelivr.net/npm/chart.js');

        $this->view->render('dashboard_ventas.php', $this->carpeta);
    }

    public function getdataDashboard() {
        date_default_timezone_set('America/Bogota');

        $year = date('Y');
        $start_date = "$year-01-01 00:00:00";
        $end_date = "$year-12-31 23:59:59";

        $venta = new Ventas($this->pdo);
        $ventas = $venta->getDataGrafic($year, $start_date, $end_date);

         //Creamos el array para todos los mese del año
        $meses = [];

        for($m = 1; $m <= 12; $m++) {
            $mes = sprintf('%04d-%02d', $year, $m);
            $meses[$mes] = 0; //Inicializamos los mese con 0 ventas
        }

        //Asignar los datos obntenidos a los mese correspondientes

        foreach($ventas as $data) {
            $meses[$data['mes']] = $data['total'];
        }

         //Asignamos los datos en formato adecuado.

        $results = [];

        foreach($meses as $mes=>$total) {
            $results[] = [
                'mes' => $mes,
                'total' => $total
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($results);
        exit();
    }
}

?>