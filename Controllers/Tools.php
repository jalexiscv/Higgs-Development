<?php

/*
 * Copyright (c) 2021-2023. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Modules\Development\Controllers;

use App\Controllers\ModuleController;

class Tools extends ModuleController
{
    public function __construct()
    {
        parent::__construct();
        helper('App\Modules\Development\Helpers\Development');
        $this->prefix = 'development-tools';
        $this->module = 'App\Modules\Development';
        $this->views = $this->module . '\Views';
        $this->viewer = $this->views . '\index';
    }

    public function index()
    {
        $url = base_url('development/tools/home/' . lpk());
        return (redirect()->to($url));
    }

    public function home(string $rnd)
    {
        $this->oid = $rnd;
        $this->prefix = "{$this->prefix}-home";
        $this->component = $this->views . '\Tools\Home';
        return (view($this->viewer, $this->get_Array()));
    }

    public function view(string $oid, string $rnd)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-view";
        $this->component = $this->views . '\Clients\View';
        return (view($this->viewer, $this->get_Array()));
    }

    public function modules(string $option, string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-modules-{$option}";
        $this->component = $this->views . '\Tools\Modules\Generator';
        return (view($this->viewer, $this->get_Array()));
    }

    public function texttophp(string $option, string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-texttophp-{$option}";
        $this->component = $this->views . '\Tools\Texttophp\Generator';
        return (view($this->viewer, $this->get_Array()));
    }

    public function poeditor(string $option, string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-poeditor-{$option}";
        $this->component = $this->views . '\Tools\Poeditor\Generator';
        return (view($this->viewer, $this->get_Array()));
    }

}
