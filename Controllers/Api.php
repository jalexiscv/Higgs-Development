<?php

namespace App\Modules\Development\Controllers;

use Higgs\API\ResponseTrait;
use Higgs\RESTful\ResourceController;

/**
 * Api
 */
class Api extends ResourceController
{
    use ResponseTrait;

    public $namespace;
    protected $prefix;
    protected $module;
    protected $views;
    protected $viewer;
    protected $component;

    public function __construct()
    {
        //header("Content-Type: text/json");
        helper('App\Helpers\Application');
        helper('App\Modules\Web\Helpers\Web');
        $this->prefix = 'web-api';
        $this->module = 'App\Modules\Web';
        $this->views = $this->module . '\Views';
        $this->viewer = $this->views . '\index';
        $this->component = $this->views . '\Api';
    }

    // all users
    public function index()
    {
        $data = array("message" => "Api Online!");
        return $this->respond($data);
    }


    public function customers(string $format, string $option, string $oid)
    {
        //header("Content-Type: text/json");
        header("Content-Type: text/html");
        $data = array(
            "oid" => $oid
        );
        if ($format == "json") {
            if ($option == 'list') {
                echo(view('App\Modules\Web\Views\Customers\List\json', $data));
            }
        } else {
            return ($this->failNotFound(lang("App.Api-breaches-no-option")));
        }
    }

    public function sitemaps(string $oid)
    {
        header('Content-Type: application/xml');
        $data = array(
            "oid" => $oid
        );
        return (view('App\Modules\Web\Views\Sitemaps\index', $data));
    }

    public function bing(string $oid)
    {
        header('Content-Type: text/plain');
        $data = array(
            "oid" => $oid
        );
        return (view('App\Modules\Web\Views\Sitemaps\bing', $data));
    }

    /**
     * @param string $format
     * @param string $rnd
     * @return \Higgs\HTTP\Response
     */
    public function generators(string $format, string $option, string $oid)
    {
        //header("Content-Type: text/json");
        if ($format == "json") {
            if ($option == 'list') {
                return (view('App\Modules\Development\Views\Generators\List\json'));
            } else {

            }
        } else {
            return ($this->failNotFound(lang("App.Api-Authentication-no-option")));
        }
    }


}
