<?php

/**
 * @param authentication, request, dates, parent, component, view, oid, views, prefix
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
