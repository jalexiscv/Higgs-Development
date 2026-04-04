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
$back = "/development/ui/home/" . lpk();
//[Request]-----------------------------------------------------------------------------
$code = "En Higgs, los botones son elementos interactivos que se usan para iniciar acciones específicas. Por defecto ofrecemos estilos predefinidos para los botones, que se pueden personalizar fácilmente con clases adicionales.";
//[build]---------------------------------------------------------------------------------------------------------------
$bootstrap = service("bootstrap");
$card = $bootstrap->get_Card("card-view-service", array(
    "title" => lang("App.Buttons"),
    "header-back" => $back,
    "content" => $code,
));
echo($card);
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
                        <label for="documentType" class="form-label">Tipo de Documento</label>
                        <div class="input-group">
                            <select class="form-select" id="documentType" name="documentType" required>
                                <option value="" selected disabled>Seleccione el tipo de documento</option>
                                <option value="documento">Documento</option>
                                <option value="acta">Acta</option>
                                <option value="foto">Fotografía</option>
                                <option value="certificado">Certificado</option>
                                <option value="contrato">Contrato</option>
                            </select>
                            <input type="text" class="form-control" id="fileLabel"
                                   placeholder="Ningún archivo seleccionado" readonly>
                            <input type="file" class="d-none" id="fileInput" name="file">
                            <button class="btn btn-primary" type="button"
                                    onclick="document.getElementById('fileInput').click()">
                                Examinar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Progreso -->
<div class="modal fade" id="uploadModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subiendo archivo</h5>
            </div>
            <div class="modal-body">
                <p class="mb-2" id="uploadingFile">Subiendo: </p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%"
                         id="uploadProgress">0%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
