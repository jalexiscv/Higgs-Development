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
