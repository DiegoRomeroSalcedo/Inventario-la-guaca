<?php

namespace Proyecto\Controller;

use Proyecto\Models\Marcas;
use Proyecto\Utils\Encryption;
use Views\View\View;

class MarcaController
{

    protected $view;
    protected $pdo;

    public function __construct($pdo, View $view)
    {
        $this->pdo = $pdo;
        $this->view = $view;
    }

    public function list()
    {
        // Obtener la lista de productos del modelo
        $marcasModel = new Marcas($this->pdo);
        $marcas = $marcasModel->getAll();
        $carpeta = "marcas";

        //Js propios
        $this->view->addScripts('jsdatatables.js');

        //estilos externos
        $this->view->addStylesExternos('https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css');
        $this->view->addStylesExternos('https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css');

        //librerias externas
        $this->view->addLibraries('https://code.jquery.com/jquery-3.7.1.js');
        $this->view->addLibraries('https://cdn.datatables.net/2.1.3/js/dataTables.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js');
        $this->view->addLibraries('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js');
        $this->view->addLibraries('https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js');

        $this->view->assign('marcas', $marcas); // No fue necesario el dat, ya que solo pase una variable en concreto.
        $this->view->render('marca_list.php', $carpeta);
    }

    public function addMarca()
    {

        $data = [
            'marca' => []
        ];
        $carpeta = "marcas";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombreMarca = $_POST['nombre_marca'];
            $marcasModel = new Marcas($this->pdo);
            $marca = $marcasModel->insertMarca($nombreMarca);

            // Preparamos los datos para la vista
            $data['marca'] = $marca;

            print_r($data);
        }
        $this->view->assign('marca', $data);
        $this->view->render('marca_form.php', $carpeta);
    }

    public function searchUpdate()
    {

        $data = [
            'marcas' => [],
        ];
        $carpeta = "marcas";

        $this->view->addStyles('style_search_data.css');
        $this->view->addScripts('search_update_marcas.js');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_marca = $_POST['id_marca'];
            $marca = new Marcas($this->pdo);
            $searchMarca = $marca->searchMarcaUpdate($id_marca);

            // print_r($searchMarca);

            foreach ($searchMarca as &$marca) {
                $datosEncriptar = [
                    'id_marca' => $marca['id_marca'],
                    'nombre_marca' => $marca['nombre_marca']
                ];
                $marca['encriptado'] = Encryption::encrypt($datosEncriptar);
            }

            //Encriptar los datos devueltos para pasarlo en la URL

            $data['marcas'] = $searchMarca;

            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }

        $this->view->render('update_form_search_marcas.php', $carpeta);
    }

    public function updateMarca()
    {

        $data = [
            'decrypted' => [],
            'marca' => []
        ];
        $carpeta = "marcas";

        if (isset($_GET['data']) && !empty($_GET['data'])) {
            $encryptedData = $_GET['data'];

            $drecryptedData = Encryption::decrypt($encryptedData);

            $data['decrypted'] = $drecryptedData;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idMarca = $_POST['id_marca'];
            $nombre = $_POST['nombre_marca'];

            $datosEncriptar = [
                'id_marca' => $idMarca,
                'nombre' => $nombre
            ];

            $dataEncrypted = Encryption::encrypt($datosEncriptar);

            $marcas = new Marcas($this->pdo);
            $marca = $marcas->updateMarca($idMarca, $nombre, $dataEncrypted);

            $data['marca'] = $marca;
        }

        $this->view->assign('data', $data);
        $this->view->render('update_marcas.php', $carpeta);
    }
}
