<?php

namespace App\Modules\Development\Controllers;

require_once(APPPATH . 'ThirdParty/Markdown/autoload.php');

use Higgs\API\ResponseTrait;
use Higgs\RESTful\ResourceController;
use Michelf\MarkdownExtra;

/**
 * Api
 */
class AI extends ResourceController
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
    /**
     * https://localhost/development/ai/test/json/1/0
     * https://localhost/development/ai/test/json/1/puedes%20escribir%20un%20programa%20en%20php%20para%20calcular%20las%20horas%20minutos%20y%20segundos%20tracurrido%20entre%20dos%20fechas%20y%20explicame%20el%20codigo
     * @param string $format
     * @param string $option
     * @param string $oid
     * @return mixed
     */
    // all users
    public function test(string $format, string $option, string $oid)
    {
        header('Content-Type: text/html');
        $api_key = 'AIzaSyDjARGry0lzknJqVw7DW-uUbW8a6QHtfC4';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $api_key;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $oid],
                    ],
                ],
            ],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error cURL: ' . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $response = $result['candidates'][0]['content']['parts'][0]['text'];
                $parser = new MarkdownExtra();
                echo('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.1.0/github-markdown.min.css">');
                echo('<div class="markdown-body">');
                echo $parser->transform($response);
                echo('</div>');
            } else {
                echo 'No se pudo obtener una respuesta válida.';
            }
        }

        curl_close($ch);

    }

}
