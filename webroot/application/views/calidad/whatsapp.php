<div id="one_col">
    <div class="container-fluid main">
        <!-- FILTROS -->
        <div class="row justify-content-between">
            <div class="col-auto">
                <h1>Evaluación Whatsapp</h1>
            </div>
        </div>
        <div class="row form-inline">
            <div class="form-group input-daterange">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="text" id="min" name="min" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                    <div class="input-group-prepend">
                        <span class="input-group-text">A</span>
                    </div>
                    <input type="text" id="max" name="max" value="<?php echo date($agente["FormatoFechaInput"]); ?>" class="form-control datepicker">
                </div>
            </div> &nbsp;
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Campaña</span>
                </div>
                <select id="id_campaign" class="form-control" name="id_campaign" onchange="cw.filterChange('id_campaign')">
                    <?php if( count($campanas) > 1): ?>
                        <option value="" selected="">-- Elige --</option>
                    <?php endif ?>
                    <?= options_select_campaign($campanas) ?>
                </select>
            </div>&nbsp;
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Cuenta</span>
                </div>
                <select id="id_wc" class="form-control" name="id_wc" onchange="cw.filterChange('id_wc')">
                    <option value="" selected="">Todas ...</option>
                </select>
            </div>&nbsp;
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Agente</span>
                </div>
                <select id="id_agente" class="form-control" name="id_agente" onchange="cw.filterChange('')">
                    <option value="" selected="">Todas ...</option>
                </select>
            </div>
        </div>
        <!-- FILTROS -->
        <!-- CUENTAS -->
        <div id="whatsapptab">
            
            <div class="tab-content" id="wa-pills-tabContent">
                <hr>
                <div class="row" id="wasaaa">
                    <!-- CONTACTOS -->
                    <div class="col-lg-3 order-1">
                        <div id="number_account" class="btn btn-info "></div>
                        <br />
                        <h5>
                            Contactos
                        </h5>
                        <div class="pastilla">
                            <div class="input-group">
                                <input type="text" class="form-control wabusc" id="wabusc" onkeypress="cw.handleWabusc(event)" />
                                <div class="input-group-append">
                                    <button class="btn btn-info" onclick="cw.filterChange('')">Buscar</button>
                                </div>
                            </div>
                            <br>
                            <div id="wacontactos" class="listascroll">
                                <p class="wacontact" data-contacto="0">
                                    <a class="waactivate" data-id="$cont->id" data-sid="0" data-wac="$cont->account" href="#">Cargando...</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- CONTACTOS -->
                    <!-- CONVERSACION -->
                    <div class="col-lg-6 order-2">
                        <div class="row justify-content-between" style="margin-bottom:8px;">
                            <div class="col-8"><i class="fas fa-user-circle"></i> <strong id="wacontactname">Nombre de usuario</strong></div>
                            <div class="col-1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 39 39"><path fill="#00E676" d="M10.7 32.8l.6.3c2.5 1.5 5.3 2.2 8.1 2.2 8.8 0 16-7.2 16-16 0-4.2-1.7-8.3-4.7-11.3s-7-4.7-11.3-4.7c-8.8 0-16 7.2-15.9 16.1 0 3 .9 5.9 2.4 8.4l.4.6-1.6 5.9 6-1.5z"></path><path fill="#FFF" d="M32.4 6.4C29 2.9 24.3 1 19.5 1 9.3 1 1.1 9.3 1.2 19.4c0 3.2.9 6.3 2.4 9.1L1 38l9.7-2.5c2.7 1.5 5.7 2.2 8.7 2.2 10.1 0 18.3-8.3 18.3-18.4 0-4.9-1.9-9.5-5.3-12.9zM19.5 34.6c-2.7 0-5.4-.7-7.7-2.1l-.6-.3-5.8 1.5L6.9 28l-.4-.6c-4.4-7.1-2.3-16.5 4.9-20.9s16.5-2.3 20.9 4.9 2.3 16.5-4.9 20.9c-2.3 1.5-5.1 2.3-7.9 2.3zm8.8-11.1l-1.1-.5s-1.6-.7-2.6-1.2c-.1 0-.2-.1-.3-.1-.3 0-.5.1-.7.2 0 0-.1.1-1.5 1.7-.1.2-.3.3-.5.3h-.1c-.1 0-.3-.1-.4-.2l-.5-.2c-1.1-.5-2.1-1.1-2.9-1.9-.2-.2-.5-.4-.7-.6-.7-.7-1.4-1.5-1.9-2.4l-.1-.2c-.1-.1-.1-.2-.2-.4 0-.2 0-.4.1-.5 0 0 .4-.5.7-.8.2-.2.3-.5.5-.7.2-.3.3-.7.2-1-.1-.5-1.3-3.2-1.6-3.8-.2-.3-.4-.4-.7-.5h-1.1c-.2 0-.4.1-.6.1l-.1.1c-.2.1-.4.3-.6.4-.2.2-.3.4-.5.6-.7.9-1.1 2-1.1 3.1 0 .8.2 1.6.5 2.3l.1.3c.9 1.9 2.1 3.6 3.7 5.1l.4.4c.3.3.6.5.8.8 2.1 1.8 4.5 3.1 7.2 3.8.3.1.7.1 1 .2h1c.5 0 1.1-.2 1.5-.4.3-.2.5-.2.7-.4l.2-.2c.2-.2.4-.3.6-.5s.4-.4.5-.6c.2-.4.3-.9.4-1.4v-.7s-.1-.1-.3-.2z"></path></svg></div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" class="wa-cargar-mas" onclick="cw.load_more()"><span>Cargar anteriores</span></a>
                                <div class="pastilla wamsgs" id="wamsgs"></div>
                            </div>
                        </div>
                    </div>
                    <!-- CONVERSACION -->
                    <!-- MENSAJES -->
                    <div id="card_eval_msg" class="col-12 col-lg-3 order-3">
                        <h5 class="modal-title" id="agenda-modalLabel">Evaluación cualitativa</h5>
                        <form class="pastilla" id="form_eqm">
                            <div class="row">
                                <div class="col-12">
                                    <div id="temp_msg" class="wamsg wamsgout"></div>
                                </div>
                                <div class="col-12">
                                        <input type="hidden" name="id" class="form-control">
                                        <input type="hidden" name="id_whatsapp_entry" class="form-control">
                                    <div class="form-group">
                                        <label for="rating">Calificación</label><br>
                                        <div class="rating-component">
                                            <input type="radio" id="star5" name="rating" value="5" />
                                            <label class = "fas fa-star" for="star5" title="5"></label>
                                                
                                            <input type="radio" id="star4" name="rating" value="4" />
                                            <label class = "fas fa-star" for="star4" title="4"></label>
                                            
                                            <input type="radio" id="star3" name="rating" value="3" />
                                            <label class = "fas fa-star" for="star3" title="3"></label>
                                            
                                            <input type="radio" id="star2" name="rating" value="2" />
                                            <label class = "fas fa-star" for="star2" title="2"></label>
                                            
                                            <input type="radio" id="star1" name="rating" value="1" />
                                            <label class = "fas fa-star" for="star1" title="1"></label>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="comment">Comentario</label>
                                        <textarea class="form-control" name="comment" id="comment"></textarea>
                                    </div>
                                </div>
                                <div class="col-12 text-right">
                                    <button type="button" class="btn btn-secondary" onclick="cw.resetFormEQM()">Cancelar</button>
                                    <button type="button" class="btn btn-primary" onclick="cw.saveECM()">Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- MENSAJES -->
                </div>
                <hr>
            </div>
        </div>
        <!-- CUENTAS -->
    </div>
</div>
<!-- Modal preguntas calidad -->
<div class="modal fade" id="waCalidadModal" tabindex="-1" role="dialog" aria-labelledby="waCalidadModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="waCalidadModalTitle">Evaluación cualitativa: <span id="cedula_name" class="text-info"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eval_form">
                <input type="hidden" name="id_eval" id="eval_id">
                <div class="modal-body">
                    <table class="table table-sm table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Atributo</th>
                                <th>Ponderación</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody id="calidadbody"></tbody>
                    </table>
                    <h5><span class="badge">Total de evaluación </span><span class="badge badge-dark" id="evaltotal">0</span></h5><br 7>
                    <h6><span>La ponderación es el valor que fue asignado a cada pregunta.</span></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="cw.saveECS()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>