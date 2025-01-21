<?php

namespace Views\View;

class View
{
    protected $data = [];
    protected $scripts  = [];
    protected $styles = [];
    protected $styleslibraries = [];
    protected $libraries = [];

    public function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function render($template, $carpeta)
    {

        if ($carpeta == "Autenticacion") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "marcas") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/marcas/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "productos") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/productos/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "inventario") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/inventario/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "ventas") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/ventas/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "facturas") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/facturas/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "clientes") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/clientes/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }

        if ($carpeta == "devoluciones") {
            extract($this->data);
            $file =  __DIR__ . '/../src/Views/devoluciones/' . $template;

            if (file_exists($file)) {
                extract($this->data);
                include $file;
            } else {
                echo "Error";
            }
        }
    }

    public function addScripts($script)
    {
        $this->scripts[] = $script;
    }

    public function getScripts()
    {
        $scripts = array_map(function ($script) {
            return BASE_URL_SCRIPTS . $script;
        }, $this->scripts);

        return $scripts;
    }

    public function addStyles($styles)
    {
        $this->styles[] = $styles;
    }

    public function getStyles()
    {
        $styles = array_map(function ($style) {
            return '/css/' . $style;
        }, $this->styles);

        return $styles;
    }

    public function addStylesExternos($style)
    {
        $this->styleslibraries[] = $style;
    }

    public function getStylesLibraries()
    {
        return $this->styleslibraries;
    }

    public function addLibraries($libraries)
    {
        $this->libraries[] = $libraries;
    }

    public function getLibraries()
    {
        return $this->libraries;
    }
}
