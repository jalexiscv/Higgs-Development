<?php

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
use Higgs\Html\Html;

$continue = "/cadastre";

if ($authentication->get_LoggedIn()) {
    $headerTitle = lang("App.Access-denied-title");
    $bodyText    = lang("App.Access-denied-message");
} else {
    $headerTitle = lang("App.login-required-title");
    $bodyText    = lang("App.login-required-message");
}

// Botones del footer — renderizados a string para pasarlos como contenido de texto
$loginBtn    = (string) BS5::button([
    'content'    => lang("App.Login"),
    'variant'    => 'primary',
    'size'       => 'sm',
    'attributes' => ['href' => '/signin'],
])->render();

$continueBtn = (string) BS5::button([
    'content'    => lang("App.Go-back"),
    'variant'    => 'secondary',
    'size'       => 'sm',
    'attributes' => ['href' => $continue],
])->render();

// headerHtmlTitle acepta HTML confiable; $headerTitle proviene de lang() (traducción de sistema, no input de usuario)
echo BS5::card([
    'headerHtmlTitle'  => '<i class="fa fa-ban me-2"></i>' . $headerTitle,
    'headerClass'      => 'bg-danger text-white',
    'content'          => $bodyText,
    'bodyAttributes'   => ['class' => 'text-center'],
    'footer'           => $loginBtn . ' ' . $continueBtn,
    'footerAttributes' => ['class' => 'text-center'],
    'attributes'       => ['class' => 'border-danger'],
])->render();
