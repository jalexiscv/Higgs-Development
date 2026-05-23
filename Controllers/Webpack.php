<?php

/**
 * @param authentication, request, dates, parent, component, view, oid, views, prefix
 */

namespace App\Modules\Development\Controllers;

use App\Controllers\ModuleController;

class Webpack extends ModuleController
{
    public function __construct()
    {
        parent::__construct();
        helper('App\Modules\Development\Helpers\Development');
        $this->prefix = 'development-webpack';
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
        $this->component = $this->views . '\Webpack\Home';
        return (view($this->viewer, $this->get_Array()));
    }

}
