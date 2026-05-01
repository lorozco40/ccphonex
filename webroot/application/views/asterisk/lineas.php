<div class="container main">
    <div class="row mb-4">
        <div class="col-12 col-sm-4">
            <a href="/asterisk" id="btn_wac_nueva" class="btn btn-success">
                <i class="fas fa-long-arrow-alt-left"></i>
                Regresar
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-left">Líneas</h1>
            <div class="alert alert-secondary" role="alert">
                Esta página se utiliza para administrar varios troncales de sistema
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 col-sm-6">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-plus"></i>
                    Añadir Linea
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#"><i class="fas fa-plus"></i>Añadir Pjsip</a>
                    <a class="dropdown-item" href="#"><i class="fas fa-plus"></i>Añadir Sip</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <form class="form" method="post" id="busform">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Mi buena campaña"/>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-info">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-4">
            <a href="/asterisk/lineas" id="btn_wac_nueva" class="btn btn-success btn-block">Lineas (Troncales)</a>
        </div>
    </div>
</div>
