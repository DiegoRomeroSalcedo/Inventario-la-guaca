<?php

namespace Proyecto\Controller;

use Exception;
use Views\View\View;
use Proyecto\Models\Facturas;

class FacturasController {

    protected $pdo;
    protected $view;
    public $carpeta = "facturas";

    public function __construct($pdo, View $view) {
        $this->pdo = $pdo;
        $this->view = $view;
    }

    public function listFacturData() {
        $data = [
            'facturas' => [],
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

        $factura = new Facturas($this->pdo);
        $facturas = $factura->getFacturListData();

        $data['facturas'] = $facturas;

        $this->view->assign('data', $data);
        $this->view->render('list_facturas_data.php', $this->carpeta);
    }

    public function getFacturaData() {

        if($_SERVER['REQUEST_METHOD'] == 'GET') {

            $facturaid = $_GET['id'] ?? 0;

            if(empty($facturaid)) {
                echo json_encode(['error' => 'Id de la factura no proporcionado']);
                exit();
            }

            try {
                $ventasModel = new Facturas($this->pdo);
                $detallesFactura = $ventasModel->getDetallesFactura($facturaid);

                if($detallesFactura) {
                    header('Content-Type: application/json');
                    echo json_encode($detallesFactura);
                } else {
                    echo json_encode(['error' => 'Factura no Encontrada']);
                }
            } catch(Exception $e) {
                echo json_encode(['Error' => $e->getMessage()]);
            }

            exit();
        }

    }

    public function renderFactura() {
        $this->view->render('factura.php', $this->carpeta);
    }
}