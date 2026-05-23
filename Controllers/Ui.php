<?php

/*
 * **
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ ░FRAMEWORK                                  2023-12-01 23:19:27
 *  ** █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Account\Views\Processes\Creator\deny.php]
 *  ** █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 *  ** █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 *  ** █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 *  ** █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 *  ** █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 *  ** █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 *  ** █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 *  ** █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 *  ** █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 *  ** █ @link https://www.codehiggs.com
 *  ** █ @Version 1.5.0 @since PHP 7, PHP 8
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ Datos recibidos desde el controlador - @ModuleController
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @authentication, @request, @dates, @parent, @component, @view, @oid, @views, @prefix
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  **
 */

namespace App\Modules\Development\Controllers;

use App\Controllers\ModuleController;

class UI extends ModuleController
{
    public function __construct()
    {
        parent::__construct();
        helper('App\Modules\Development\Helpers\Development');
        $this->prefix = 'development-ui';
        $this->module = 'App\Modules\Development';
        $this->views = $this->module . '\Views';
        $this->viewer = $this->views . '\index';
    }

    public function index()
    {
        $url = base_url('development/ui/home/' . lpk());
        return (redirect()->to($url));
    }

    public function home(string $rnd)
    {
        $this->oid = $rnd;
        $this->prefix = "{$this->prefix}-home";
        $this->component = $this->views . '\Ui\Home';
        return (view($this->viewer, $this->get_Array()));
    }

    public function buttons(string $oid, string $rnd)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-buttons";
        $this->component = $this->views . '\Ui\Buttons';
        return (view($this->viewer, $this->get_Array()));
    }

    public function cards(string $oid, string $rnd)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-cards";
        $this->component = $this->views . '\Ui\Cards';
        return (view($this->viewer, $this->get_Array()));
    }

    public function chatbox(string $oid, string $rnd)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-chatbox";
        $this->component = $this->views . '\Ui\Chatbox';
        return (view($this->viewer, $this->get_Array()));
    }

    public function uploaders(string $oid, string $rnd)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-uploaders";
        $this->component = $this->views . '\Ui\Uploaders';
        return (view($this->viewer, $this->get_Array()));
    }

}
