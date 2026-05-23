<?php

/**
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ ░FRAMEWORK                                  2024-03-14 15:43:03
 * █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Sie\Views\Modules\Editor\form.php]
 * █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 * █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 * █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 * █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 * █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 * █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 * █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 * █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 * █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 * █ @link https://www.codehiggs.com
 * █ @Version 1.5.0 @since PHP 7, PHP 8
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ Datos recibidos desde el controlador - @ModuleController
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ @var object $parent Trasferido desde el controlador
 * █ @var object $authentication Trasferido desde el controlador
 * █ @var object $request Trasferido desde el controlador
 * █ @var object $dates Trasferido desde el controlador
 * █ @var string $component Trasferido desde el controlador
 * █ @var string $view Trasferido desde el controlador
 * █ @var string $oid Trasferido desde el controlador
 * █ @var string $views Trasferido desde el controlador
 * █ @var string $prefix Trasferido desde el controlador
 * █ @var array $data Trasferido desde el controlador
 * █ @var object $model Modelo de datos utilizado en la vista y trasferido desde el index
 * █ ---------------------------------------------------------------------------------------------------------------------
 **/
//[Services]-----------------------------------------------------------------------------
$request = service('Request');
$bootstrap = service('Bootstrap');
$dates = service('Dates');
$strings = service('strings');
$authentication = service('authentication');
$back = '/development/ui/home/' . lpk();
//[Request]-----------------------------------------------------------------------------
$code = 'En Higgs, los botones son elementos interactivos que se usan para iniciar acciones específicas. Por defecto ofrecemos estilos predefinidos para los botones, que se pueden personalizar fácilmente con clases adicionales.';
//[build]---------------------------------------------------------------------------------------------------------------
$bootstrap = service('bootstrap');
$card = $bootstrap->get_Card('card-view-service', [
    'title' => lang('App.Buttons'),
    'header-back' => $back,
    'content' => $code,
]);
echo($card);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h2 class="card-title">
                    Bótones y sus tamaños
                </h2>
            </div>
            <div class="card-body pad table-responsive">
                <p>Various types of buttons. Using the base class <code>.btn</code></p>
                <table class="table table-bordered text-center">
                    <tbody>
                    <tr>
                        <th>Normal</th>
                        <th>Large <code>.btn-lg</code></th>
                        <th>Small <code>.btn-sm</code></th>
                        <th>Extra Small <code>.btn-xs</code></th>
                        <th>Flat <code>.btn-flat</code></th>
                        <th>Disabled <code>.disabled</code></th>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-primary">Primary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-lg">Primary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm">Primary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-xs">Primary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-flat">Primary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary disabled">Primary</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-secondary">Secondary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-lg">Secondary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm">Secondary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-xs">Secondary</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-flat">Secondary
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary disabled">Secondary
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-success">Success</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-lg">Success</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm">Success</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-xs">Success</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-flat">Success</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success disabled">Success</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-info">Info</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-lg">Info</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm">Info</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-xs">Info</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-flat">Info</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info disabled">Info</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-danger">Danger</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-lg">Danger</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm">Danger</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-xs">Danger</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-flat">Danger</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger disabled">Danger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-warning">Warning</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-lg">Warning</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm">Warning</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-xs">Warning</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-flat">Warning</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning disabled">Warning</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!-- /.col -->
</div>
