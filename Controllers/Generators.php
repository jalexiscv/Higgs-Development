<?php

/*
 * Copyright (c) 2021-2021. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Modules\Development\Controllers;

use App\Controllers\ModuleController;

class Generators extends ModuleController
{
    public function __construct()
    {
        parent::__construct();
        helper('App\Modules\Development\Helpers\Development');
        $this->prefix = 'development-generators';
        $this->module = 'App\Modules\Development';
        $this->views = $this->module . '\Views';
        $this->viewer = $this->views . '\index';
    }

    public function index()
    {
        $url = base_url('development/generators/home/' . lpk());
        return (redirect()->to($url));
    }

    public function home(string $rnd)
    {
        $url = base_url('development/generators/list/' . lpk());
        return (redirect()->to($url));
        //$this->oid = null;
        //$this->prefix = "{$this->prefix}-home";
        //$this->component = $this->views . '\Generators\Home';
        //return (view($this->viewer, $this->get_Array()));
    }

    public function list(string $rnd)
    {
        $this->oid = null;
        $this->prefix = "{$this->prefix}-list";
        $this->component = $this->views . '\Generators\List';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un modelo de conexion a una tabla en la base de datos
     * @param string $oid
     * @return string
     */
    public function model(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-model";
        $this->component = $this->views . '\Generators\Model';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un controlador
     * @param string $oid
     * @return string
     */
    public function controller(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-controller";
        $this->component = $this->views . '\Generators\Controller';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un lister
     * @param string $oid
     * @return string
     */
    public function lister(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-lister";
        $this->component = $this->views . '\Generators\Lister';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un creator
     * @param string $oid
     * @return string
     */
    public function creator(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-creator";
        $this->component = $this->views . '\Generators\Creator';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un viewer
     * @param string $oid
     * @return string
     */
    public function viewer(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-viewer";
        $this->component = $this->views . '\Generators\Viewer';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un editor
     * @param string $oid
     * @return string
     */
    public function editor(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-editor";
        $this->component = $this->views . '\Generators\Editor';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un Deleter
     * @param string $oid
     * @return string
     */
    public function deleter(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-deleter";
        $this->component = $this->views . '\Generators\Deleter';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un Deleter
     * @param string $oid
     * @return string
     */
    public function lang(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-lang";
        $this->component = $this->views . '\Generators\Lang';
        return (view($this->viewer, $this->get_Array()));
    }

    /**
     * Permite generar un migrador
     * @param string $oid
     * @return string
     */
    public function migration(string $oid)
    {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-migration";
        $this->component = $this->views . '\Generators\Migration';
        return (view($this->viewer, $this->get_Array()));
    }

}
