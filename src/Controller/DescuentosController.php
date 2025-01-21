<?php

namespace Proyecto\Controller;

use Views\View\View;
use Proyecto\Models\Descuentos;

class DescuentosController
{

    protected $pdo;
    protected $view;
    public $carpeta = "productos";

    public function __construct($pdo, View $view)
    {
        $this->pdo = $pdo;
        $this->view = $view;
    }

    public function listDescuentosVencidos()
    {
        $data = [
            'descuentosVencidos' => []
        ];

        //scripts propios
        $this->view->addScripts('jsdatatables.js');
        $this->view->addScripts('quitar_descuento_productos.js');

        //estilos externos
        $this->view->addStylesExternos('https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css');
        $this->view->addStylesExternos('https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css');

        $this->view->addLibraries('https://code.jquery.com/jquery-3.7.1.js');
        $this->view->addLibraries('https://cdn.datatables.net/2.1.3/js/dataTables.js');

        $descuento = new Descuentos($this->pdo);
        $descuentos = $descuento->validateUpdateDescuentos();

        $data['descuentosVencidos'] = $descuentos;

        $this->view->assign('data', $data);
        $this->view->render('actualizar_descuento_productos.php', $this->carpeta);
    }

    public function quitarDescuento()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Obtenemos los datos enviados

            $data = json_decode(file_get_contents('php://input'), true);

            if ($data) {

                $descuento = new Descuentos($this->pdo);
                $descuentos = $descuento->updateDescuentosProductos($data);
                $response = ['success' => true];
            } else {
                $response = ['success' => false, 'message' => 'Datos no validos'];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }
}
