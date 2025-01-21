<?php

namespace Proyecto\Controller;

use Views\View\View;
use Proyecto\Models\Inventario;
use Proyecto\Models\Marcas;
use Proyecto\Models\Productos;

class InventarioController {

    protected $view;
    protected $inventario;
    protected $marcasModel;
    protected $productosModel;
    public $carpeta = 'inventario';

    public function __construct(View $view, Inventario $inventario, Marcas $marcasModel, Productos $productosModel) {
        $this->view = $view;
        $this->inventario = $inventario;
        $this->marcasModel = $marcasModel;
        $this->productosModel = $productosModel;
    }

    public function list() {

        $data = [
            'idYmarca' => [],
            'idYprodu' => [],
            'usr_insact' => [],
            'defaultData' => [],
        ];

        // js propios
        $this->view->addScripts('datapickers.js');
        $this->view->addScripts('jsdatatables.js');
        $this->view->addScripts('search_data_inventario.js');

        //css externos
        $this->view->addStylesExternos('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
        $this->view->addStylesExternos('https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css');
        $this->view->addStylesExternos('https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css');

        //librearias externas
        $this->view->addLibraries('https://code.jquery.com/jquery-3.7.1.js');
        $this->view->addLibraries('https://code.jquery.com/ui/1.13.2/jquery-ui.js');
        $this->view->addLibraries('https://cdn.datatables.net/2.1.3/js/dataTables.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js');

        //Intanciamos la clase de marcas mediante la inyeccion de dependendencias para el selector de marca
        $idYmarca = $this->marcasModel->getIdNombre();
        $idYprodu = $this->productosModel->getIdNombre();
        $usr_insAct = $this->inventario->getUsrs();
        $datadefault = $this->inventario->defaultData();
        $data['idYmarca'] = $idYmarca;
        $data['idYprodu'] = $idYprodu;
        $data['usr_insact'] = $usr_insAct;
        $data['defaultData'] = $datadefault;
        
        $this->view->assign('data', $data);
        $this->view->render('invetario_list.php', $this->carpeta);
    }

    public function searchData() {
        $marcaProd = $_POST['nom_marca'] ?? null;
        $idProduct = $_POST['marca_producto'] ?? null;
        $detalle = $_POST['detalle_pro'] ?? null;
        $usr_insert = $_POST['usr_insercion'] ?? null;
        $usr_actual = $_POST['usr_actualiza'] ?? null;
        $startDate = $_POST['startDate'] ?? null;
        $endDate = $_POST['endDate'] ?? null;

        $dataSearch = [$marcaProd, $idProduct, $detalle, $usr_insert, $usr_actual, $startDate, $endDate];

        //Realizamos la validacion ante la base de datos.
        $resultsInventario = $this->inventario->searchData($dataSearch);

        $response = [
            'array' => $resultsInventario
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}