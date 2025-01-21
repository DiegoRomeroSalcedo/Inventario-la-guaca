<?php

ob_start();

$title = "Dasboard";

?>

<div id="chart-container">
    <canvas id="salesChart">
        <!-- Resultados del grafico -->
    </canvas>
</div>

<?php

//inclusion de scripts
$scriptsHtml = '';

foreach ($this->getScripts() as $script) {
    $scriptsHtml .= '<script src="'.$script. '"></script>';
}

//inclusion de librerias
$librariesHtml = '';

foreach($this->getLibraries() as $library) {
    $librariesHtml  .= '<script src="' .$library.'"></script>';
}

$content = ob_get_clean();

include __DIR__ . '/../layouts/layout.php';

