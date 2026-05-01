<div class="container main">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-left">Configuración de Asterisk*</h1>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 col-md-3">
            <label for="config">Archivos</label>
            <select name="config" id="config" class="form-control">
                <option value="">-- Seleccione --</option>
                <option value="sip.conf">SIP</option>
                <option value="pjsip.conf">PJSIP</option>
                <option value="extensions.conf">EXTENSIONES</option>
                <option value="queues.conf">COLAS</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label for="import">Importaciones</label>
            <select name="import" id="import" class="form-control" disabled>
                <option value="">-- Seleccione --</option>
            </select>
        </div>
        <div class="col-12 col-md-6">
            <div class="row">
                <div class="col-12 col-sm-12">
                    <label class="col-12">&nbsp;</label>
                    <button id="btn-guardar" class="btn btn-primary">
                        Guardar
                    </button>
                    <button id="btn-reload" class="btn btn-success">
                        Cargar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <label id="label-texto" for="texto">&nbsp;</label>
            <textarea name="texto" id="texto" class="form-control" rows="20"></textarea>
        </div>
    </div>
</div>
