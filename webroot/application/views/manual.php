<br />
<br />
<div class="container pastilla">
    <?php if(in_array("manual", $agente["permisoSec"])): ?>
        <div class="nonom035">
            <h2>Manual de usuario</h2>
            <div class="row">
                <div class="container fluid">
                    <div class="accordion" id="accorManual">
                        <div class="card">
                            <div class="card-header nopadding" id="heading001">
                                <h3 class="mb-0">
                                    <button class="btn btn-info btn-lg btn-block collapsed" type="button" data-toggle="collapse" data-target="#collapse001" aria-expanded="true" aria-controls="collapse001">
                                        <i class="far fa-hdd"></i>&nbsp;&nbsp;Consola
                                    </button>
                                </h3>
                            </div><!-- cierre menu consola-->
                            <div id="collapse001" class="collapse" aria-labelledby="heading001" data-parent="#accorManual">
                                <div class="accordion" id="accorbarra">
                                    <div class="text-center">
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseHome" aria-expanded="false" aria-controls="collapseHome">
                                            <i class="fa fa-home"></i> Home
                                        </button>
                                        <button class="btn btn-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseChat" aria-expanded="false" aria-controls="collapseChat">
                                            <!-- <i class="fa fa-comments"></i> --> Chat
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseEmail" aria-expanded="false" aria-controls="collapseEmail">
                                            <i class="fa fa-envelope"></i> E-mail
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseSms" aria-expanded="false" aria-controls="collapseSms">
                                            <i class="far fa-comment-dots"></i> SMS
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseVideo" aria-expanded="false" aria-controls="collapseVideo">
                                            <i class="fas fa-video"></i> Video conferencia
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseWhats" aria-expanded="false" aria-controls="collapseWhats">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseCalendario" aria-expanded="false" aria-controls="collapseCalendario">
                                            <i class="far fa-calendar-alt"></i> Calendario
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseExt" aria-expanded="false" aria-controls="collapseExt">
                                            Ext
                                        </button>
                                    </div>
                                    <div class="collapse" id="collapseHome" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Home</h4>
                                            <ul>
                                            <li>Home <i class="fa fa-home"></i> módulo inicial en la pantalla de Assertive, en esta encontraremos secciones para llamadas entrantes o salientes.</li><br />
                                                <ul>
                                                <li style="font-size: 15px;"> Información de llamada</li>
                                                <ul>
                                                    <li> <strong>Nombre</strong> muestra el nombre del contacto.</li>
                                                    <li> <strong>Número</strong> muestra el número marcado o del que recibimos la llamada.</li>
                                                    <li> <strong>Campaña</strong> muestra nombre de la camapaña.</li>
                                                    <li> <strong>Id llamada</strong> identificador asignado a esa llamada.</li>
                                                    <li> <strong>Scrip</strong> mostrara el dialogo que debe decir el agente.</li><br>
                                                    <li> Para ingresar el <strong>script</strong> revisar el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Campañas.</strong></li>
                                                </ul>
                                                <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-17-modal-sm"><i>información de llamada</i></button></dl>
                                                <li style="font-size: 15px;"> Seleccion de campaña</li>
                                                <ul>
                                                    <li> Tiene dos opciones desplegables, uno para ingresar texto y 2 botones.</li>
                                                    <li> <strong>Campaña</strong> muestra la campaña(s) en las que estamos asignados.</li>
                                                    <li> <strong>Formualrio</strong> muestra los formulario activos a la campaña(s), al dar clic podemos elegir si existe mas de 1 formulario.</li>
                                                    <li> <strong>ID (opcional CRM)</strong> cuando usamos un CRM en esta casilla agregamos el no. de ID a buscar (ejemplo <strong style="color: #C62525; text-shadow: 1px 1px 1px #000000;"> 1, 23, 60, 99 </strong> etc + clic <strong style="color: #0d6efd; text-shadow: 1px 1px 1px #000000;"> - Ver - </strong> ).</li>
                                                    <li> Sino es el formulario correcto solo damos clic en el botón  <i class="fas fa-times"></i>  y elegimos el nuevo formulario.</li><br />
                                                    <li> Para crear un <strong>formulario o convertirlo en CRM</strong> revisar el apartado <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;"> Configuración / Formulario / Generación y mantenimientos.</strong></li>
                                                </ul>
                                                <dl>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-18-modal-sm"><i>formulario</i></button>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-n18-modal-sm"><i>crm</i></button>
                                                </dl>
                                                <li style="font-size: 15px;"> Agenda</li>
                                                <li>Esta sección permite crear un contacto, realizar busqueda y tener opciones directas con un contacto.</li><br />
                                                    <ul>
                                                        <li><strong> Crear </strong> </li>
                                                        <ol>
                                                            <li> Damos clic en el icono <strong><i class="fas fa-user-plus"></i></strong>.</li>
                                                            <li> Se mostrara una ventana con los campos a llenar.</li>
                                                            <li> Damos clic en <strong>Guardar</strong> y nos mostrara <strong><i>Registro guardado.</i></strong></li>
                                                            <li> Listo registro agregado.</li>
                                                        </ol>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-n19-modal-sm"><i>llenar campos</i></button></dl>
                                                        <li><strong> Búsqueda </strong> </li>
                                                        <ol>
                                                            <li> Opciones de busqueda <i>por telefóno, nombre, apellido ó email</i>.</li>
                                                            <li> Ingresamos el texto y damos clic en <strong>Buscar</strong> se desplegaran 10 resultados max, <strong><i>para un mejor resultado debemos ser más especificos.</i></strong></li>
                                                        </ol>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-19-modal-sm"><i>búsqueda</i></button></dl>
                                                        <li><strong> Opciones directas </strong> </li>
                                                        <li> Al desplegar el resultado de busqueda seleccionaremos el registro deseado y damos clic en alguna de las opciones.</li>
                                                        <ul>
                                                            <li><i class="far fa-eye"></i> Visualizar.</li>
                                                            <li><i class="far fa-edit"></i> Editar.</li>
                                                            <li><i class="far fa-envelope"></i> Envio de email.</li>
                                                            <li><i class="fas fa-phone"></i> Realizar llamada.</li>
                                                            <li><i class="fas fa-sms"></i> Envío de sms.</li>
                                                        </ul>
                                                    </ul>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-17-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/informacionLlamada.png"); ?>" alt="informacionLlamada" height="210" width="680">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-18-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/form.png"); ?>" alt="form" height="410" width="650">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-n18-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/crm.png"); ?>" alt="crm" height="750" width="700">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-19-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/buscar.png"); ?>" alt="buscar" height="180" width="650">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-n19-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/llenarCampos.png"); ?>" alt="crearAgenda" height="550" width="700">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon home-->
                                    <div class="collapse" id="collapseChat" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <div class="text-center">
                                                en construcción ... <i class="fas fa-cog fa-spin" style="color: #fab005; font-size: 2em;"></i>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon chat-->
                                    <div class="collapse" id="collapseEmail" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">E-mail</h4>
                                            <ul>
                                                <li>E-mail <i class="fa fa-envelope"></i> este modulo permite recibir, enviar y transferir emails, asi como visualizar el historial entrante y saliente.</li>
                                                <li>Puede ser configurado con cualquier proveedor como:
                                                    <i><span class="text-info">G</span>
                                                        <span class="text-danger">o</span>
                                                        <span class="text-warning">o</span>
                                                        <span class="text-info">g</span>
                                                        <span class="text-success">l</span>
                                                        <span class="text-danger">e</span>,
                                                        <span style=" color: #609; text-shadow: 2px 2px 10px #fff;">yahoo</span>,
                                                        <span class="text-info">outlook</span>, etc.
                                                    </i>
                                                </li>
                                                <li> El modulo de email mostrara las opciones de Busqueda, Nuevo, Entrantes, Salientes y Buscado.</li><br>
                                                <li>Para configurar la cuenta(s) de e-mail, revisar el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;"> Configuración / Email. </strong></li><br>
                                                <ul>
                                                    <li style="font-size: 15px;">Recibir</li>
                                                    <ol>
                                                        <li>Cuando llegue un email mostrara una leyenda <strong>Tienes email</strong> y en el icono <i class="fa fa-envelope"></i> nos mostrara <i style="color: #ff0000; font-size: 10px;" class="fas fa-circle"></i> damos clic.</li>
                                                        <li>Seleccionamos la baneja Entrante y elegimos el email que muestra un icono <span class="badge rounder-pill bg-danger"> nuevo </span>.</li>
                                                        <li>Mostrara la leyenda <strong>Email actualizado a leido, empieza la cuenta de tiempo de servicio.</strong> ya en esta opción podemos visualizar, responder o transferir.</li>
                                                        <ul>
                                                            <li> <i class="text-info">Visualizar</i> podemos dar lectura al email que nos enviaron.</li>
                                                        </ul>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-ee21-modal-sm"><i>entrante</i></button>
                                                            <button type="button" class="btn btn-link text-info" data-toggle="modal" data-target=".bd-ev22-modal-sm"><i>visualizar</i></button>
                                                        </dl>
                                                    </ol>
                                                    <li style="font-size: 15px;">Nuevo</li>
                                                    <ol>
                                                        <li>Para enviar un e-mail nuevo damos clic en <i class="far fa-edit"></i></li>
                                                        <li>Llenamos las opciones para, asunto, mensaje y si es necesario adjuntamos algun archivo.</li>
                                                        <li>Damos clic en <strong> Enviar. </strong>.</li>
                                                        <li>Listo email enviado será almacenado en la bandeja <strong>Salientes.</strong></li>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-en23-modal-sm"><i>nuevo</i></button></dl>
                                                    </ol>
                                                    <li style="font-size: 15px;">Reenviar</li>
                                                    <ol>
                                                        <li>Seleccionamos el email a reenviar de la bandeja <strong>Entrantes.</strong></li>
                                                        <li>Damos clic en <i class="fas fa-reply"></i> para reenviar el email.</li>
                                                        <li>Escribimos la respuesta en el cuerpo del mensaje, si es necesario adjuntamos algun archivo.</li>
                                                        <li>Damos clic en <strong>Enviar.</strong></li>
                                                        <li>Listo email enviado, sera almacenado en la bandeja <strong>Salientes.</strong></li>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-er24-modal-sm"><i>responder</i></button></dl>
                                                    </ol>
                                                    <li style="font-size: 15px;">Buscar</li>
                                                    <ol>
                                                        <li>Al lado del icono <i class="fas fa-search"></i> escribimos una direccion de email o asunto y damos <strong> Enter </strong> con el teclado.</li>
                                                        <li>En la baneja de <strong>Buscado</strong> nos mostrara los email entrantes y salientes que coincidan con la referencia que escribimos en buscar.</li>
                                                        <li>Seleccionamos el email para visualizarlo y listo</li>
                                                        <ul>
                                                            <li><i class="far fa-arrow-alt-circle-right" style="color: #ca8f00;"></i> email entrantes.</li>
                                                            <li><i class="far fa-arrow-alt-circle-left" style="color: #725ee6;"></i> email salientes.</li>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-eb25-modal-sm"><i>buscar</i></button></dl>
                                                    </ol>
                                                    <li style="font-size: 15px;">Transferir</li>
                                                    <ol>
                                                        <li><i>Solo podemos tranferir email entrantes</i>, seleccionamos el email.</li>
                                                        <li>Desplegamos <strong> -- Elige -- </strong> y seleccionamos al agente al que vamos a transfer el email.</li>
                                                        <li>Damos clic en <i class="far fa-share-square"></i> y mostrara <strong>Mensaje transferido al agente seleccionado.</strong></li>
                                                        <li>Listo email transferido.</li>
                                                    </ol><br>
                                                </ul>
                                                <li>Para ingresar mas de una direccion de email debemos separlas por comas y sin espacio:<strong>correo@gmail.com,correo2@gmail.com</strong> etc.</li>
                                            </ul>
                                            <div class="modal fade bd-ee21-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailEntrante.png"); ?>" alt="emailEntrante" height="450" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-ev22-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailVisualizar.png"); ?>" alt="emailVisualizar" height="520" width="970">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-en23-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailNuevo.png"); ?>" alt="emailNuevo" height="600" width="700">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-er24-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailResponder.png"); ?>" alt="emailResponder" height="600" width="700">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-eb25-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailBuscar.png"); ?>" alt="emailBuscar" height="400" width="900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon email-->
                                    <div class="collapse" id="collapseSms" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">SMS</h4>
                                            <ul>
                                                <li>Sms es el envio de mensajes de 2 vias.</li>
                                                <li>En esta sección podremos hacer el envio de mensajes, crear plantillas para el mensaje ó editar plantillas existentes.</li>
                                                <li>Para la configuración de SMS revisar el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;"> Configuración / Generales</strong>.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Envio de mensaje</li>
                                                    <ol>
                                                        <li>Ingresa el numero a 10 digitos ejemplo: 5531313131.</li>
                                                        <li>Escribir el contenido del mensaje no mayor a 239 caracteres <i>(se muestra un contador de caracteres).</i></li>
                                                        <li>Damos clic en <strong>Enviar SMS</strong> y nos mostrara <strong><i>Procesando SMS, gracias.</i></strong>.</li>
                                                        <li>SMS enviado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-25-modal-sm"><i>mensaje</i></button></dl>
                                                    <li style="font-size: 15px;">Crear una plantilla</li>
                                                    <ol>
                                                        <li>Escribir el contenido del mensaje</li>
                                                        <li>Damos clic en <strong>Crear plantilla</strong> y nos mostrara <strong><i>Plantilla agregada con exito</i></strong></li>
                                                        <li>Plantilla creada</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-26-modal-sm"><i>plantilla</i></button></dl>
                                                    <li style="font-size: 15px;">Uso de plantilla en SMS nuevo</li>
                                                    <ol>
                                                        <li>Ingresa el numero a 10 digitos ejemplo: 5531313131 .</li>
                                                        <li>De las plantillas existentes solo damos clic en Usar y se agregara el texto a nuestro mensaje nuevo.</li>
                                                        <li>Damos clic en <strong>Enviar SMS</strong> y nos mostrara <strong><i>Procesando SMS, gracias.</i></strong>.</li>
                                                        <li>SMS enviado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-27-modal-sm"><i>plantilla existente</i></button></dl>
                                                    <li style="font-size: 15px;">Actualizar plantilla</li>
                                                    <ol>
                                                        <li>Editamos el texto de la plantilla a cambiar.</li>
                                                        <li>Damos clic en <strong>Actualizar</strong> y nos mostrara<strong><i> Plantilla actualizada con exito</i></strong></li>
                                                        <li>Plantilla actualizada.</li>
                                                    </ol>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-25-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/smsEnvio.png"); ?>" alt="smsEnvio" height="300" width="700">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-26-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/smsPlantilla.png"); ?>" alt="smsPlantilla" height="200" width="700">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-27-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/smsPexistente.png"); ?>" alt="smsPexistente" height="550" width="600">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon sms-->
                                    <div class="collapse" id="collapseVideo" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Video Conferencia</h4>
                                            <ul>
                                                <li>En esta sección podremos realizar video conferencias de manera rapida y sencilla.</li>
                                                <li>Lo unico que necesitamos son las direcciones de email para enviar la invitación</li>
                                                <li>Un ventaja al recibir la invitaciones es que no necesitas esperar a que otorgen permiso para ingresar.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Invitación</li>
                                                    <ol>
                                                        <li>Daremos clic en <i class="fas fa-user-plus fa-flip-horizontal"></i></li>
                                                        <li>Luego clic en <i class="far fa-copy fa-flip-vertical"></i> para copiar el enlace</li>
                                                        <li>Ya copiado podemos enviar el enlace por email, sms, whatsapp, etc</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;">Iniciar</li>
                                                    <ol>
                                                        <li>Ingresa el nombre con el que te identificaran.</li>
                                                        <li>Da clic en el boton <strong style="color: #0d6efd; text-shadow: 1px 1px 1px #000000;"> Entrar a la reunión </strong></li>
                                                        <ul>
                                                            <li>Puede activar o desactivar el microfono <i class="fas fa-microphone-slash"></i></li>
                                                            <li>Puedes activar o desactivar la camara <i class="fas fa-video-slash"></i></li>
                                                        </ul>
                                                        <li>Listo ya nos encontramos dentro.</li><br />
                                                    </ol>
                                                    <li style="font-size: 15px;">Menu</li>
                                                    <ul>
                                                        <li>Dentro de la sala de conferencias tenermos diferentes botones.</li>
                                                        <ul>
                                                            <li><i class="fas fa-microphone-slash"></i> activa o desactiva el microfono.</li>
                                                            <li><i class="fas fa-video-slash"></i> activa o desactiva la camara</li>
                                                            <style>
                                                            .fa-stack { font-size: 0.5em; }
                                                            i { vertical-align: middle; }
                                                            </style>
                                                            <li><span class="fa-stack">
                                                                <i class="fas fa-tv fa-stack-2x"></i>
                                                                <i class="fas fa-share fa-stack-1x"></i>
                                                            </span> compartir pantalla</li>
                                                            <li><i class="far fa-comment-alt fa-flip-horizontal"></i> mostrar u ocultar chat</li>
                                                            <li><i class="far fa-hand-paper"></i> levantar o bajar la mano</li>
                                                            <li><i class="fas fa-user-friends"></i> Participantes</li>
                                                            <li><i class="fas fa-th-large"></i> activar o desactivar la vista</li>
                                                            <li>Colgar</li>
                                                            <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-vc27-modal-sm"><i>panel</i></button></dl>
                                                        </ul>
                                                    </ul>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-vc27-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/vconferenciaPanel.png"); ?>" alt="vconferenciaPanel" height="450" width="1030">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon videoconferencia-->
                                    <div class="collapse" id="collapseWhats" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">WhatsApp</h4>
                                            <ul>
                                                <li>Contamos con la funcionalidad de Multicuentas de WhatsApp.</li>
                                                <li>Por seguridad, politica y buen uso de WhatsApp, el primer mensaje debera ser enviado por el usuario.</li>
                                                <li>Con WhatsApp podemos tener comunicación directa y en tiempo real con los usuarios, para la configuración de WhatsApp revisar el apartado <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;"> Configuración / Whatsapp</strong>.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Asignar WhatsApp a operador</li>
                                                    <ol>
                                                        <li>Damos clic en <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Usuarios</strong></li>
                                                        <li>Selecciona al usuario y da clic en <strong> Permisos</strong>.</li>
                                                        <li>En la sección <strong>Cuentas Whatsapp</strong> haz clic sobre el numero para seleccionarlo y en la seccción <strong>Funcionalidad</strong> clic en la casilla <i class="far fa-square"></i> Consola Whatsapp.</li>
                                                        <li>Da clic en <strong> Guardar todo</strong>, listo usuario asignado a WhatsApp.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Desasignar WhatsApp a operador</li>
                                                    <ol>
                                                        <li>Damos clic en <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Usuarios</strong></li>
                                                        <li>Selecciona al usuario y damos clic en <strong> Permisos</strong>.</li>
                                                        <li>En la seccion <strong>Cuentas Whatsapp</strong> haz clic en Ninguna y en la seccción <strong>Funcionalidad</strong> haz clic en la casilla <i class="far fa-check-square"></i> Consola Whatsapp.</li>
                                                        <li>Listo usuario desasignado de WhatsApp.</li>
                                                    </ol>
                                                </ul><br />
                                            </ul>
                                            <h4 class="text-info">Funcionamiento</h4>
                                            <ul>
                                                <li>WhastApp inicia al recibir la alerta y termina al finalizar una conversación.</li><br />
                                                    <ul>
                                                        <li style="font-size: 15px;">Alerta de mensaje</li>
                                                        <ul>
                                                            <li>Cuando el sistema asigne al operador un WhatsApp nuevo se mostrara una alerta<strong> Tienes un WhatsApp.</strong>.</li>
                                                            <li>En el modulo <i class="fab fa-whatsapp"></i> mostrara este icono <i style="color: #ff0000; font-size: 10px;" class="fas fa-circle"></i>, indicando que hay mensaje(s) nuevos.</li>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-119-modal-sm">nuevo mensaje</button></dl>
                                                        <li style="font-size: 15px;"> Contactos</li>
                                                        <ul>
                                                            <li>Se visualizan todos los usuarios con los que hemos tenido alguna interacción <i class="text-info"> por primera vez el cliente debe enviar el mensaje.</i></li>
                                                            <li>Los contactos que se muestran a la <span class="text-info">izquierda</span> son todos los existentes y a la <strong class="text-info">derecha</strong> los asignados al operador <strong>nuevos</strong>.</li>
                                                            <li>Para seleccionar un contacto solo debemos dar clic sobre el nombre o telefono que se muestra.</li>
                                                            <li>Si queremos enviar un mensaje a todos los contactos, debemos dar clic en <span class="text-info">Todos *</span>.</li>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-120-modal-sm">contactos</button></dl>
                                                        <li style="font-size: 15px;"> Buscar</li>
                                                        <ul>
                                                            <li>Ingresa el nombre o numero del celular y da clic en <strong>Buscar</strong><i class="text-info"> puedes usar palabras de referencia en la búsqueda</i>.</li>
                                                            <li>Bajo la opción busqueda mostrara los resultados, para acceder da clic sobre el registro, sino hay busqueda exitosa mostrara <i>Sin resutados</i>.</li>
                                                            <li>El buscador realizara su funcion con todos los registros (contactos).</li>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-122-modal-sm">buscar</button></dl>
                                                        <li style="font-size: 15px;"> Chat</li>
                                                        <ol>
                                                            <li>Al dar clic sobre algun contacto desplegara la ventana para interactuar con el contacto.</li>
                                                            <li>En la parte superior mostrara nombre y (numero del contacto).</li>
                                                            <li>La ventana nos permite visualizar 20 mensajes (imagenes, emojis, texto, etc) si necesitamos ver anteriores damos clic en <i style="color: #17a2b8;">Cargar anteriores </i>.</li>
                                                            <li>Los mensajes del cliente se desplegaran del lado izquiero con fondo blanco.</li>
                                                            <li>Los mensajes enviados por nosotros se mostraran del lado derecho en fondo verde.</li>
                                                            <li>Para enviar un mensaje, escribimos en la barra y damos clic en <i class="fas fa-paper-plane"></i></li>
                                                            <li>Si deseas adjuntar un archivo da clic en <i class="fas fa-paperclip"></i> busca el archivo y da clic en <strong>Enviar</strong></li>
                                                            <li>Para finalizar debemos dar clic en <strong>Terminar conversación</strong> así el operador pasara de ocupado a disponible.</li>
                                                            <li>Si es necesario transferir la conversación dar clic en el select <strong>Transferir chat a:</strong> y clic en <i style="color: #17a2b8">Transferir</i></li>
                                                        </ol>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-121-modal-lg">mensajes</button></dl>
                                                        <li style="font-size: 15px;"> Notificaciones</li>
                                                        <ul>
                                                            <li>Puedes dar clic en cualquir contacto para abrir la conversación siempre que este libre<strong> (finalizado)</strong>, pero en algunas ocaciones te mostrara una alerta:</li>
                                                            <li><strong>Contacto activo</strong> se muestra cuando ya tienes la conversación abierta.</li>
                                                            <li><strong>Contacto asignado a <i> algun nombre </i></strong> ese contacto no esta libre esta asignado a otro agente.</li>
                                                            <li><strong>Sin mensajes para mostrar</strong> no hay mensajes en la conversación.</li>
                                                            <li><strong>No es una sesion activa</strong> no puedes transferir un contacto con sesion finalizada.</li>
                                                        </ul>
                                                    </ul>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-119-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/wicono.png"); ?>" alt="wicono" height="100" width="500">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-120-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/wcontactos.png"); ?>" alt="wcontactos" height="600" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-121-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/wconversacion.png"); ?>" alt="wconversacion" height="400" width="900">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-122-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/bwhatsapp.png"); ?>" alt="bwhatsapp" height="620" width="450">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon whatsapp-->
                                    <div class="collapse" id="collapseCalendario" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info"> Calendarizar</h4>
                                            <ul>
                                                <li>Muestra los recordatorios <i class="text-info">agregados y asignados al agente</i> como tarjetas.</li>
                                                <li>Las tarjetas se muestran en color <strong style="color: #28a745;"> verde</strong> si estan vigentes y en <strong style="color: #dc3545">rojo</strong> si estan vencidas.</li>
                                                <li>Podremos agendar, reagendar o cancelar algun recordatorio.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Agendar</li>
                                                    <ol>
                                                        <li>Da clic en <strong><i class="far fa-calendar-alt"></i> Calendarizar</strong>.</li>
                                                        <li>Mostrara una ventana con los campos a llenar.</li>
                                                        <li>Damos clic en <strong>Agregar</strong> y nos mostrara <strong>Calendarización exitosa</strong>.</li>
                                                        <li>La tarjeta se mostrara y el recordatorio a sido agregado.</li>
                                                    </ol>
                                                    <dl>
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-41-modal-sm"><i>calendarizar</i></button>
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-42-modal-sm"><i>nuevo</i></button>
                                                    </dl>
                                                    <li style="font-size: 15px;">Reagendar</li>
                                                    <ol>
                                                        <li>Selecciona la tarjeta y modifica la fecha y/o mensaje.<i> Nombre y apellido no son modificables.</i></li>
                                                        <li>Damos clic en <strong>Reagendar</strong> y nos mostrara <strong>Modificado con exito.</strong>.</li>
                                                        <li>Nos mostrara la nueva tarjeta con los cambios.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;">Cancelar</li>
                                                    <ol>
                                                        <li>Seleccionamos la tarjeta y damos clic en <strong>Cancelar</strong> mostrara <strong>Modificado con exito.</strong>.</li>
                                                        <li>La tarjeta ya no sera visible <i>pero se guarda como recordatorio cancelado</i>.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-43-modal-sm"><i>reagendar ó cancelar</i></button></dl>
                                                </ul>
                                                <li>Para asignar un recordatorio a un operador revisa el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Calendarizar </strong></li>
                                            </ul><br />
                                            <h4 class="text-info">Recordatorio</h4>
                                            <ul>
                                                <li>Calendarizado el evento, mostrara un Recordatorio en la fecha y hora señalada.</li>
                                                <li>Para visualizar el recordatorio damos clic en <i class="far fa-bell"></i><strong> Recordatorio</strong>.</li>
                                                <li>A diferencia del apartado anterior solo podras reagendar, terminar o cancelar.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Reagendar</li>
                                                    <ol>
                                                        <li>Damos clic en <strong>Reagendar</strong> nos mostrara <strong>Modificado con exito</strong>.</li>
                                                        <li>Desaparece el botón y la tarjeta se guarda en Calendarizar.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Terminada</li>
                                                    <ol>
                                                        <li>Damos clic en <strong>Terminada</strong> nos mostrara <strong>Gracias</strong>.</li>
                                                        <li>Desaparece el botón y la tarjeta se guarda en Calendarizar.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Cancelar</li>
                                                    <ol>
                                                        <li>Damos clic en <strong>Cancelar</strong> y nos mostrara <strong>Gracias</strong>.</li>
                                                        <li>Desaparece el botón, la tarjeta ya no es visible en Calendarizar <i style="color: #16a7c0;"> pero el registro se guarda en sistema</i>.</li>
                                                    </ol>
                                                </ul>
                                                <dl>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-44-modal-sm"><i>alerta recordatorio</i></button>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-45-modal-sm"><i>recordatorio</i></button>
                                                </dl>
                                            </ul>
                                            <div class="modal fade bd-41-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/cnuevo.png"); ?>" alt="cnuevo" height="150" width="550">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-42-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/nuevocalendar.png"); ?>" alt="nuevocalendar" height="400" width="350">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-43-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/creagendar.png"); ?>" alt="creagendar" height="350" width="550">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-44-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/arecordatorio.png"); ?>" alt="arecordatorio" height="100" width="550">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-45-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/rrecordatorio.png"); ?>" alt="rrecordatorio" height="400" width="350">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon calendarizar-->
                                    <div class="collapse" id="collapseExt" data-parent="#accorbarra">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Ext</h4>
                                            <ul>
                                                <li>En Assertive contamos con un modulo adicional, Ext <i>extra(s)</i>, este permite incrustar algunas herramientas adicionales.</li>
                                                <li>Podemos agregar hasta 2 Ext y tener de manera mas completa lo necesario para los operadores en el sistema.</li>
                                                <li>Para la configuración de Ext revisar el apartado <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;"> Configuración / Generales</strong>.</li><br />
                                                <ul>
                                                <li>Algunas herramientas podrian ser</li>
                                                    <ul>
                                                        <li>Paginas informativas</li>
                                                        <li>Paginas catalogos de productos</li>
                                                        <li>Chats servicio al cliente</li>
                                                        <li>Etc..</li>
                                                    </ul>
                                                </ul>
                                                <dl>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-ext44-modal-sm"><i>ext</i></button>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-ext2_44-modal-sm"><i>ext2</i></button>
                                                </dl>
                                            </ul>
                                            <div class="modal fade bd-ext44-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/eext.png"); ?>" alt="epantalla1" height="500" width="980">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-ext2_44-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/eext2.png"); ?>" alt="epantalla1" height="550" width="980">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon ext-->
                                </div><!-- cierre acordeon consola -->
                            </div><!-- cierre menu botones consola-->
                        </div> <!-- cierre card consola-->
                        <div class="card">
                            <div class="card-header nopadding" id="heading002">
                                <h3 class="mb-0">
                                    <button class="btn btn-info btn-lg btn-block collapsed" type="button" data-toggle="collapse" data-target="#collapse002" aria-expanded="false" aria-controls="collapse002">
                                        <i class="fas fa-mobile-alt"></i>&nbsp;&nbsp;Web Phone
                                    </button>
                                </h3>
                            </div>
                            <div id="collapse002" class="collapse" aria-labelledby="heading002" data-parent="#accorManual">
                                <div class="accordion" id="accorphone">
                                    <center>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#llamada" aria-expanded="false" aria-controls="llamada">
                                            <i class="fas fa-headset"></i> Hacer llamada
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#encurso" aria-expanded="false" aria-controls="encurso">
                                            <i class="fas fa-phone-volume"></i> Llamada en curso
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#tipollamada" aria-expanded="false" aria-controls="tipollamada">
                                            <i class="fab fa-weixin"></i> Tipo de llamada
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#color" aria-expanded="false" aria-controls="color">
                                            <i class="fas fa-palette"></i> Color de llamada
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#chanspy" aria-expanded="false" aria-controls="chanspy">
                                            <i class="fas fa-user-secret"></i> Chanspy
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#ocultar" aria-expanded="false" aria-controls="ocultar">
                                            <i class="fas fa-eye-slash"></i> Ocultar webphone
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#historial" aria-expanded="false" aria-controls="historial">
                                            <i class="fas fa-align-center"></i> Historial llamadas
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#conferencia" aria-expanded="false" aria-controls="conferencia">
                                            <i class="fas fa-users"></i> Conferencias
                                        </button>
                                        <button class="btn btn-info collapsed " type="button" data-toggle="collapse" data-target="#transferencia" aria-expanded="false" aria-controls="transferencia">
                                            <i class="fas fa-retweet"></i> Transferencias
                                        </button>
                                    </center>
                                    <div class="collapse" id="llamada" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Llamadas</h4>
                                            <ul>
                                            <li>Para realizar una llamada ingresaremos los números con el teclado que se muestra del webphone o desde el teclado del dispositivo, PC, telefono etc.</li>
                                            <li>La marcación se puede realizar con diferentes opciones: directa, con prefijo, o extension.</li>
                                            <li>Marcación con prefijo se realiza oprimiendo un digito o combinacion de digitos antes del numero telefónico <i style="color: #17a2b8;">(consulte que número de prefijo tiene asignado).</i></li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Marcación directa</li>
                                                    <ul>
                                                        <li>Número a 10 digitos + Llamar</li>
                                                        <li>5531313131 + Llamar</li>
                                                        <li>5512345678 + Llamar</li>
                                                    </ul>
                                                    <dt><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-01-modal-sm"><i>directa</i></button></dt><br />
                                                    <li style="font-size: 15px;">Marcación con prefijo</li>
                                                    <ul>
                                                        <li>Prefijo + Numero a 10 digitos + Llamar</li>
                                                        <li>9 + 5531313131 + Llamar = 95531313131 + Llamar</li>
                                                        <li>22 + 5512345678 + Llamar = 225512345678 + Llamar</li>
                                                    </ul>
                                                    <dt><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-02-modal-sm"><i>prefijo</i></button></dt><br />
                                                    <li style="font-size: 15px;">Marcación a extensión</li>
                                                    <ul>
                                                        <li>Extensión + Llamar</li>
                                                        <li>9120 + Llamar</li>
                                                        <li>1314 + Llamar</li>
                                                    </ul>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-01-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/webphone1.png"); ?>" alt="webphone1" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-02-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/webphone2.png"); ?>" alt="webphone2" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon llamadas-->
                                    <div class="collapse" id="encurso" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Llamada en curso</h4>
                                            <ul>
                                            <li>Al <strong>realizar o recibir</strong> una llamada se desplegara el menú con diferentes botones.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Descripción de botónes</li>
                                                    <ul>
                                                        <li><i class="fa fa-comments text-success"></i><span class="text-info"> Conferencia</span> permite realizar la unión de 1 o más llamadas en una sala para conferencia.</li>
                                                        <li><i class="fa fa-share text-secondary"></i><span class="text-info"> Transferir</span> realiza el envío de una llamada a otro número marcado con opción a retomar la llamada.</li>
                                                        <li><i class="fa fa-pause text-info"></i><span class="text-info"> Pausa </span> deja en espera la llamada en curso.</li>
                                                        <li><i class="fa fa-random text-info"></i><span class="text-info"> Transferir desatendido</span> realiza el envío de una llamada a otro número sin opción a retomar la llamada.</li>
                                                        <li><i class="fa fa-microphone text-warning"></i><span class="text-info"> Silenciar micrófono</span> deja el micrófono inactivo.</li>
                                                        <li><i class="fa fa-stop text-danger"></i><span class="text-info"> Terminar o Colgar</span> finaliza la llamada.</li>
                                                    </ul><br />
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-03-modal-sm"><i>menú botones</i></button></dl>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-03-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/encurso.png"); ?>" alt="encurso" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon en curso-->
                                    <div class="collapse" id="tipollamada" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Tipo de llamada</h4>
                                            <ul>
                                            <li>Toda llamada <strong>entrante ó saliente</strong> mostrara en un apartado bajo el webphone como<i> (historico)</i> con su detalle.</li><br />
                                                <ul>
                                                <li style="font-size: 15px;">Llamada entrante</li>
                                                    <ul>
                                                        <li><span class="text-info">Entrante</span><span style="font-weight: bold;"> < </span></li>
                                                        <li><span class="text-info">Número</span> 5552485248</li>
                                                        <li><span class="text-info">Fecha mes/dia</span> 08/20</li>
                                                        <li><span class="text-info">Hora am/pm</span> 12:37:35 pm</li>
                                                        <li><span class="text-info">Duración</span> 6 minutos</li>
                                                    </ul><br />
                                                    <li style="font-size: 15px;">Llamada saliente</li>
                                                    <ul>
                                                        <li><span class="text-info">Saliente</span><span style="font-weight: bold;"> > </span></li>
                                                        <li><span class="text-info">Número</span> 5578987898</li>
                                                        <li><span class="text-info">Fecha mes/día</span> 08/20</li>
                                                        <li><span class="text-info">Hora am/pm</span> 02:38:25 pm</li>
                                                        <li><span class="text-info">Duración</span> 4 minutos</li>
                                                    </ul><br />
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-04-modal-sm"><i>entrante - saliente</i></button></dl>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-04-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/wentrantesaliente.png"); ?>" alt="wentrantesaliente" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon tipo-->
                                    <div class="collapse" id="color" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Status de color</h4>
                                            <ul>
                                            <li>Al dar clic en el botón <strong>Llamar</strong> se despliega la barra control de llamada.</li>
                                            <li>Esta barra muestra un color tenue de fondo dependiendo lo que este realizando.</li><br />
                                                <ul>
                                                <li style="font-size: 15px;">Color en control de llamada</li>
                                                    <ul>
                                                        <li><span class="badge bg-success text-dark">_____</span> llamada enlazandose, proceso de espera mientras sale la llamada.</li>
                                                        <li><span class="badge bg-info text-dark">_____</span> enlace correcto, proceso de espera mientras llama al receptor.</li>
                                                        <li><span class="badge bg-warning text-dark">_____</span> llamada en pausa, no hay interacción en llamada.</li>
                                                        <li><span class="badge bg-secondary text-white">_____</span> micrófono en mudo, no hay audio de salida.</li>
                                                        <li><span class="badge bg-danger text-white">_____</span> llamada no enlazada.</li>
                                                    </ul>
                                                </ul><br />
                                                <dl>
                                                    <button type="button" class="btn btn-link text-success" data-toggle="modal" data-target=".bd-05-modal-sm"><i>enlazandose</i></button>
                                                    <button type="button" class="btn btn-link text-info" data-toggle="modal" data-target=".bd-06-modal-sm"><i>enlazado</i></button>
                                                    <button type="button" class="btn btn-link text-warning" data-toggle="modal" data-target=".bd-07-modal-sm"><i>pausa</i></button>
                                                    <button type="button" class="btn btn-link text-secondary" data-toggle="modal" data-target=".bd-08-modal-sm"><i>mudo</i></button>
                                                    <button type="button" class="btn btn-link text-danger" data-toggle="modal" data-target=".bd-09-modal-sm"><i>no enlaza</i></button>
                                                </dl>
                                            </ul>
                                            <div class="modal fade bd-05-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/verde.png"); ?>" alt="Web Phone" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-06-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/azul.png"); ?>" alt="Web Phone" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-07-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/amarillo.png"); ?>" alt="Web Phone" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-08-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/gris.png"); ?>" alt="Web Phone" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-09-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/rojo.png"); ?>" alt="Web Phone" height="550" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon status de color-->
                                    <div class="collapse" id="chanspy" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Chanspy</h4>
                                            <ul>
                                            <li>Nos permite escuchar la conversación del agente en tiempo real de cualquier campaña.</li>
                                            <li>Para activar el botón de chanspy debemos tener los permisos necesarios, revisar el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Usuarios / Permisos</strong>.</li><br />
                                                <ul>
                                                <li style="font-size: 15px;">Proceso de marcación</li>
                                                    <ol>
                                                        <li>Verificar que agente este en linea, puede estar en llamada o no.</li>
                                                        <li>Tener el número de extensión asignada <i class="text-info">8012, 3015</i> etc.</li>
                                                        <li>Da clic en<i class="text-primary"> Chanspy</i>, este realizara una llamada al 555.</li>
                                                        <li>Se escuchara el siguiente audio<i class="text-info"> por favor ingresa la extensión seguido de la tecla de <strong>#</strong>número.</i></li>
                                                        <li>Ingresa la <i class="text-primary">extensión</i> con el teclado del web phone<i class="text-info"> 8012#</i>.</li>
                                                        <li>Después de unos segundos se escuchara la llamada.</li>
                                                        <li>Para finalizar chanspy haz clic en el icono <i class="fa fa-stop text-danger"></i> colgar.</li>
                                                    </ol><br />
                                                    <dl>
                                                        <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target=".bd-10-modal-sm"><i>chanspy</i></button>
                                                        <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target=".bd-11-modal-sm"><i>extensión</i></button>
                                                    </dl>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-10-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/chanspy1.png"); ?>" alt="Web Phone" height="580" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-11-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/chanspy2.png"); ?>" alt="Web Phone" height="580" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon chanspy-->
                                    <div class="collapse" id="ocultar" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Ocultar Webphone</h4>
                                            <ul>
                                            <li>El WebPhone por default estará despleglado, ocupando un área en pantalla que no dificulta la visibilidad.</li><br />
                                                <ul>
                                                    <li>Si es necesaria mayor visibilidad, damos click en el icono <img src="<?php echo site_url("assets/img/imgmanual/ico_phone.png"); ?>" width="18"> y se ocultara el webphone.</li>
                                                    <li>Al ocultar el webphone <strong>NO</strong> se tendrá visible el historial, la llamada, hora, fecha y/o duración.</li>
                                                    <li>Podrás hacer uso del botón estando o no en llamada.</li>
                                                    <li>Para hacer visible el webphone nuevamente da clic en el icono <img src="<?php echo site_url("assets/img/imgmanual/ico_phone.png"); ?>" width="18">.</li>
                                                </ul>
                                            </ul>
                                        </div>
                                    </div><!-- cierre accordeon ocultar webphone-->
                                    <div class="collapse" id="historial" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Historial de llamadas</h4>
                                            <ul>
                                                <li>Al finalizar una llamada <strong>entrante o saliente</strong> esta permanecera en el área de <i class="text-info">llamadas recientes.</i></li>
                                                <li>Cada que se finalice una llamada esta lista crecera, si no es más de utilidad podremos borrar el listado.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Borrado</li>
                                                    <ol>
                                                        <li>Da clic en el icono <i class="fa fa-trash text-secondary"></i> <strong>Llamadas recientes</strong> y borrara el historial.</li>
                                                        <li>Puedes repetir este proceso cada que sea necesario.</li>
                                                    </ol>
                                                </ul>
                                                <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-12-modal-sm"><i>historial</i></button></dl>
                                            </ul>
                                            <div class="modal fade bd-12-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/hpantalla.png"); ?>" alt="hpantalla" height="900" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon historial de llamadas-->
                                    <div class="collapse" id="conferencia" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Conferencia</h4>
                                            <ul>
                                            <li>Es la unión de 2 o más llamadas en una sala, para iniciar una conferencia realizaremos lo siguiente:</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Iniciar conferencia</li>
                                                        <ol>
                                                            <li>Haz la marcación al 1er numero y colocamos la llamada en pausa <i class="fa fa-pause text-info"></i> ,<strong> repetimos el proceso</strong> para las llamadas que agregaremos a la conferencia.</li>
                                                            <li>Ya que tenemos todas las llamadas <strong> pausadas</strong> damos clic en el botón <strong class="text-info">Conferencia</strong> para habilitar la sala.</li>
                                                            <li>Se escuchara una grabacion <strong><i>(Por el momento usted es la única persona en la conferencia)</i></strong>.</li>
                                                            <li>Al termino de la grabación damos clic en el icono <strong class="text-success"><i class="fa fa-comments"></i></strong> de cada llamada para agregarla a la sala.</li>
                                                            <li>Se iran mostrando en la sala las <i class="text-primary">llamadas </i>que se van agregando.</li>
                                                            <li>Listo conferencia iniciada.</li>
                                                        </ol>
                                                        <dl>
                                                            <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target=".bd-13-modal-sm"><i>pausa</i></button>
                                                            <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target=".bd-14-modal-sm"><i>llamadas</i></button>
                                                        </dl>
                                                    <li style="font-size: 15px;"> Terminar conferencia</li>
                                                        <ol>
                                                            <li>Cada participante podra salir de la sala colgando la llamada desde su dispositivo <i>celular, teléfono fijo, etc</i>.</li>
                                                            <li>El agente podra terminar las llamadas dando clic <strong>Colgar</strong> al lado de cada numero telefónico.</li>
                                                            <li>Repetir el proceso para terminar cada llamada.</li>
                                                            <li>Por ultimo damos clic en <strong>Cerrar</strong> para finalizar la sala de conferencias.</li>
                                                            <li>Listo conferencia finalizada</li>
                                                        </ol><br />
                                                    <li style="font-size: 15px;">Expulsar participantes</li>
                                                        <ul>
                                                            <li>Para sacar a uno o más participante solo damos clic en el botón <strong>Colgar</strong> que esta al lado de cada número telefónico.</li>
                                                            <li>Listo participante expulsado.</li>
                                                        </ul>
                                                </ul><br />
                                            <li>Si el operador colgara su propia extensión, no hay problema puede regresar a la conferencia marcando <i class="text-info">no de extension + 1000</i>.</li>
                                            <li>Si tu extensión es<i class="text-info"> 5091</i> se suma <i class="text-info">1000</i> tendrías que marcar la extension <i class="text-info"> 6091</i>.</li>
                                            </ul>
                                            <div class="modal fade bd-13-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/conferenciapausa.png"); ?>" alt="Web Phone" height="680" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-14-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/conferenciallamadas.png"); ?>" alt="Web Phone" height="680" width="420">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon conferencia-->
                                    <div class="collapse" id="transferencia" data-parent="#accorphone">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Transferencia</h4>
                                            <ul>
                                            <li>A diferencia de la conferencia, esta no requiere de una sala, solo a quien se canalizara la llamada <i class="text-info">receptor</i>.</li>
                                            <li>Hay 2 maneras de enviar una llamada, por transferencia asistida ó transferencia ciega.</li><br />
                                                <ul>
                                                <li style="font-size: 15px;"> Transferencia asistida</li>
                                                    <ol>
                                                        <li>Tener la llamada que solicita la transfrencia y ponerla en pausa <i class="fa fa-pause text-info"></i> puede ser<i class="text-info"> entrante o saliente</i>.</li>
                                                        <li>Marcar el número donde será transferida la llamada.</li>
                                                        <ul>
                                                            <li>Si no hay respuesta podemos colgar y retomar la primer llamada para informar que no hay contacto.</li><br />
                                                        </ul>
                                                        <li>Una vez que tenemos la segunda llamada damos clic en <strong>transferir <i class="fa fa-share text-secondary"></i></strong> en la llamada que solicita la transferencia.</li>
                                                        <li>Llamada transferida.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target=".bd-15-modal-sm"><i>asistida</i></button></dl><br />
                                                <li style="font-size: 15px;"> Transferencia ciega</li>
                                                    <ol>
                                                        <li>Tener la llamada que solicita la transfrencia y damos clic en <strong>tranferir desatendido <i class="fa fa-random text-info"></i></strong> puede ser <i class="text-info"> entrante o saliente</i>.</li>
                                                        <li>Nos mostrara un recuadro, agregamos el numero de destino y damos clic en <strong>Aceptar</strong>.</li>
                                                        <li>Llamada transferida.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-16-modal-sm"><i>ciega</i></button></dl>
                                                </ul>
                                            <li>La diferencia entre transferencia<i class="text-info"> asistida y ciega</i> es que en esta última <strong>NO</strong> sabemos si fue <i class="text-info">contestada, entro al buzón o se perdio la llamada</i>.</li>
                                            </ul>
                                            <div class="modal fade bd-15-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/transasistida.png"); ?>" alt="Web Phone" height="680" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-16-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/transciega2.png"); ?>" alt="Web Phone" height="600" width="950">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon transferencia-->
                                </div><!-- cierre acordeon web phone -->
                            </div><!-- cierre menu botones web phone-->
                        </div> <!-- cierre card web phone-->
                        <div class="card">
                            <div class="card-header nopadding" id="heading005">
                                <h3 class="mb-0">
                                    <button class="btn btn-info btn-lg btn-block collapsed" type="button" data-toggle="collapse" data-target="#collapse005" aria-expanded="false" aria-controls="collapse005">
                                        <i class="fas fa-tools"></i>&nbsp;&nbsp;Configuración
                                    </button>
                                </h3>
                            </div>
                            <div id="collapse005" class="collapse" aria-labelledby="heading005" data-parent="#accorManual">
                                <div class="accordion" id="accorConfig">
                                    <center>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseAgenda" aria-expanded="false" aria-controls="collapseAgenda">
                                            Agenda
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseCalendarizar" aria-expanded="false" aria-controls="collapseCalendarizar">
                                            Calendarizar
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseCalidad" aria-expanded="false" aria-controls="collapseCalidad">
                                            Calidad
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseCampañas" aria-expanded="false" aria-controls="collapseCampañas">
                                            Campañas
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseDespachador" aria-expanded="false" aria-controls="collapseDespachador">
                                            Despachador
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseEmail" aria-expanded="false" aria-controls="collapseEmail">
                                            Email
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseFormulario" aria-expanded="false" aria-controls="collapseFormulario">
                                            Formulario
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseGenerales" aria-expanded="false" aria-controls="collapseGenerales">
                                            Generales
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseSMS" aria-expanded="false" aria-controls="collapseSMS">
                                            SMS
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseUsuarios" aria-expanded="false" aria-controls="collapseUsuarios">
                                            Usuarios
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseCWhatsapp" aria-expanded="false" aria-controls="collapseCWhatsapp">
                                            Whatsapp
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseEmergencia" aria-expanded="false" aria-controls="collapseEmergencia">
                                            Emergencia
                                        </button>
                                        <button class="btn btn-info collapsed" type="button" data-toggle="collapse" data-target="#collapseLicencia" aria-expanded="false" aria-controls="collapseLicencia">
                                            Licencia
                                        </button>
                                    </center>
                                    <div class="collapse" id="collapseAgenda" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Agenda</h4>
                                            <ul>
                                                <li>Agenda nos permite realizar <strong>búsqueda, agregar y modificar registros</strong> asi como <strong>exportar</strong> la información a excel.</li>
                                                <li>Estando en <strong>Consola / Home</strong> la agenda permite tener una interacción directa con cada cliente, mientras en este apartado es <i class="text-info">almacenaje, busqueda y exportación</i>.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Buscar</li>
                                                    <ol>
                                                        <li>Opciones de busqueda <i>por telefono, nombre, apellido o email</i>.</li>
                                                        <li>Ingresamos el texto y damos cloc en <strong>Buscar</strong> mostrara los registros que coincidan con esos parámetros debajo del boton <strong>Nuevo</strong>.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-37-modal-sm"><i>buscar</i></button></dl>
                                                    <li style="font-size: 15px;">Agregar</li>
                                                    <ol>
                                                        <li>Damos clic en el botón <strong>Nuevo</strong>.</li>
                                                        <li>Se desplegara una ventana con los campos a llenar, hay <strong>requeridos y opcionales</strong>.</li>
                                                            <ul>
                                                                <li><strong class="text-info">Requeridos</strong></li>
                                                                <ul>
                                                                    <li><strong>Activo</strong> debe estar <i class="far fa-check-square"></i> para visualizar el registro.</li>
                                                                    <li><strong>Contactable</strong> debe estar <i class="far fa-check-square"></i> para desplegar las opciones directas en Home y envio de email por el CRM.</li>
                                                                    <li><strong>Agenda</strong> Publico <i class="text-info">(visible para todos)</i> o elegir que agente podra ver el registro.</li>
                                                                    <li><strong>Campaña</strong> elegir en que campaña estara visible el registro.</li>
                                                                </ul>
                                                                <li><strong class="text-info">Opcionales</strong></li>
                                                                <ul>
                                                                    <li>Llenar el resto de los campos segun sea necesario.</li>
                                                                </ul>
                                                            </ul><br />
                                                        <li>Damos clic en <strong>Guardar</strong> y mostrara <strong><i>Registro guardado</i></strong>.</li>
                                                        <li>Registro agregado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-38-modal-sm"><i>agregar</i></button></dl>
                                                    <li style="font-size: 15px;">Modificar</li>
                                                    <ol>
                                                        <li>Da clic en <strong>Editar</strong> del registro a modificar.</li>
                                                        <li>Se despliega una ventana, realizamos los cambios necesarios.</li>
                                                        <li>Da clic en <strong>Guardar</strong> nos mostrara <strong>Registro guardado</strong>.</li>
                                                        <li>Registro actualizado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-39-modal-sm"><i>modificar</i></button></dl>

                                                    <li style="font-size: 15px;"> Exportar a excel</li>
                                                    <ul>
                                                        <li>Da clic en el icono de Excel y comenzara la descarga con el nombre de <i>agenda.csv</i></li>
                                                        <li>Archivo descagado.</li><br />
                                                        <dl>Sino se muestra la descarga revisa la carpeta <i class="text-info">Descargas</i> de tu computadora.</dl>
                                                    </ul>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-37-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/buscaagenda.png"); ?>" alt="busca agenda" height="180" width="850">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-38-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/aconfnuevo.png"); ?>" alt="aconfnuevo" height="550" width="750">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-39-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/aconfmodificar.png"); ?>" alt="aconfmodificar" height="550" width="750">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon agenda-->
                                    <div class="collapse" id="collapseCalendarizar" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Calendarizar</h4>
                                            <ul>
                                                <li>La manera en Calendarizar es similar que en <strong>Consola / Calendario / Calendarizar</strong> ya que podemos agendar, reagendar y cancelar un recordatorio.</li>
                                                <li>Adicional en este apartado podemos asignar o reasignar recordarios sin que el operador tenga activa una sesion.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Crear</li>
                                                    <ol>
                                                        <li>Agrega el <i class="text-info">nombre y apelldo</i>.</li>
                                                        <li>Selecciona tipo <i class="text-info">Llamar, SMS, Email, Otro</i><strong> (manera de contacto)</strong>.</li>
                                                        <li>Asigna una <i class="text-info">fecha y hora de recordatorio</i>.</li>
                                                        <li>Selecciona <i class="text-info">el agente</i> que lo contactara.</li>
                                                        <li>Agrega <i class="text-info">observaciones</i> <strong>(opcional)</strong>.</li>
                                                        <li>Da clic en <strong>Crear</strong> nos mostrara <strong>Calendarización exitosa</strong>.</li>
                                                        <li>Recordatorio agregado al listado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-46-modal-sm"><i>asignar</i></button></dl>
                                                    <li style="font-size: 15px;"> Reagendar</li>
                                                    <ol>
                                                        <li>Selecciona el registro, podras modificar <i class="text-info">fecha & hora, reasignar al agente y observaciones</i> unicamente.</li>
                                                        <li>Da clic en <strong>Reagendar</strong> nos mostrara <strong>Calendarización actualizada</strong>.</li>
                                                        <li>Recordatorio actualizado.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Cancelar</li>
                                                    <ol>
                                                        <li>Selecciona el registro y da clic en <strong>Cancelar</strong> nos mostrara <strong>Calendarización actualizada</strong>.</li>
                                                        <li>Recordatorio cancelado <i class="text-info">(no se muestra en pantalla pero se guarda como recordatorio cancelado)</i>.</li>
                                                    </ol>
                                                </ul>
                                                <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-47-modal-sm"><i>reagendar o cancelar</i></button></dl>
                                            </ul>
                                            <div class="modal fade bd-46-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/creaasignacion.png"); ?>" alt="crea" height="150" width="950">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-47-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/confcalendar.png"); ?>" alt="confcalendar" height="250" width="950">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon calendarizar-->
                                    <div class="collapse" id="collapseCalidad" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Calidad</h4>
                                            <ul>
                                                <li>Nos permite crear <strong>Cédulas de evaluación</strong> para el agente por campaña en llamadas inbound y outbound.</li>
                                                <li>Para crear una cédula previamente debemos haber creado una campaña, revisar el apartado <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Campañas</strong></li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Crear cédula</li>
                                                    <ol>
                                                        <li><strong>Nombre de evaluación</strong> nombre para identificar la cédula.</li>
                                                        <li><strong>Selecciona campaña</strong> a que campaña estas asignando la cédula.</li>
                                                        <li><strong>Activo</strong> activar o desactivar la cédula <i>si se desactiva esta no sera visible para calificar una llamada</i>.</li>
                                                        <li>Da clic en <strong>Crear</strong> nos mostrara <strong>Formulario creado con éxito</strong>.</li>
                                                        <li>Cédula creada, esta se mostrara en el listado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-48-modal-sm"><i>crea cédula</i></button></dl>
                                                    <li style="font-size: 15px;">Actualizar cédula</li>
                                                    <ol>
                                                        <li>Selecciona la cédula y modifica <strong>nombre de evaluacion, nombre de campaña o check activo</strong>.</li>
                                                        <li>Da clic en <strong>Actualizar</strong> nos mostrara <strong>Formulario actualizado con éxito</strong>.</li>
                                                        <li>Cédula actualizada.</li>
                                                    </ol><br /><br />
                                                    <li style="font-size: 15px;">Agregar preguntas</li>
                                                    <ol>
                                                        <li>Identifica la cedula y da clic en <strong>Campos</strong> <i class="text-info">agregaremos las preguntas a evaluar</i>.</li>
                                                        <li><strong>Pregunta</strong> rubro que estamos por evaluar en la llamada.</li>
                                                        <li><strong>Ponderación</strong> valor que se asigna a esa pregunta <i class="text-info">el valor total de preguntas no debe pasar de 100</i>.</li>
                                                        <li><strong>No.Orden</strong> orden en el que se mostraran las preguntas. <i class="text-info">el orden iniciara desde 0</i>.</li>
                                                        <li>Da clic en <strong>Agregar</strong>.</li>
                                                        <li>Se iran agregando al listado <i class="text-info">para agregar más preguntas <strong>repetir los pasos 2 al 5</strong></i>.</li>
                                                    </ol>
                                                    <dl>
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-49-modal-sm"><i>identificar</i></button>
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-50-modal-sm"><i>crea pregunta</i></button>
                                                    </dl>
                                                    <li style="font-size: 15px;">Actualizar preguntas</li>
                                                    <ol>
                                                        <li>Selecciona la pregunta y modifica pregunta, ponderación u orden.</li>
                                                        <li>Da clic en <strong>Actualizar</strong>, se efectuara el cambio.</li>
                                                        <li>Pregunta actualizada.</li>
                                                    </ol>
                                                </ul><br />
                                                <li><i>Identificaremos el nombre de la cédula en la parte superior en color azul</i>.</li>
                                            </ul>
                                            <div class="modal fade bd-48-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/calidadcreaced.png"); ?>" alt="calidadcreaced" height="150" width="880">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-49-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/identcedula.png"); ?>" alt="confcalendar" height="380" width="850">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-50-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/pregcedula.png"); ?>" alt="confcalendar" height="250" width="800">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon calidad-->
                                    <div class="collapse" id="collapseCampañas" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Campañas</h4>
                                            <ul>
                                                <li>El punto de partida para cada modulo de Assertive es la creación de <strong>Campaña(s)</strong>.</li>
                                                <li>Para cada menu en Consola, Configuración, Reportes o Monitoreo sera indispensable la Campaña.</li>
                                                <li>Este módulo permite crear y actualizar la campaña, así mismo modificar los horarios de atención de dicha campaña.</li>
                                                <li>Iniciaremos creando la campaña y posteriormente como se modifican los horarios.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Crear campaña</li>
                                                    <ol>
                                                        <li>Da clic en <strong>Nueva</strong>.</li>
                                                        <li><strong>Nombre</strong> que asignaremos a la campaña.</li>
                                                        <li><strong>Scrip</strong> texto que se muestra en las llamadas inbound y outbound <i class="text-info">speech</i>.</li>
                                                        <li><strong>DIDś</strong> número que es asignado a la campaña <i class="text-info">1234 ó 8989 etc</i></li>
                                                            <ul>
                                                                <li>Si una campaña tiene <strong>2 o más DIDś</strong> estos se colocaran separados por una coma <i class="text-info">1234,1212</i>.</li>
                                                            </ul><br />
                                                        <li> <strong>Activa</strong> esta casilla activa o desactiva la campaña.</li>
                                                        <li>Da clic en <strong>Guardar</strong> mostrara <strong>Campaña guardada exitosamente</strong>.</li>
                                                        <li>Campaña creada, esta se mostrara en el listado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-51-modal-sm"><i>crea campaña</i></button></dl>
                                                    <li style="font-size: 15px;"> Actualizar campaña</li>
                                                    <ol>
                                                        <li>Selecciona la campaña a modificar y da clic en <strong>Editar</strong>.</li>
                                                        <li>Se despliega la ventana, realizamos los cambios necesarios.</li>
                                                        <li>Da clic en <strong>Guardar</strong> nos mostrara <strong>Campaña guardada exitosamente</strong>.</li>
                                                        <li>Campaña actualizada.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;">Atributos</li>
                                                    <ol>
                                                        <li>Selecciona la campaña y da clic en <strong>Atributos</strong>.</li>
                                                        <li><strong>Prefijo</strong> numero para identificar la llamada ó area del pais<i class="text-info"> consultar con su administrador</i>.</li>
                                                        <li><strong>Tarifa local</strong> costo en pesos por minuto de llamadas salientes locales.</li>
                                                        <li><strong>Tarifa celular</strong> costo en pesos por minuto de llamadas entrantes y salientes a celular.</li>
                                                        <li><i class="far fa-square"></i> al seleccionar el check nos permite recalcular las tarifas si el archivo de tarifas esta actualizado.<i class="text-info"> solo será visible para el perfil de <strong>Admin</strong></i>.</li>
                                                        <li>Da clic en <strong>Guardar</strong> mostrara <strong>Atributos guardados</strong>.</li>
                                                        <li>Regresara el menu de <strong>Campañas</strong>.</li><br />

                                                    </ol>
                                                </ul><br />
                                                <h4 class="text-info">Horarios</h4>
                                                <li>Al crear una campaña se establece un horario <i class="text-info">L - V de 09:00:00 - 17:59:59</i> y <i class="text-info">S y D sin horario establecido</i>.</li>
                                                <li>Este horario es necesario establecerlo adecuadamente para los reportes de <strong>SL (nivel de servicio)</strong>.</li><br />
                                                <li style="font-size: 15px;"> Horarios po campaña</li>
                                                <ol>
                                                    <li>Selecciona la campaña, da clic en <strong>Horarios</strong>.</li>
                                                    <li>Agrega el horario de inicio y fin en formato <i class="text-info">hh:mm:ss</i> 08:00:00 por día.</li>
                                                    <li>Da clic en <strong>Actualizar</strong> mostrara <strong>Horario de campaña actualizado</strong>.</li>
                                                    <li>Horario actualizada.</li>
                                                    <li>Regresara el menu de <strong>Campañas</strong>.</li>
                                                </ol>
                                                <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-52-modal-sm"><i>horario campaña</i></button></dl>
                                            </ul>
                                            <div class="modal fade bd-51-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/creacampana.png"); ?>" alt="crea" height="130" width="850">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-52-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/horacampaña.png"); ?>" alt="confcalendar" height="400" width="450">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon campañas-->
                                    <div class="collapse" id="collapseDespachador" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Despachador</h4>
                                            <ul>
                                                <li>Para crear un despachador se hará por etapas: <strong>crear, carga, revisar y concluir</strong>.</li>
                                                <li>Creado el despachador mostraremos los ajustes antes de iniciar, asi como la asignacion de usuarios (operadores).</li>
                                                <li>Por ultimo mostraremos el funcionamiento una vez iniciado.</li>
                                                <li>Es de importancia seguir cada paso para tener un despachador optimo y funcionanado correctamente.</li> <br />
                                                <ul>
                                                    <li style="font-size: 15px;">Crear despachador</li>
                                                    <ol>
                                                        <li>Despliega el combo y selecciona la <strong>Campaña</strong>.</li>
                                                        <li>Asigna el nombre a ese despachador.</li>
                                                        <li>Da clic en <strong>Nuevo</strong> mostrara <strong>Campaña despachador agregada con éxito.</strong> </li>
                                                        <li>Despachador creado, este se mostrara del lado izquierdo.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-53-modal-sm"><i>crea despachador</i></button></dl>
                                                    <li style="font-size: 15px;">Cargar base de datos</li>
                                                    <ol>
                                                        <li>Da clic sobre <i class="text-info">el nombre que asignaste al despachador</i> para seleccionarlo.</li>
                                                        <li>Antes de iniciar la carga de base debemos tomar en cuenta los siguientes requerimientos.</li>
                                                        <ul>
                                                            <li>El archivo deberá estar separado por comas (<strong class="text-info"> , </strong>).</li>
                                                            <li>NO debe contener comillas simples (<strong class="text-info"> ' </strong>), ni bobles (<strong class="text-info"> " </strong>) en los valores, pero las puede tener como delimitador de campo.</li>
                                                            <li>Los encabezados NO deben contener caracteres especiales <strong class="text-info"> " ! # $ % & ' ( ) * + , - . / </strong>.</li>
                                                            <li><strong>NO</strong> deberá contener ningún encabezado con la palabra reservada <strong class="text-info">id</strong>.</li>
                                                            <li>Deberá tener un encabezado con el nombre de las columnas como quieres que se vean en el formulario</li>
                                                            <li>Deberá tener forzosamente una columna con nombre <strong class="text-info">Teléfono</strong>.</li>
                                                            <li>Los datos deben estar homologados <strong>(tener el mismo formato)</strong> para ser reconocidos.</li>
                                                            <li>Los campos de fecha deberán tener el formato <strong class="text-info">"AAAA-mm-dd H-m-s"</strong>.</li>
                                                            <li>Los campos con <strong>valor (cantidades)</strong> deberán tener el formato como <strong class="text-info">número standar</strong>.</li>
                                                            <li>El largo máximo de caracteres por campo es de <strong>255</strong>.</li>
                                                            <li>Guardar el documento con extension <strong class="text-info">.csv</strong></li>
                                                        </ul><br />
                                                        <li>Da clic en <strong>Seleccionar archivo</strong>.</li>
                                                        <li>Buscamos el archivo .csv a cargar y da clic en <strong>Abrir</strong></li>
                                                        <li>Seleccionado el archivo da clic en <strong>Enviar</strong> mostrara <strong>Archivo subido con éxito</strong>.</li>
                                                    </ol><br />
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-54-modal-sm"><i>agrega csv</i></button></dl>
                                                    <li style="font-size: 15px;">Revisión de campos</li>
                                                    <ol>
                                                        <li>Seguido del clic <strong>Enviar</strong> mostrara en pantalla los campos que fueron agregados en forma de lista.</li>
                                                        <li>Revisaremos la funcionalidad de cada uno, el check del campo <strong>Uso</strong> esta ectivo por <i class="text-info">default</i>.</li>
                                                            <ul>
                                                                <li><strong>Columna</strong> orden secuencial de cada columna empezando en 0.<strong class="text-danger"> no editable</strong></li>
                                                                <li><strong>Nombre</strong> como se muestra desde la base de datos por columna, <strong>asi será visualizado desde el despachador</strong>.<strong class="text-success"> editable</strong></li>
                                                                <li><strong>Tipo</strong> formato que se da a la columna (texto corto, texto largo, check, lista, opciones, fecha y hora).<strong class="text-success"> editable</strong></li>
                                                                <li><strong>Valores</strong> se agregan y se visualizan en forma de combo.<strong class="text-danger"> no editable</strong></li>
                                                                    <ul>
                                                                        <li>Cambiara a editable solo al selecionar <strong>Lista u Opción</strong> en el campo <strong>Tipo</strong></li>
                                                                        <li>Para agregar valores, serán separados por coma sin espacio <strong class="text-info"><i>dia,tarde,noche</i></strong>.</li>
                                                                    </ul><br />
                                                                <li><strong>Orden</strong> se asigna el orden de como visualizar los campos consecutivamente <strong>empezando desde 0</strong>.<strong class="text-success"> editable</strong></li>
                                                                <li><strong>Lectura</strong> marca la casilla si el campo <strong>NO</strong> sera editable solo informativo.<strong class="text-success"> editable</strong></li>
                                                                <li><strong>Requerido</strong> marcar la casilla si el campo <strong>NO</strong> debe dejarse vacio.<strong class="text-success"> editable</strong></li>
                                                                <li><strong>Usar</strong> desactiva la casilla si el campo no se usara.<strong class="text-success"> editable</strong></li>
                                                            </ul><br />
                                                        <li>Revisadas las columnas da clic en <strong>Pasar</strong> nos mostrara <strong>Se a cargado el csv</strong>.</li>
                                                        <li>Base de datos agregada.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-55-modal-sm"><i>revision</i></button></dl>
                                                    <li style="font-size: 15px;">Concluir</li>
                                                    <ul>
                                                        <li>Mostrara opciones como indicador, configuración, condiciones, campos cargados, tipificaciones y usuarios.</li>
                                                        <ul>
                                                            <li><strong>Indicador</strong> muestra nombre del despachador, no. registros <i class="text-info">(todos)</i>, nuevos <i class="text-info">(sin tocar)</i>, parciales <i class="text-info">(visualizados)</i> y finalizados <i class="text-info">(terminados)</i>.</li>
                                                            <li><strong>Configuración</strong> es el tipo de despachador a usar <i class="text-info">manual, progresivo, predictivo y predictivoAMD</i>.</li>
                                                            <li><strong>Condiciones</strong> permite filtrar tipificación o campo especifico de la base en horario especifico <i class="text-info">opcional</i>.</li>
                                                            <li><strong>Campos Base</strong> los agregados desde la base y dejamos en check <strong> activo en el campo Uso</strong>.</li>
                                                            <li><strong>Tipificaciones X Llamada</strong> como serán catalogadas las llamada<i class="text-info"> podemos dejar tipificaciones <strong>simples o con subniveles hasta 4 subniveles</strong></i>.</li>
                                                            <li><strong>Agentes</strong> son los operadores activos en campaña para asignar a un despachador.</li>

                                                        </ul>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-56-modal-sm"><i>concluir</i></button></dl>
                                                </ul>
                                            </ul>
                                            <h4 class="text-info">Ajustes</h4>
                                            <ul>
                                                <li>Finalizadas la etapas del despachador revisaremos los ajustes antes de iniciarlo <i class="text-info">configuración, condiciones, campo base, tipificaciones y agentes</i>.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;">Campos base</li>
                                                    <ul>
                                                        <li> Se muestran 3 botones <strong>Nuevo</strong> y por campo <strong>Modificar</strong> y <strong>Eliminar</strong>.</li><br />
                                                        <ul>
                                                            <li style="font-size: 15px;">Nuevo</li>
                                                            <ol>
                                                                <li> Da clic en <strong>Nuevo</strong> mostrara la ventana <strong>Campo de despachador</strong>.</li>
                                                                <li><strong><i>Nombre</i></strong> se asigna a la columna nueva.</li>
                                                                <li><strong><i>Tipo</i></strong> opciones del campo (texto corto, texto largo, check, lista, opciones, fecha y hora).</li>
                                                                <li><strong><i>Valores</i></strong> se agregan separados por coma y sin espacio <strong><i>dia,tarde,noche</i></strong>, unicamente en las opciones <strong>lista u opciones</strong>.</li>
                                                                <li><strong><i>Orden</i></strong> como se visualizan los campos consecutivos, iniciando con el 0.</li>
                                                                <li><strong><i>Lectura</i></strong> marca la casilla si el campo es informativo.</li>
                                                                <li><strong><i>Requerido</i></strong> marca la casilla si el campo es requerido. <i class="text-info">obligatorio</i></li>
                                                                <li><strong><i>Dependencias</i></strong> debera estar marcada la casilla <strong>NO</strong>.</li>
                                                                <li>Da clic en <strong>Guardar</strong> mostrara <strong>Se a guardado el campo</strong>.</li>
                                                                <li> Listo campo agregado.</li>
                                                            </ol>
                                                            <dl>
                                                                <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-61-modal-sm"><i>nueva</i></button>
                                                                <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-58-modal-sm"><i>campo despachador</i></button>
                                                            </dl>
                                                            <li>Modificar</li>
                                                            <ol>
                                                                <li>Da clic en <strong>Modificar</strong> del campo a realizar cambios, desplegara la ventana <strong>Campo de despachador</strong>.</li>
                                                                <li><strong><i>Nombre</i></strong> muestra el nombre asignado a ese campo <i class="text-info">se puede modificar</i>.</li>
                                                                <li><strong><i>Tipo</i></strong> opcion en la que se muestra el contenido del campo como se visualiza el texto (texto corto, texto largo, check, lista, opciones, fecha y hora).</li>
                                                                <li><strong><i>Valores</i></strong> se agregan separados por coma sin espacio <strong><i>dia,tarde,noche</i></strong>, selecionando en <strong>Tipo</strong> lista u opciones, .</li>
                                                                <li><strong><i>Orden</i></strong> nos muestra el número en el orden que fue asignado.</li>
                                                                <li><strong><i>Lectura</i></strong> marcar casilla si el campo no sera editable <i>solo informativo.</i></li>
                                                                <li><strong><i>Requerido</i></strong> marcar casilla si el campo <strong>NO</strong> debe dejarse vacio.</li>
                                                                <li>Damos clic en <strong>Guardar</strong> y nos mostrara <strong>Se a guardado el campo</strong>.</li>
                                                                <li>Listo campo actualizado.</li>
                                                            </ol>
                                                            <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-57-modal-sm"><i>campos</i></button></dl>
                                                            <li> Eliminar</li>
                                                            <ol>
                                                                <li> Seleciona el campo y damos clic en <strong>Eliminar</strong> y nos mostrara <strong>Seguro deseas borrar el regitro</strong> damos clic en <strong>Aceptar</strong></li>
                                                                <li> Nos mostrara <strong>Se a eliminado la columna de la tabla</strong>.</li>
                                                                <li> Listo columna eliminada.</li>
                                                            </ol>
                                                            <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-62-modal-sm"><i>eliminar</i></button></dl>
                                                        </ul>
                                                    </ul><br />
                                                    <span style="font-size: 15px;"> Tipificación</span>
                                                    <ul>
                                                        <li> Por default mostrara 2 campos Tipificación y Comentarios con el botón <strong>Modificar</strong> con la leyenda <i>Obligatorio</i>.</li>
                                                        <li> Tambien se visualiza un boton para agregar un campo <strong>Nuevo</strong>.</li>
                                                        <li> <i class="text-success">Agregaremos los campos para solicitar información del contacto.</i></li><br />
                                                        <ul>
                                                            <li> Nuevo</li>
                                                            <ol>
                                                                <li> Damos clic en <strong>Nuevo</strong> y no mostrara la ventana <strong>Campo de despachador</strong>.</li>
                                                                <li> <strong><i>Nombre</i></strong> se asigna a la columna nueva.</li>
                                                                <li> <strong><i>Tipo</i></strong> como se visualiza el texto (texto corto, texto largo, check, lista, opciones, fecha y hora).</li>
                                                                <li> <strong><i>Valores</i></strong> se agregan separados por coma sin espacio <strong><i>dia,tarde,noche</i></strong>, selecionando en <strong>Tipo</strong> lista u opciones, .</li>
                                                                <li> <strong><i>Orden</i></strong> se coloca el numero en el orden que desea visualizar el campo.</li>
                                                                <li> <strong><i>Lectura</i></strong> marcar casilla si el campo no sera editable <i>solo informativo.</i></li>
                                                                <li> <strong><i>Requerido</i></strong> marcar casilla si el campo <strong>NO</strong> debe dejarse vacio.</li>
                                                                <li> <strong><i>Dependencias</i></strong> debera estar marcada la casilla <strong>NO</strong>.</li>
                                                                <li> Damos clic en <strong>Guardar</strong> y nos mostrara <strong>Se a guardado el campo</strong>.</li>
                                                                <li> Listo campo agregado.</li>
                                                            </ol>
                                                            <dl>
                                                                <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-59-modal-sm"><i>nuevo</i></button>
                                                                <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-58-modal-sm"><i>campo despachador</i></button>
                                                            </dl>
                                                            <li>Modificar</li>
                                                            <ol>
                                                                <li> Damos clic en <strong>Modificar</strong> del campo a realizar cambios y no mostrara la ventana <strong>Campo de despachador</strong>.</li>
                                                                <li> <strong><i>Nombre</i></strong> si se desea se puede modificar.</li>
                                                                <li> <strong><i>Tipo</i></strong> como se visualiza el texto (texto corto, texto largo, check, lista, opciones, fecha y hora).</li>
                                                                <li> <strong><i>Valores</i></strong> se agregan separados por coma sin espacio <strong><i>dia,tarde,noche</i></strong>, selecionando en <strong>Tipo</strong> lista u opciones, .</li>
                                                                <li> <strong><i>Orden</i></strong> nos muestra el número en el orden que fue asignado.</li>
                                                                <li> <strong><i>Lectura</i></strong> marcar casilla si el campo no sera editable <i>solo informativo.</i></li>
                                                                <li> <strong><i>Requerido</i></strong> marcar casilla si el campo <strong>NO</strong> debe dejarse vacio.</li>
                                                                <li> Damos clic en <strong>Guardar</strong> y nos mostrara <strong>Se a guardado el campo</strong>.</li>
                                                                <li> Listo campo actualizado.</li>
                                                            </ol>
                                                            <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-60-modal-sm"><i>tipificación</i></button></dl>
                                                            <li> Eliminar</li>
                                                            <ol>
                                                                <li> Seleciona el campo y damos clic en <strong>Eliminar</strong> y nos mostrara <strong>Seguro deseas borrar el regitro</strong> damos clic en <strong>Aceptar</strong></li>
                                                                <li> Nos mostrara <strong>Se a eliminado la columna de la tabla</strong>.</li>
                                                                <li> Listo columna eliminada.</li>
                                                            </ol>
                                                            <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-62-modal-sm"><i>eliminar</i></button></dl>
                                                        </ul>
                                                    </ul>
                                                    <li style="font-size: 15px;"> Usuarios</li>
                                                    <ul>
                                                        <li> Se mostrara un listado con nombres de los operadores, que estan disponibles para asginalos al despachador.</li>
                                                        <li> Los operadores asginados a un despachador no estaran disponibles hasta ser desasignados.</li><br />
                                                        <ul>
                                                            <li> Asignar</li>
                                                            <ol>
                                                                <li> Damos clic en la flecha y selecciona el nombre.</li>
                                                                <li> Damos clic en <strong>Agregar</strong> y nos mostrara <strong>Usuario agregado con éxito.</strong></li>
                                                                <li> Listo usuario asignado. se mostrara en el listado.</li>
                                                            </ol>
                                                        </ul>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-63-modal-sm"><i>usuarios</i></button>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-64-modal-sm"><i>asignado</i></button>
                                                        </dl>
                                                        <ul>
                                                            <li> Desasignar</li>
                                                            <ol>
                                                                <li> Los operadores asignados tendran aun lado el boton <strong>Quitar</strong>.</li>
                                                                <li> Seleccinamos el usuario y damos clic en <strong>Quitar</strong> nos mostrara <strong>Usuario quitado con éxito</strong></li>
                                                                <li> Listo usuario desasignado, se eliminara del listado y quedara disponible.</li>
                                                            </ol>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-64-modal-sm"><i>desasignar</i></button></dl>
                                                    </ul>
                                                </ul><br />
                                            </ul>
                                            <h4 class="text-info">Funcionamiento</h4>
                                            <ul>
                                                <li> La funcionalidad se dividira en ejecutar<i>(como iniciar, detener y archivar, reactivar un despachador)</i> y operación<i>(como lo visualiza el operador)</i></li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Ejecutar</li>
                                                    <ul>
                                                        <li>Iniciar</li>
                                                        <ul>
                                                            <li> Para iniciar el despachador damos clic en <strong>Iniciar</strong> y nos mostrara <strong>Estas seguro de realizar esa acción?</strong>.</li>
                                                            <li> Damos clic en <strong>Aceptar</strong> y nos mostrara <strong>Campaña despachador iniciada</strong>.</li>
                                                            <li> Listo despachador activo nos mostrara <i>En operaciópn!</i></li>
                                                        </ul>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-66-modal-sm"><i>iniciar</i></button>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-65-modal-sm"><i>en operacion</i></button>
                                                        </dl>
                                                        <li> Detener</li>
                                                        <ul>
                                                            <li> Selecciona el despachador a detener</li>
                                                            <li> Damos clic en <strong>Detener</strong> y nos mostrara <strong>Estas seguro de detener éste despachador?</strong>.</li>
                                                            <li> Damos clic en <strong>Aceptar</strong> y nos motrara <strong>Campaña despachador detenida</strong>.</li>
                                                            <li> Listo despachador detenido <i>(una vez detenido ya no podran visualizarlo los operadores)</i>.</li>
                                                        </ul>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-68-modal-sm"><i>selecciona</i></button>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-67-modal-sm"><i>detener</i></button>
                                                        </dl>
                                                        <li> Archivar</li>
                                                        <ul>
                                                            <li> Selecciona el despachador que se archivara </li>
                                                            <li> Damos clic en <strong>Archivar</strong> y nos mostrara <strong>Estas seguro de realizar esa acción?</strong>.</li>
                                                            <li> Damos clic en <strong>Aceptar</strong> y nos mostrara <strong>Campaña despachador activada</strong>.</li>
                                                            <li> Listo despachador archivado.</li>
                                                        </ul>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-70-modal-sm"><i>selecciona</i></button>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-69-modal-sm"><i>archivar</i></button>
                                                        </dl>
                                                        <li> Reactivar</li>
                                                        <ul>
                                                            <li> Damos clic en <strong>Archivo</strong> <i>(se encuentra al final de la lista de los despachadores)</i>.</li>
                                                            <li> Se muestran los despachadores archivados, selecciona el que se va a reactivar.</li>
                                                            <li> Damos clic en <strong>Reactivar</strong>y nos mostrara <strong>Campaña despachador reactivada</strong>.</li>
                                                            <li> Listo despachador activo <i>(se mostrara nuevamente en la lista)</i></li>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-71-modal-sm"><i>archivo</i></button></dl>
                                                    </ul><br />
                                                    <li style="font-size: 15px;"> Operación</li>
                                                    <ul>
                                                        <li></li>
                                                    </ul>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-53-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/dcrear.png"); ?>" alt="dcrear" height="70" width="1040">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-54-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/agregacsv.png"); ?>" alt="agrega csv" height="400" width="750">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-55-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/revisiondesp.png"); ?>" alt="revision despachador" height="500" width="900">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-56-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/dconcluir.png"); ?>" alt="dconcluir" height="500" width="1000">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-57-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/despcampo.png"); ?>" alt="campos" height="330" width="350">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-58-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/despcamnuevo.png"); ?>" alt="campo desp" height="400" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-59-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/tipinueva.png"); ?>" alt="tipificacion nueva" height="90" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-60-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/desptipi.png"); ?>" alt="confcalendar" height="180" width="350">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-61-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/camponueva.png"); ?>" alt="campos nuevo" height="90" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-62-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/borracampo.png"); ?>" alt="campos nuevo" height="100" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-63-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/userdesp.png"); ?>" alt="usuarios" height="90" width="350">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-64-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/userasig.png"); ?>" alt="usuarios" height="150" width="350">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-65-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/enoperacion.png"); ?>" alt="usuarios" height="110" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-66-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/iniciardesp.png"); ?>" alt="usuarios" height="100" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-67-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/detenerdesp.png"); ?>" alt="usuarios" height="100" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-68-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/selectdesp.png"); ?>" alt="usuarios" height="350" width="450">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-69-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/archivardesp.png"); ?>" alt="usuarios" height="100" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-70-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/selectarchivar.png"); ?>" alt="usuarios" height="400" width="600">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-71-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/reactivardesp.png"); ?>" alt="usuarios" height="300" width="600">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon despachador-->
                                    <div class="collapse" id="collapseEmail" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Email</h4>
                                            <ul>
                                                <li style="font-size: 15px;"> Email</li>
                                                <ul>
                                                    <li> Para el envío y recepción de corre electrónico se debe realizar la configuración correspondiente (POP) y (SMTP).</li>
                                                    <li> Se puedes realizar la configuración de cualquier correo electrónico que nos proporcione los parámetros del servidor.</li><br />
                                                    <ul>
                                                        <li style="font-size: 14px;">POP</li>
                                                        <ul>
                                                            <li> <i class="text-info">server</i> dirección de servidor correo entrante <i class="text-info">pop.secureserver.net</i></li>
                                                            <li> <i class="text-info">puerto</i> no de puerto correo entrante <i class="text-info">110</i></li>
                                                            <li> <i class="text-info">tipo</i> seleccionar tipo de correo entrante <i class="text-info">pop ó imap</i>.</li>
                                                            <li> <i class="text-info">seguridad</i></li>
                                                            <li> <i class="text-info">user</i> nombre de usuario asignado al email</li>
                                                            <li> <i class="text-info">pass</i> password asignado al email</li>
                                                        </ul><br />
                                                        <li style="font-size: 14px;">Smtp</li>
                                                        <ul>
                                                            <li> <i class="text-info">activo</i> colocar <strong>1</strong> para recibir email. </li>
                                                            <li> <i class="text-info">server</i> dirección de servidor correo saliente <i class="text-info">smtpout.secureserver.net</i></li>
                                                            <li> <i class="text-info">puerto</i> no de puerto correo saliente <i class="text-info">80</i></li>
                                                            <li> <i class="text-info">user</i> nombre de usuario asignado al email</li>
                                                            <li> <i class="text-info">pass</i> password asignado al email</li>
                                                            <li> <i class="text-info">email</i> dirección de email que se deja configurado.</li>
                                                            <li> <i class="text-info">nombre</i> que nombre aparecera al enviar un email.</li>
                                                        </ul>
                                                    </ul><br />
                                                    <span> La configuración de cada email dependera de los parámetros que maneje cada proveedor.</span>
                                                </ul>
                                                <dl>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-31-modal-sm"><i>email pop</i></button>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-32-modal-sm"><i>email smtp</i></button>
                                                </dl><br />
                                            </ul>
                                            <div class="modal fade bd-31-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailpop.png"); ?>" alt="email pop" height="280" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-32-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/emailsmtp.png"); ?>" alt="email smtp" height="320" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="collapse" id="collapseFormulario" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Formulario</h4>
                                            <ul>
                                                <li> Un formulario nos permite recabar información de llamadas inbound u outbound.</li>
                                                <li> Para crear una formulario previamente debemos haber creado una campaña, revisar el apartado <strong class="text-warning">configuración / campañas</strong>.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Crear</li>
                                                    <ol>
                                                        <li> <strong>Campaña</strong> selecciona la campaña para asignar el formulario.</li>
                                                        <li> <strong>Nombre</strong> ingresa el nombre del formulario para esa campaña.</li>
                                                        <li> <strong>Para salientes</strong> solo si la campaña es outbound haz clic en el check. </li>
                                                        <li> <strong>Activo</strong> el check por default estara activado, <i>solo podra desactivarse una vez creado el formulario</i>.</li>
                                                        <li> Damos clic en <strong>Crear</strong>y mostrara <strong>Formulario creado con éxito</strong>.</li>
                                                        <li> Listo formulario creado y se mostrara en el listado.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-72-modal-sm"><i>crea formulario</i></button></dl>
                                                    <li style="font-size: 15px;"> Actualizar</li>
                                                    <ol>
                                                        <li> Selecciona el formulario y modifica campaña, nombre, check de salientes y/o check de activo.</li>
                                                        <li> Damos clic en <strong>Actualizar</strong> y nos mostrara <strong>Formulario actualizado con éxito</strong>.</li>
                                                        <li> Listo formulario actualizado.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Agregar campos</li>
                                                    <ol>
                                                        <li> Identificamos el formulario y damos clic en <strong>Campos</strong>, <i>agregaremos los campos para el formulario</i>.</li>
                                                        <li> <strong><i>Tipo</i></strong> como se visualiza el texto (texto corto, texto largo, check, lista, opciones).</li>
                                                        <li> <strong><i>Nombre</i></strong> se asigna a la columna nueva.</li>
                                                        <li> <strong><i>Valores</i></strong> se agregan separados por coma sin espacio <strong><i>dia,tarde,noche</i></strong>, selecionando en <strong>Tipo</strong> lista u opciones.</li>
                                                        <li><strong><i>Dependencias</i></strong> por default estara en <strong>NO</strong>. <i>para usar las dependencias, consulte a su administrador.</i></li>
                                                        <li> <strong><i>Requerido</i></strong> marcar el check si el campo <strong>NO</strong> debe dejarse vacio.</li>
                                                        <li> <strong><i>Orden</i></strong> se coloca el numero en el orden que desea visualizar el campo. <i>se inicia desde 0.</i></li>
                                                        <li> Damos clic en <strong>Agregar</strong> y nos mostrara <strong>Campo agregado con éxito.</strong>.</li>
                                                        <li> Listo campo agregado se mostrara en la lista.</li>
                                                    </ol>
                                                    <dl>
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-73-modal-sm"><i>identificar formulario</i></button>
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-74-modal-sm"><i>crea pregunta</i></button>
                                                    </dl>
                                                    <li style="font-size: 15px;"> Actualizar campos</li>
                                                    <ol>
                                                        <li> Selecciona el campo y modifica tipo, nombre, valores, dependencia, orden y/o check de requerido.</li>
                                                        <li> Damos clic en <strong> Actualizar</strong>y nos mostrara <strong>Campo actualizado con exito</strong>.</li>
                                                        <li> Listo campo actualizado.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Borrar campos</li>
                                                    <ol>
                                                        <li> Selecciona el campo a eliminar.</li>
                                                        <li> Damos clic en <strong>Borrar</strong> y nos mostrara <strong>Campo eliminado con éxito</strong>.</li>
                                                        <li> Listo campo eliminado.</li>
                                                    </ol><br />
                                                    <dl><strong><i class="text-danger">importante!!</i></strong> Si borras algún campos se perdera la informacion del mismo.</dl>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-74-modal-sm"><i>actualiza y borra</i></button></dl>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-72-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/creaform.png"); ?>" alt="creaform" height="130" width="750">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-73-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/identform.png"); ?>" alt="confcalendar" height="480" width="850">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-74-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/campoform.png"); ?>" alt="confcalendar" height="300" width="800">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon formulario-->
                                    <div class="collapse" id="collapseGenerales" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Generales</h4>
                                            <ul>
                                                <li> En este módulo se realizan las configuraciones de <strong> Consola, Twitter, Sistema, SMS y Facebook</strong> dependiendo la necesidad o utiliadad.</li>
                                                <li> Mostraremos a detalle que debe llevar en cada seccion y campo.</li>
                                                <li> Cada que se modifique un campo no importanto la sección, debera dar clic en <strong>Guardar</strong>.</li>
                                                <li> Si no puedes realizar cambios revisa el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">Configuración / Usuarios / Permisos</strong>.</li><br />
                                                <ul>
                                                    <li style="font-size: 18px; color: #17a2b8;"> Consola</li><br />
                                                    <ul>
                                                        <li><i class="text-info">chartUrl</i> url del servidor que provee el servicio de chat. <i class="text-info">https://chat.enlinea.com/conversar/net</i></li>
                                                        <li><i class="text-info">vcServer</i> url del servidor que provee el servicio de videoconferencia. <i class="text-info">https://videoconf.video.com/conferencia/net</i></li>
                                                        <li><i class="text-info">vcRoom</i> nombre único que se da a la sala de videoconferencia dentro del sistema.</li>
                                                        <li><i class="text-info">acw</i> es el tiempo que se proporciona despues de llamada.</li>
                                                            <ul>
                                                                <li>Si no deseampos este tiempo solo colocamos un <i class="text-info">0</i>.</li>
                                                                <li>Si requerimos esta opcion infinita<strong> el agente termina este tiempo cuando lo desee</strong> colocaremos un <i class="text-info">1</i>.</li>
                                                                <li>Si requieren tiempo especifico multiplicamos <strong>no min x 60 segundos </strong>ejemplo: <i class="text-info">5 minutos x 60 segundos = 300 segundos</i> <strong>300</strong> es el dato a colocar.</li>
                                                            </ul><br />
                                                        <li><i class="text-info">extraUrl</i> sección para incrustrar una página en consola.</li>
                                                        <li><i class="text-info">extraUrl2</i> 2da sección para incrustar una página en consola.</li>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-33-modal-sm"><i>parámetros consola</i></button></dl><br />
                                                    <li style="font-size: 18px; color: #17a2b8;"> Twitter</li><br />
                                                        <ul>
                                                            <li> Antes de iniciar con la configuración se debe contar con los accesos de Token proporcionados por Twitter.</li>
                                                            <li> Configuración Keys and tokens</li><br />
                                                            <ul>
                                                            <li> Access token & access token secret</li>
                                                                <ul>
                                                                    <li><i class="text-info"> usuario</i> nombre del usuario con el que se dio de alta en twitter.</li>
                                                                    <li><i class="text-info"> oauth_access_token</i> se agrega el (Access token)</li>
                                                                    <li><i class="text-info"> oauth_access_token_secret</i> se agrega el (Access token secret)</li>
                                                                </ul><br />
                                                            <li> Consumer API keys</li>
                                                                <ul>
                                                                    <li><i class="text-info"> consumer_key</i> se agrega la (API key)</li>
                                                                    <li><i class="text-info"> consumer_secret</i> se agrega la (API secret key)</li>
                                                                </ul><br />
                                                            </ul>
                                                        </ul>
                                                    <li> No cuentas con estos accesos, visita el link para mayor información. <i class="text-info">https://developer.twitter.com/en/docs/basics/authentication/guides/access-tokens.html</i></li>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-28-modal-sm"><i>parámetros twitter</i></button></dl><br />
                                                    <li style="font-size: 18px; color: #17a2b8;">Sms</li><br />
                                                        <ul>
                                                            <li><i class="text-info">server</i> url del servidor que provee el servicio de SMS. <i class="text-info">https://sms.envia.com/enviado/enviando</i></li>
                                                            <li><i class="text-info">user</i> nombre del usuario asignado por el proveedor</li>
                                                            <li><i class="text-info">pass</i> password asignada por el proveedor</li>
                                                        </ul>
                                                        <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-30-modal-sm"><i>parámetros sms</i></button></dl><br />
                                                    <li style="font-size: 18px; color: #17a2b8;">Sistema</li><br />
                                                        <ul>
                                                        <li><i class="text-info">Formato fechas</i> nos permite elegir mediante un select variantes de fecha.</li>
                                                        <li> De esto depende la manera como se mostraran las fechas en el sistema.</li>
                                                            <ul>
                                                                <li><strong>Opción 1</strong> Día / Mes / Año</li>
                                                                <li><strong>Opción 2</strong> Mes / Día / Año</li>
                                                                <li><strong>Opción 3</strong> Año / Mes / Día</li>
                                                            </ul><br/ >
                                                        <li> El formato de hora no cambia, permanece igual para todos <strong>Hora / Minuto / Segundo</strong>.</li>
                                                        </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-29-modal-sm"><i>formato fecha</i></button></dl><br />
                                                    <li style="font-size: 18px; color: #17a2b8;">Facebook</li><br />
                                                    <ul>
                                                        <li> Antes de iniciar con la configuración debera contar con los accesos de API proporcionados por Facebook. </li>
                                                        <li> Configuración access token y api</li><br />
                                                        <ul>
                                                            <li> <i class="text-info"> accessToken</i> es el token de acceso.</li>
                                                            <li> <i class="text-info"> app_id</i> identificador de la API.</li>
                                                            <li> <i class="text-info"> app_secret</i> se agrega clave secreta de la API.</li>
                                                            <li> <i class="text-info"> admin_id</i> identificador del perfil ó página.</li>
                                                        </ul><br />
                                                    </ul>
                                                    <li> No cuentas con estos accesos, visita el link para mayor información. <i class="text-info">https://developers.facebook.com/docs/apis-and-sdks?locale=es_ES</i></li>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-123-modal-lg"><i>parámetros facebook</i></button></dl>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-28-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/twitter.png"); ?>" alt="twitter sistema" height="280" width="500">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-29-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/cformfecha.png"); ?>" alt="cformfecha" height="120" width="500">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-30-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/smssis.png"); ?>" alt="sms sistema" height="210" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-33-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/consolasistema.png"); ?>" alt="consola sistema" height="280" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-123-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/facebook.png"); ?>" alt="twitter sistema" height="200" width="950">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon generales-->
                                    <div class="collapse" id="collapseSMS" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <ul>
                                                <h4 class="text-info">SMS</h4>
                                                <li> Este módulo también es para el envío de SMS, con la diferencia que se puede usar una base de datos y hacerlo de forma masiva.</li>
                                                <li> Para el envio se debe preparar el archivo.csv, agregar valores por default, cargar el archivo.csv y crearlo.</li>
                                                <li> Creado el SMS pasara al módulo de status, donde se ejecutara y visualizara antes y despues de envío del mensaje.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Preparación y configuración</li>
                                                    <ul>
                                                        <li> Preparar archivo.csv </li>
                                                        <ul>
                                                            <li> Da clic en el botón <strong>Descargar formato </strong>formatoCampSMS.csv</li>
                                                            <li> Debe tener <span class="text-success">obligatorio teléfono a 10 digitos,</span><span class="text-info"> nombre, dato, saludo, mensaje y cierre</span> en ese orden.</li>
                                                            <li> El tamaño total del mensaje deberá ser de 250 caracteres o menos, incluyendo nombre y dato.</li>
                                                            <li> Llena los campos como se muestran en el archivo sin sobrepasar de 250 caracteres incluyendo nombre y dato.</li>
                                                            <li> Da clic en guardar y este lo hara en formato .csv</li>
                                                            <li> Listo archivo preparado</li>
                                                        </ul>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-125-modal-lg"><i>descarga formato</i></button>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-126-modal-sm"><i>formato</i></button>
                                                        </dl>
                                                        <li> Valores por default</li>
                                                        <ol>
                                                            <li> Si saludo, mensaje y cierre del archivo están vacías se usaran los valores default del formulario para completar el registro.</li>
                                                            <li> <span class="text-info">Nombre de campaña</span> con el cual podra visualizarse en la sección de status.</li>
                                                            <li> <span class="text-info">Saludos</span> forma inicial del mensaje ejemplo: <span class="text-info">hola, buenos días etc</span>.</li>
                                                            <li> <span class="text-info">Mensaje</span> cuerpo del mensaje.</li>
                                                            <li> <span class="text-info">Cierre</span> despedida ejemplo: <span class="text-info">buenas tardes, buen día, etc</span>.</li>
                                                        </ol>
                                                        <dl>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-127-modal-lg"><i>valores default</i></button>
                                                        </dl>                                                    <li> Cargar archivo</li>
                                                        <ol>
                                                            <li> Da clic en <strong>Seleccionar archivo</strong></li>
                                                            <li> Busca el archivo formatoCampSMS.csv que se preparo con anticipación.</li>
                                                            <li> Da clic en agregar o aceptar segun sea el caso</li>
                                                            <li> Listo archivo cargado</li>
                                                        </ol><br />
                                                        <li> Crear SMS</li>
                                                        <ol>
                                                            <li> Da clic en <strong>Crear</strong></li>
                                                            <li> SMS creado y listo para enviar</li>
                                                        </ol>
                                                    </ul><br />
                                                    <li style="font-size: 15px;"> Status y ejecución</li>
                                                    <ul>
                                                        <li> Status antes de envío</li>
                                                        <ul>
                                                            <li> Una vez creado el sms lo podremos visualizar en la tabla de status en Campañas</li>
                                                            <li> Lo buscamos con el nombre de campaña que le asignamos y mostrara el no de registos y sms por enviar.</li>
                                                            <li> Al final del regsitro tendremos el botón de <strong>Enviar</strong> activado.</li>
                                                        </ul>
                                                    </ul>
                                                </ul>
                                                <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-124-modal-sm"><i>sms</i></button></dl>
                                            </ul>
                                            <div class="modal fade bd-124-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/tipolicencia.png"); ?>" alt="creaform" height="400" width="450">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-125-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/descargaformato.png"); ?>" alt="descarga formato" height="150" width="800">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-126-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/formatosms.png"); ?>" alt="formato" height="150" width="500">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-127-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/defaultsms.png"); ?>" alt="formato" height="200" width="850">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon licencia-->
                                    <div class="collapse" id="collapseUsuarios" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Usuarios</h4>
                                            <ul>
                                                <li> Como en todo sistema debemos crear un usuario y contraseña para accesar a este.</li>
                                                <li> Este módulo permite buscar, crear, actualizar y cerrar sesión de usuarios, así como otorgar permisos de acuerdo al perfil asignado.</li>
                                                <li> Cada usuario que sea dado de alta como agente u operador, debera contar con un numero de extensión esto para el uso de toda la seccion de consola.</li>
                                                <li> Para la creación de extensiones, revisar el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">configuración / colas</strong></li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Buscar</li>
                                                    <ol>
                                                        <li> Para realizar una búsqueda, ingresamos algunos de los siguientes parámetros <strong>email, nombre, apellido o extensión</strong>.</li>
                                                        <li> Damos clic en <strong>Buscar</strong>.</li>
                                                        <li> Se mostraran los registros que coincidan con ese parámetro bajo el buscador.</li>
                                                    </ol>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-76-modal-sm"><i>buscar usuarios</i></button></dl>
                                                    <li style="font-size: 15px;"> Crear</li>
                                                    <ol>
                                                        <li> <strong>Email</strong> con el cual ingresaremos al sistema, ejemplo: <span class="text-info">usuario1@prueba.com</span></li>
                                                        <li> <strong>Clave</strong> password que usaremos para ingresar al sistema, alfanumerico: <span class="text-info">pru3b4</span></li>
                                                        <li> <strong>Nombre(s)</strong> del usuario.</li>
                                                        <li> <strong>Apellido(s)</strong> completos del usuario.</li>
                                                        <li> <strong>Extensión</strong> número asignado que consta de 4 digitos, ejemplo <span class="text-info">1000, 1020, 1012 etc.</span></li>
                                                        <li> <strong>Activo</strong> el check por default estara activo <span class="text-info">podrá desactivarse ya creado el usuario</span>.</li>
                                                        <li> Damos clic en <strong>Crear</strong> nos mostrara <strong>Usuario creado con éxito.</strong></li>
                                                        <li> Listo usuario creado y se mostrara en el listado.</li>
                                                    </ol>
                                                    <li> Al crear un usuario se mostrara esta imagen <span style="color: #656262;"><i class="fas fa-circle"></i></span> en <strong>Online</strong>, indicando que <strong>NO</strong> a iniciado sesión.</li>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-77-modal-sm"><i>crear usuario</i></button></dl>
                                                    <li style="font-size: 15px;"> Actualizar usuarios</li>
                                                    <ol>
                                                        <li> Selecciona el usuario y modifica email, clave, nombre, apellido, extension y /o check de activo</li>
                                                        <li> Damos clic en <strong>Actualizar</strong> y nos mostrara <strong>Usuario actualizado con éxito</strong>.</li>
                                                        <li> Listo usuario actualizado.</li>
                                                    </ol><br />
                                                    <li style="font-size: 15px;"> Online y offline</li>
                                                    <ul>
                                                        <li> Cuando un usuario inicia sesión el boton cambiara de color a <span style="color: #0F0;"><i class="fas fa-circle"></i></span> mostrandose en <strong>Online</strong>.</li>
                                                        <ol>
                                                            <li> Si requiere terminar sesión del usuario solo necesita dar click sobre el boton en verde.</li>
                                                            <li> Mostrara un recuadro <strong>Estás seguro?</strong>, damos clic en <strong>Aceptar</strong> y nos mostrara <strong>Usuario fue desconectado</strong>.</li>
                                                            <li> Su estado cambiara de online a offline <span style="color: #656262;"><i class="fas fa-circle"></i></span> .</li>

                                                        </ol>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-78-modal-sm"><i>online offline</i></button></dl>
                                                </ul><br />
                                                <h4 class="text-info">Permisos</h4>
                                                <li> Al crear un usuario se activan diferentes tipos de permisos como son reportes, configuración, seccion y parámetros.</li>
                                                <li> Estos estaran inabilitados, veamos el proceso para otorgar los permisos por cada sección.</li><br />
                                                <ul>
                                                    <li style="font-size: 15px;"> Permisos</li>
                                                    <ol>
                                                        <li> En esta seccion otorgaremos los permisos necesarios a los reportes, configuración y/o sección.</li>
                                                        <li> Damos clic del check&nbsp;&nbsp;<i class="far fa-square"></i>&nbsp;&nbsp;correspondiente y mostrara&nbsp;&nbsp;<i class="far fa-check-square"></i>&nbsp;&nbsp;<span class="text-info"><i>repetimos el proceso las veces necesarias</i></span>.</li>
                                                        <li> Damos clic en <strong>Guardar</strong> y nos mostrara <strong>Permisos guardados</strong>.</li>
                                                        <li> Listo permisos guardados y nos enviara a <strong>Usuarios</strong></li>
                                                    </ol><br />
                                                    <span> Para otorgar todos los permisos en reportes, configurción y/o sección damos clic en el check&nbsp;&nbsp;<strong>Todos<i class="far fa-square"></i></strong> de cada permiso.</span>
                                                    <dl></dl>
                                                    <li style="font-size: 15px;">Parámetros</li>
                                                    <ul>
                                                        <li><strong>Extensión</strong> número asignado que consta de 4 digitos, ejemplo 1000, 1020, 1012 etc.</li>
                                                        <li><strong>ID chat para autologin</strong> </li>
                                                        <li><strong>Password asterisk</strong> se deja en blanco por default. <i>para algun cambio consulta con tu administrador</i></li>
                                                        <li><strong>Servidor asterisk</strong> se deja en blanco por default. <i>para algun cambio consulta con tu administrador</i></li>
                                                        <li><strong>Tema visual</strong> nombre corto del tema ejemplo: <strong><i>default, claro, dark, ó turki</i></strong></li>
                                                        <li> Damos clic en <strong>Guardar</strong> y nos mostrara <strong>Permisos guardados</strong>.</li>
                                                        <li> Listo permisos guardados y nos enviara a <strong>Usuarios</strong></li>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-79-modal-sm"><i>permisos & parametros</i></button></dl>
                                                    <li style="font-size: 15px;"> Quitar permisos</li>
                                                    <ol>
                                                        <li> Damos clic del check&nbsp;&nbsp;<i class="far fa-check-square"></i>&nbsp;&nbsp;correspondiente y mostrara&nbsp;&nbsp;<i class="far fa-square"></i>&nbsp;&nbsp;<i>repetimos el proceso las veces necesarias</i>.</li>
                                                        <li> Damos clic en <strong>Guardar</strong> y nos mostrara <strong>Permisos guardados</strong>.</li>
                                                        <li> Listo permisos guardados y nos enviara a <strong>Usuarios</strong></li>
                                                    </ol><br />
                                                    <span> Para quitar todos los permisos en reportes, configurción y/o sección damos clic en el check&nbsp;&nbsp;<strong>Todos<i class="far fa-square"></i></strong> de cada permiso.</span>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-76-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/buscarusuario.png"); ?>" alt="crea" height="150" width="830">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-77-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/creausuario.png"); ?>" alt="confcalendar" height="120" width="800">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-78-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/sesionusuario.png"); ?>" alt="confcalendar" height="150" width="850">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-79-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/permisosuser.png"); ?>" alt="confcalendar" height="600" width="900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon usuarios-->
                                    <div class="collapse" id="collapseCWhatsapp" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                        <h4 class="text-info">Whatsapp</h4>
                                            <li> Contamos con la funcionalidad de Multicuentas de WhatsApp.</li>
                                            <li> Por seguridad, politica y buen uso de WhatsApp, el primer mensaje debera ser recibido por parte del usuario.</li>
                                            <li> Con WhatsApp podemos tener comunicación directa y en tiempo real con usuarios, para hacer uso de este modulo debemos realizar algunas configuraciones.</li><br />
                                            <ul>
                                                <li style="font-size: 15px;"> Configurar WhatsApp en equipo celular</li>
                                                <ol>
                                                    <li> Tener un número nuevo, no usar un numero de WhatsApp exisente <i class="text-info">(el nuevo número puede ser de cualquier compañia teléfonica)</i></li>
                                                    <li> Desde el equipo célular ya con el nuevo número bajar la aplicación de WhatsApp Business <i class="text-info">(no usar WhatsApp normal pueden bloquear el número)</i></li>
                                                    <li> Configurar el nuevo numero en la aplicación de WhatsApp Business <i class="text-info">(la configuración es identica que en whastapp normal)</i></li>
                                                    <li> Agrega una imagen y nombre del cliente <i class="text-info">(así los usuarios podrán visualizar al contacto como empresa)</i></li>
                                                    <li> Dentro de WhasApp Business damos clic en <strong class="text-info"><i class="fas fa-ellipsis-v"></i></strong>, <strong class="text-info">Ajustes</strong>, <strong class="text-info">Ajustes de empresa</strong>, <strong class="text-info">Enlace directo</strong> y copiamos <span class="text-info">https://wa.me/nuestro_numero</span></li>
                                                    <li> Con el enlace copiado, podremos enviar sms para tener el primer contacto por el usuario <i class="text-info"><strong>(ver envio de sms más abajo)</strong></i></li>
                                                </ol><br />
                                                <li style="font-size: 15px;"> Configurar Waboxapp & Whatsapp web</li>
                                                <ol>
                                                    <li> Ingresa a Google Chrome, copia y pega el link <i class="text-info">https://chrome.google.com/webstore/category/extensions</i></li>
                                                    <li> Dentro del store busca Waboxapp y da clic en <strong>Agregar a Chrome</strong>, saldra un cuadro de dialogo damos clic en<strong> Agregar extension</strong>.</li>
                                                    <li> Se agregara el icono <span><img src="<?php echo site_url('assets/img/imgmanual/waboxred.png'); ?>" width="18" height="18"></span> en la barra de extensiones de chrome.</li>
                                                    <li> Damos clic en el icono y mostrara <span class="text-success"> Enter your Waboxapp API key</span> damos clic.</li>
                                                    <li> Agregamos la <i class="text-success">Api key</i> y damos clic en <strong>Validate</strong>, <i class="text-info">la Api Key la proporcionara el administrador del sistema.</i></li>
                                                    <li> Mostrara un mensaje, cerramos la pagina y damos clic en <span><img src="<?php echo site_url('assets/img/imgmanual/waboxred.png'); ?>" width="18" height="18"></span></li>
                                                    <li> Desplegara <span class="text-success">Go to web.whatsapp.com</span> damos clic</li>
                                                    <li> Cambiara el icono a <span><img src="<?php echo site_url('assets/img/imgmanual/wabox.png'); ?>" width="18" height="18"></span> y mostrara la pagina de WhatsApp Web.</li>
                                                    <li> Escaneamos el codigo QR con el telefono configurado en WhatsApp Business.</li>
                                                    <li> Hemos configurado Waboxapp y Whatsapp web.</li><br />
                                                    <span><i> Este procedimiento debera ser realizado en el equipo donde estara el supervisor <strong>NO</strong> los operadores.</i></span><br>
                                                </ol>
                                                <dl>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-117-modal-lg">store waboxapp</button>
                                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-118-modal-sm">agrega extensión</button>
                                                </dl>
                                                <li style="font-size: 15px;"> Envio de Sms y creación de plantilla</li>
                                                <ol>
                                                    <li> Una vez que tenemos el link de WhasApp <span class="text-info">https://wa.me/nuestro_numero</span> podemos generar los sms.</li>
                                                    <li> Damos clic en <strong>Consola</strong> y luego en el modúlo <i class="far fa-comment-dots"></i> SMS</li>
                                                    <li> Se crea una plantilla con el texto que deseemos y el link <span class="text-info">https://wa.me/nuestro_numero</span>.</li>
                                                    <li> Enviamos los sms a los usuarios.</li><br />
                                                    <span><i> Para enviar un SMS y crear plantillas revisar el apartado del manual <strong style="color: #17a2b8; text-shadow: 1px 1px 1px #000000;"> Consola / SMS</strong></i></span>
                                                </ol><br />
                                            </ul>
                                            <div class="modal fade bd-117-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/store_wabox.png"); ?>" alt="store wabox" height="250" width="900">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-118-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/mensaje_instalar.png"); ?>" alt="store wabox" height="180" width="400">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon Whatsapp-->
                                    <div class="collapse" id="collapseEmergencia" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Emergencia</h4>
                                            <ul>
                                                <li> Es importante destacar que solo se hara uso en caso de <strong>error de Web Socket</strong></li>
                                                <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-36-modal-sm"><i>error websocket</i></button></dl>
                                                <ul>
                                                    <li> Botón de emergencia</li>
                                                    <ul>
                                                        <li> Se reiniciara el servicio de conmutador (Asterisk).</li>
                                                        <li> Se cortara cualquier llamada en curso.</li>
                                                        <li> Se cerraran todas las sesiones de usuario activas.</li>
                                                        <li> Por seguridad se guardaran los datos de quien reinicia junto con la hora y fecha.</li>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-34-modal-sm"><i>botón emergencia</i></button></dl>
                                                    <li> Resultado después de activar el botón de emergencia</li>
                                                    <ul>
                                                        <li> Iniciando el reincio de sistema aparecera este icono, <i class="fas fa-spinner fa-1x fa-spin"></i><i> evento en proceso.</i></li>
                                                        <li> Se desplegara un recuadro indicando los procesos realizados en el sistema.</li>
                                                        <li> Listo sistema en linea.</li>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-35-modal-sm"><i>respuesta botón</i></button></dl>
                                                </ul>
                                                <li> No tienes acceso a este modulo revisa el apartado de <strong style="color: #ffc107; text-shadow: 1px 1px 1px #000000;">configuración / usuarios / permisos</strong>.</li><br />
                                            </ul>
                                            <div class="modal fade bd-34-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/botonemergencia.png"); ?>" alt="boton emergencia" height="350" width="800">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-35-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/respboton.png"); ?>" alt="respuesta boton" height="550" width="800">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade bd-36-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/websocket.png"); ?>" alt="websocket" height="240" width="300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accodeon emergencia-->
                                    <div class="collapse" id="collapseLicencia" data-parent="#accorConfig">
                                        <div class="card card-body parrafo-imagen">
                                            <h4 class="text-info">Licencia</h4>
                                            <ul>
                                                <ul>
                                                    <li> Este apartado muestra el tipo de licencia que tenemos para <strong><span style="color: #9fbc49;">Assertive</span></strong> (
                                                    <span style="color: #000000; text-shadow: 2px 2px 10px #ffffff;">Suite</span>,&nbsp;
                                                    <span style="color: #999999; text-shadow: 2px 2px 10px #b3b3b3;">Plus </span> o&nbsp;
                                                    <span style="color: #ffffff; font-weight: bold; text-shadow: 2px 2px 10px #000;">Premium</span>&nbsp;) y nos despliega la siguiente información.</li>
                                                    <!-- <li> Dentro de la información nos mostrara: expiración, cantidad de usuarios, usuarios actualmente en sistema, cuantos disponibles y la version.</li><br /> -->
                                                    <ul>
                                                        <li> Expiracion ó vigencia.</li>
                                                        <li> Cantidad de usuarios por licencia. </li>
                                                        <li> Cuantos estan en sistema actualmente.</li>
                                                        <li> Versión.</li>
                                                    </ul>
                                                    <dl><button type="button" class="btn btn-link" data-toggle="modal" data-target=".bd-75-modal-sm"><i>licencia</i></button></dl>
                                                </ul>
                                            </ul>
                                            <div class="modal fade bd-75-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <img src="<?php echo site_url("assets/img/imgmanual/tipolicencia.png"); ?>" alt="creaform" height="400" width="450">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- cierre accordeon licencia-->
                                </div><!-- cierre acordeon configuración -->
                            </div><!-- cierre menu botones configuración-->
                        </div> <!-- cierre card configuración-->
                        <div class="card">
                            <div class="card-header nopadding" id="heading003">
                                <h3 class="mb-0">
                                    <button class="btn btn-info btn-lg btn-block collapsed" type="button" data-toggle="collapse" data-target="#collapse003" aria-expanded="false" aria-controls="collapse003">
                                        <i class="far fa-edit"></i>&nbsp;&nbsp;Reportes
                                    </button>
                                </h3>
                            </div>
                            <div id="collapse003" class="collapse" aria-labelledby="heading003" data-parent="#accorManual">
                                <div class="accordion" id="accorReportes">
                                    <div class="card card-body parrafo-imagen">
                                        <h4 class="text-info">Lista de reportes</h4>
                                        <div class="table table-striped parrafo-imagen">
                                            <div class="table-header-group">
                                                <div class="table-cell">Reporte</div>
                                                <div class="table-cell">Descripción</div>
                                                <div class="table-cell">Filtros</div>
                                                <div class="table-cell"><span> <img src="<?php echo site_url('assets/img/excel5.png'); ?>" width="22" height="22"></span></div>
                                                <div class="table-cell"><span><img src="<?php echo site_url('assets/img/pdf4.png'); ?>" width="18" height="18"></span></div>
                                                <div class="table-cell">Preview</div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Buzón de voz</div>
                                                <div class="table-cell">Llamadas canalizadas a un buzón de voz</div>
                                                <div class="table-cell">fecha y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-80-modal-lg"><i class="fas fa-search"></i></button></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Calidad</div>
                                                <div class="table-cell">Llamadas evaluadas por los auditores</div>
                                                <div class="table-cell">fecha, nombre evaluación, tipo de llamada y agente.</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-81-modal-lg"><i class="fas fa-search"></i></button></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Formulario</div>
                                                <div class="table-cell">Respuestas de los formularios por llamada</div>
                                                <div class="table-cell">fecha y formulario</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-82-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Sms detalle</div>
                                                <div class="table-cell">Detalle por dia de los sms enviados</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-83-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Sms indicador</div>
                                                <div class="table-cell">Por día muestra los sms enviados y recibidos con total</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-84-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">eMail detalle</div>
                                                <div class="table-cell">Detalle por dia de los email enviados</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-85-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">eMail indicador</div>
                                                <div class="table-cell">Por día muestra los email enviados y respondidos así como un total</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-86-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Inbound</div>
                                                <div class="table-cell">Llamadas entrantes por día a detalle</div>
                                                <div class="table-cell">fecha, campaña, agente, llamadas y calidad</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-87-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Outbound</div>
                                                <div class="table-cell">Llamadas salientes por día a detalle</div>
                                                <div class="table-cell">fecha, campaña, agente, llamadas y calidad</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-88-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Por campaña</div>
                                                <div class="table-cell">Gráfica las llamadas exitosas y abandonadas agrupadas por campaña</div>
                                                <div class="table-cell">fecha</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-89-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Llamadas exitosas</div>
                                                <div class="table-cell">Llamadas exitosas y abandonadas agrupadas por campaña</div>
                                                <div class="table-cell">fecha, tipo de llamada y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-90-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Encuestas</div>
                                                <div class="table-cell">Encuestas respondidas antes del ivr</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-91-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Tiempo de espera</div>
                                                <div class="table-cell">Llamadas agrupadas por campaña y rango de tiempo con totales</div>
                                                <div class="table-cell">fecha</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-92-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Opciones IVR</div>
                                                <div class="table-cell">Llamadas con tipo de opcion en el IVR</div>
                                                <div class="table-cell">fecha</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-93-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Abandonos</div>
                                                <div class="table-cell">Llamadas entrantes y saliente abandonadas por día</div>
                                                <div class="table-cell">fecha, campaña y llamadas</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-94-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Abandonadas c/30 min</div>
                                                <div class="table-cell">Total de llamadas abandonadas por día cada 30 minutos</div>
                                                <div class="table-cell">fecha y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-95-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Atendidas c/30 min</div>
                                                <div class="table-cell">Total de llamadas atendidas por día cada 30 minutos</div>
                                                <div class="table-cell">fecha y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-96-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Comparativo c/30 min</div>
                                                <div class="table-cell">Grafica las llamadas atendidas vs abandono por día cada 30 minutos</div>
                                                <div class="table-cell">fecha y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-97-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Histórico</div>
                                                <div class="table-cell">Grafica el historico de llamadas inbound, outbound y las atendidas, abandono por día</div>
                                                <div class="table-cell">fecha y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-98-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">ACW</div>
                                                <div class="table-cell">Actividad despues de llamada por agente</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-99-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Descansos</div>
                                                <div class="table-cell">Status de actividad del agente en tiempo por día</div>
                                                <div class="table-cell">fecha y campaña</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-100-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Distribución de llamadas</div>
                                                <div class="table-cell">Llamadas agrupadas por agente con tiempo y estatus</div>
                                                <div class="table-cell">fecha, tipo llamada, campaña y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-101-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Tiempo de sesión</div>
                                                <div class="table-cell">Status en la duración de sesión por agente por día</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-102-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Distribución gráfico</div>
                                                <div class="table-cell">Grafica por agente el total de llamadas</div>
                                                <div class="table-cell">fecha y agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-103-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Acumulado diario</div>
                                                <div class="table-cell">Porcentajes de nivel de servicio diario y totales</div>
                                                <div class="table-cell">fecha</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-104-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Acumulado mensual</div>
                                                <div class="table-cell">Porcentajes de nivel de servicio por mensual, campaña y totales</div>
                                                <div class="table-cell">fecha</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-105-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Nivel de servicio inbound</div>
                                                <div class="table-cell">Nivel de servicio por dia c/30 min en llamadas inbound y totales</div>
                                                <div class="table-cell">fecha, campaña, agente y horario</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-106-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Nivel de servicio outbound</div>
                                                <div class="table-cell">Nivel de servicio por dia c/30 min en llamadas outbound y totales</div>
                                                <div class="table-cell">fecha, campaña y horario</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-107-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Nivel de servicio gráfica</div>
                                                <div class="table-cell">Grafico nivel de servicio por dia c/30 min en llamadas inbound</div>
                                                <div class="table-cell">fecha, campaña y horario</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-108-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Despachador detalle</div>
                                                <div class="table-cell">Muestra todos los registros de la base con su status, no despliegues y llamadas</div>
                                                <div class="table-cell">despachador</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-109-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Despachador llamadas</div>
                                                <div class="table-cell">Muestra los registros de la base tipificados con su status</div>
                                                <div class="table-cell">fecha, despachador, agente y tipificación</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-110-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Despachador indicadores</div>
                                                <div class="table-cell">Status por rubros del despachador y sus tipificaciones con totales</div>
                                                <div class="table-cell">despachador</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-111-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Twitter detalle</div>
                                                <div class="table-cell">Muestra los twits recibidos a detalle por dia</div>
                                                <div class="table-cell">fecha, agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-112-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">Twitter indicadores</div>
                                                <div class="table-cell">Muestra los twits recibidos y respondidos con promedios y totales</div>
                                                <div class="table-cell">fecha, agente</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-113-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">WhatsApp detalle</div>
                                                <div class="table-cell">Muestra los mensajes entrantes y salientes a detalle por dia</div>
                                                <div class="table-cell">fecha, agente, contacto</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-114-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">WhatsApp sesión</div>
                                                <div class="table-cell">Muestra tiempos de sesion por agente y mensjaes</div>
                                                <div class="table-cell">fecha, agente, contacto</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-115-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                            <div class="table-row">
                                                <div class="table-cell">WhatsApp indicadores</div>
                                                <div class="table-cell">Muestra total de mensajes entrantes y salientes por dia</div>
                                                <div class="table-cell">fecha, agente, contacto</div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-check-square"></i></div>
                                                <div class="table-cell"><i style="font-size: 16px;" class="far fa-square"></i></div>
                                                <div class="table-cell"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".bd-116-modal-lg"><i class="fas fa-search"></i></button></i></div>
                                            </div>
                                        </div>
                                        <ul>
                                            <h4 class="text-info">Descripción de filtros</h4>
                                            <hr>
                                            <ul>
                                                <div class="row">
                                                    <div class="col">
                                                        <li><span class="text-info">Fecha</span> seleccionamos fecha de inicio y fecha de termino.</li>
                                                        <li><span class="text-info">Horario</span> son las llamadas dentro de servicio ó fuera de servicio.</li>
                                                        <li><span class="text-info">Campaña</span> nombre de las campañas.</li>
                                                        <li><span class="text-info">Agente</span> nombre de los agentes.</li>
                                                        <li><span class="text-info">Status de llamadas</span> llamada terminada o abandonada.</li>
                                                        <li><span class="text-info">Tipo de llamda</span> llamadas entrantes(inbound) o salientes(outbound).</li>
                                                    </div>
                                                    <div class="col">
                                                        <li><span class="text-info">Calidad</span> seleccionamos las llamadas evaluadas y no evaludas.</li>
                                                        <li><span class="text-info">Formulario</span> seleccionamos el nombre de los formularios.</li>
                                                        <li><span class="text-info">Evaluacion</span> seleccionamos el nombre de la evaluacion.</li>
                                                        <li><span class="text-info">Preview</span> seleccionamos por el nombre del despachador.</li>
                                                        <li><span class="text-info">Tipificación</span> clasificación que se le dio a la llamada.</li>
                                                        <li><span class="text-info">Contacto</span> nombre asignado al contacto en whatsapp.</li>
                                                    </div>
                                                </div>
                                            </ul>
                                        </ul>
                                    </div>
                                    <div class="modal fade bd-80-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repovm.png"); ?>" alt="reporte vm" height="500" width="900">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-81-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repocalidad.png"); ?>" alt="reporte calidad" height="400" width="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-82-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repoformulario.png"); ?>" alt="reporte formulario" height="400" width="900">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-83-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_smsdet.png"); ?>" alt="reporte sms_detalle" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-84-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_smsindi.png"); ?>" alt="reporte sms_indicador" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-85-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_emaildet.png"); ?>" alt="reporte email_detalle" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-86-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_emailindi.png"); ?>" alt="reporte email_indicador" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-87-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_inbound.png"); ?>" alt="reporte inbound" height="550" width="1100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-88-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_outbound.png"); ?>" alt="reporte outbound" height="550" width="1100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-89-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_campana.png"); ?>" alt="reporte outbound" height="500" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-90-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_exitosas.png"); ?>" alt="reporte exitosas" height="400" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-91-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php // echo site_url("assets/img/imgmanual/repo_encuestas.png"); ?>" alt="reporte encuestas" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-92-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_tespera.png"); ?>" alt="reporte espera" height="350" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-93-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_ivr.png"); ?>" alt="reporte ivr" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-94-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_abandonos.png"); ?>" alt="reporte abandonos" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-95-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_abanmediahora.png"); ?>" alt="abandonos mediahora" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-96-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_etenmediahora.png"); ?>" alt="atendidas mediahora" height="450" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-97-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_compmediahora.png"); ?>" alt="atendidas mediahora" height="500" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-98-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_historico.png"); ?>" alt="historico" height="530" width="1050">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-99-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_acw.png"); ?>" alt="acw" height="350" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-100-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_descanso.png"); ?>" alt="descanso" height="530" width="1050">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-101-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_poragente.png"); ?>" alt="por agente" height="530" width="1050">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-102-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_tiemposesion.png"); ?>" alt="tiempo sesion" height="530" width="1050">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-103-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_distrgrafico.png"); ?>" alt="distribucion grafico" height="530" width="800">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-104-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_acumdiario.png"); ?>" alt="acumulado diario" height="450" width="1100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-105-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_acummes.png"); ?>" alt="acumulado mes" height="450" width="1100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-106-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_slinbound.png"); ?>" alt="sl inbound" height="450" width="900">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-107-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_sloutbound.png"); ?>" alt="sl outbound" height="450" width="900">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-108-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_slgraf.png"); ?>" alt="sl grafica" height="650" width="850">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-109-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_despdetalle.png"); ?>" alt="desp detalle" height="450" width="1100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-110-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_desptipi.png"); ?>" alt="desp tipificaciones" height="500" width="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-111-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_despindi.png"); ?>" alt="desp indicadores" height="400" width="900">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-112-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_twitdet.png"); ?>" alt="twitter detalle" height="500" width="1100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-113-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_twitindi.png"); ?>" alt="twitter indicador" height="450" width="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-114-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_whatsdetalle.png"); ?>" alt="whatsapp detalle" height="500" width="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-115-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_whatssesion.png"); ?>" alt="whatsapp sesion" height="500" width="1000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade bd-116-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <img src="<?php echo site_url("assets/img/imgmanual/repo_whatsindi.png"); ?>" alt="whatsapp indicador" height="500" width="1000">
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- cierre acordeon repores -->
                            </div><!-- cierre menu botones reportes-->
                        </div><!-- cierre card reportes -->
                        <div class="card">
                            <div class="card-header nopadding" id="heading006">
                                <h3 class="mb-0">
                                    <button class="btn btn-info btn-lg btn-block collapsed" type="button" data-toggle="collapse" data-target="#collapse006" aria-expanded="false" aria-controls="collapse006">
                                        <i class="fas fa-tv"></i>&nbsp;&nbsp;Monitoreo
                                    </button>
                                </h3>
                            </div>
                            <div id="collapse006" class="collapse" aria-labelledby="heading006" data-parent="#accorManual">
                                <div class="card-body"></div>
                            </div>
                        </div><!-- cierre card disponible -->
                        <div class="card">
                            <div class="card-header nopadding" id="heading006">
                                <h3 class="mb-0">
                                    <button class="btn btn-info btn-lg btn-block collapsed" type="button" data-toggle="collapse" data-target="#collapse007" aria-expanded="false" aria-controls="collapse007">
                                        <i class="fas fa-tv"></i>&nbsp;&nbsp;Perfiles
                                    </button>
                                </h3>
                            </div>
                            <div id="collapse007" class="collapse" aria-labelledby="heading006" data-parent="#accorManual">
                                <div class="card-body"></div>
                            </div>
                        </div><!-- cierre card disponible -->
                    </div> <!-- cierre accord manual -->
                </div><!-- cierre del container-->
            </div><!-- cierre del row principal-->
        </div><!-- cierre del row nonom035 -->
        <div class="row nom035 text-center" style="display:none">
            <img src="<?php echo site_url('assets/img/nom035.jpg'); ?>" alt="NOM035">
        </div>
    <?php else: ?>
        <div>
            <p>Ya estás en el sistema, por favor dirígete a la consola.</p>
        </div>
    <?php endif; ?>
</div><!-- cierre container pastilla-->
