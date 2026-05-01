<div class="row">
    <div class="col">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCalendar"><i class="far fa-calendar-alt"></i> Calendarizar</button>
    </div>
</div>
<div class="row" id="ueventos"></div>
<!--Modal calendarizar-->
<div class="modal fade bd-example-modal-md" id="modalCalendar" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" id="modalancho"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalCenterTitle"><i class="far fa-calendar-alt calen"></i>&nbsp;&nbsp;Calendarizar</h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo site_url('calendario/creacalendar');?>" method="post">
                    <input type='hidden' name='agentes' value='<?php echo $agente['id']; ?>'>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text nombre">Nombre</span>
                        </div>
                        <input type="text" class="form-control" name="name" placeholder="Nombre" required="required">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text apellido">Apellido</span>
                        </div>
                        <input type="text" class="form-control" name="last" placeholder="Apellidos">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text contacto">Tipo</span>
                        </div>
                        <select type="text" class="form-control" name="type">
                            <option value="Llamar">Llamar</option>
                            <option value="SMS">Envia SMS</option>
                            <option value="eMail">eMail</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text fecha">Fecha & Hora</span>
                        </div>
                        <input type="datetime-local" class="form-control" name="scheduled" min="<?php echo date("Y-m-d\TH:i"); ?>"
                        value="<?php echo date("Y-m-d\TH:i"); ?>" required="required">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text obser">Observaciones</span>
                        </div>
                        <textarea type="text" class="form-control" name="observations" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>
