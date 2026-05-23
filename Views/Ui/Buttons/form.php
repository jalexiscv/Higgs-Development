<?php

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
