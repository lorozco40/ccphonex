$(document).ready(function(){
    // Lista de archivos a subir
    let newFileList = new DataTransfer()
    const fileInput = $('#fileEmailAdjuntoModal')
    const hiddenFileInput = $('#fileEmailAdjuntoModal2')
    const fileList = $('#fileList')
    let PostMaxSizeEmailText = $("#post_max_size_email").val()
    const PostMaxSizeEmail = parseFloat(PostMaxSizeEmailText)
    let totalFileSize = 0

    moment.locale('es')
    emconsola = {
        editor:         CKEDITOR.replace('input_email_body'),
        preguntaremail: setInterval(function(){ emconsola.traer_data(emconsola.maxid) }, 20000),
        maxid:          0,
        emdata:         {cuenta:{},agentes:{},data:{}},
        emsele:         {},
        traer_data: function(maxid) {
            $.get('https://'+bago_url+'/email/consola', {uid: agente.id, maxid: maxid, uid: agente.id, key: agente.token}, function(res) {
                if (typeof res.error !== "undefined") {
                    toastmsg(res.error, "danger")
                } else {
                    emconsola.emdata.cuenta  = res.cuenta[0]
                    $("#emForm input[name=id_cuenta]").val(emconsola.emdata.cuenta.id)
                    html = "<option value=''>-- Elige --</option>"
                    emconsola.emdata.agentes = {}
                    res.agentes.forEach(function(row){
                        emconsola.emdata.agentes[row.id] = row
                        if (row.id != agente.id) {
                            html += "<option value='"+row.id+"'>"+row.nombre+"</option>"
                        }
                    })
                    $("#emtransto").html(html)
                    htmlin = ""
                    htmlout = ""
                    veralerta = false
                    res.data.forEach(function(row){
                        if (row.id > emconsola.maxid) emconsola.maxid = row.id
                        emconsola.emdata.data[row.id] = row
                        if(row.status > 0) {
                            clasenuevo = ''
                        } else {
                            clasenuevo = ' <span class="esnuevo">nuevo</span>'
                            veralerta = true
                        }
                        if (row.type == 'entrante') {
                            htmlin += "<a class='emlink btn-link text-dark row' data-id='" + row.id + "' data-uid='" + row.id_user + "' href='#'>" +
                                "<div class='col'><div class='row'><div class='col col-auto'>" + moment(row.date).format("DD MMM") +
                                "</div><div class='col text-right'>" + row.sender + "</div></div><div class='row'><div class='col'><i>"
                            if (row.subject.length>45) {
                                htmlin += row.subject.substring(0,40) + " ...</i>" + clasenuevo + "</div></div></div></a>"
                            } else {
                                htmlin += row.subject + "</i>" + clasenuevo + "</div></div></div></a>"
                            }
                        } else {
                            htmlout += "<a class='emlink btn-link text-dark row' data-id='" + row.id + "' data-uid='" + row.id_user + "' href='#'>" +
                                "<div class='col'><div class='row'><div class='col col-auto'>" + moment(row.date).format("DD MMM") +
                                "</div><div class='col text-right'>" + row.to  + "</div></div><div class='row'><div class='col'><i>"
                            if (row.subject.length>45) {
                                htmlout += row.subject.substring(0,45) + " ...</i></div></div></div></a>"
                            } else {
                                htmlout += row.subject + "</i></div></div></div></a>"
                            }
                        }
                    })
                    if (veralerta) {
                        notifyMe({msg:'Tienes un Email'})
                        toastmsg("Tienes un Email", "success")
                        $("[href='#emailconsolax']").append('<span class="badge embadge">1</span>')
                        veralerta = false
                    }
                    if (htmlin != '') {
                        if (maxid == 0) {
                            $("#emlista").html(htmlin)
                        } else {
                            $("#emlista").prepend(htmlin)
                        }
                    }
                    if (htmlout != '') {
                        if (maxid == 0) {
                            $("#emvlista").html(htmlout)
                        } else {
                            $("#emvlista").prepend(htmlout)
                        }
                    }
                }
            },"json")
            .fail(function(res){
                if (typeof res.responseJSON?.error !== "undefined") {
                    toastmsg(res.responseJSON.error, "danger")
                } else {
                    toastmsg("Error de red, verifica tu conexión.", "danger")
                }
            })
        },
        mostrar: async function(id) {
            emconsola.emsele = emconsola.emdata.data[id]
            if (emconsola.emsele.status == false && emconsola.emsele.id_user == agente.id) {
                await $.post(site_url+'email/actu', {id: id, status: 1}, function(res) {
                    if (typeof res.error != "undefined") {
                        toastmsg(res.error, "danger")
                    } else {
                        $(".embadge").remove()
                        emconsola.emdata.data[id].status = 1
                        emconsola.emsele.status = 1
                        toastmsg("Email actualizado a leido, empieza la cuenta de tiempo de servicio.", "success")
                        $("a.emlink[data-id="+id+"]").find(".esnuevo").remove()
                    }
                }, 'json')
                .fail(function() {

                    toastmsg("Error de red, verifica tu conexión.", "danger")
                })
            }
            $("#spinnerModal").modal("show")
            let VerCorreo = await $.post(site_url+'email/verCorreo', {id: id})
            $("#spinnerModal").modal("hide")

            html =  "<i>De:</i><strong> " + emconsola.emsele.sender + ", " + emconsola.emsele.from + "</strong><br>" +
                    "<i>Para:</i><strong> " + emconsola.emsele.to + "</strong><br>"
            if( VerCorreo?.cc?.length > 0 ) {
                emconsola.emdata.data[id].cc = VerCorreo.cc
                emconsola.emsele.cc = VerCorreo.cc
                html += "<i>CC:</i><strong> " + emconsola.emsele.cc + "</strong><br>"
            }
            html += "<i>Fecha:</i><strong> " + moment(emconsola.emsele.date).format("DD-MM-YYYY hh:mm:ss") + "</strong><br>" +
                    "<i>Asunto:</i><strong> " + emconsola.emsele.subject + "</strong>"

            $("#eminfo").html(html)
            preurl = (window.location.host == 'localhost') ? 'src="https://'+agente.servask+'/emailfiles/' : '/emailfiles/'
            $("#emiframe").contents().find('body').html(emconsola.emsele.htmlmsg.replace(/src="/g, preurl))
            $("#emiframe").contents().find('body').html(emconsola.emsele.htmlmsg.replace(/\\"/g, '"'))
            html = ""
            if (emconsola.emsele.attachments.length > 0) {
                var html = "<i>Adjuntos:</i>"
                var atas = emconsola.emsele.attachments.split(",")
                atas.forEach(function(e){
                    var parts = e.split("/")
                    html += "<br><a target='_blank' href='"+site_url+"emailfiles/"+emconsola.emdata.cuenta.id+"/"+e+"'>"+parts[parts.length-1]+"</a>"
                })
            }
            $("#emadjuntos").html(html)
            if (emconsola.emsele.id_user != agente.id) {
                $(".emselin").removeClass("emselin").addClass("noemselin")
                $(".noemsel").removeClass("noemsel").addClass("emsel")
                toastmsg('Mensaje asignado a otro usuario.', "danger")
            } else if (emconsola.emsele.type == 'entrante') {
                $(".noemsel").removeClass("noemsel").addClass("emsel")
                $(".noemselin").removeClass("noemselin").addClass("emselin")
            } else {
                $(".emselin").removeClass("emselin").addClass("noemselin")
                $(".noemsel").removeClass("noemsel").addClass("emsel")
            }
        },
        transferir: function() {
            if ($('#emtransto').val() != "") {
                $.post(site_url+'email/actu', {id: emconsola.emsele.id, transfer: $('#emtransto').val()}, function(respuesta) {
                    if (respuesta.status == "error") {
                        toastmsg(respuesta.msg, "danger")
                    } else {
                        $(".emlink[data-id=" + emconsola.emsele.id + "]").remove()
                        toastmsg("Mensaje transferido al agente seleccionado.", "success")
                        emconsola.cerrar()
                    }
                }, 'json')
                .fail(function() {
                    if (typeof res.responseJSON.error !== "undefined") {
                        toastmsg(res.responseJSON.error, "danger")
                    } else {
                        toastmsg("Error de red, verifica tu conexión.", "danger")
                    }
                })
            } else {
                toastmsg("Debes elegir un agente para transferir el correo.", "danger")
            }
        },
        responder: function() {
            CKEDITOR.instances.input_email_body.setData("<br>=== " + emconsola.emsele.sender + ", " +
                emconsola.emsele.from + " - " + moment(emconsola.emsele.date).format("DD-MM-YYYY hh:mm:ss") +
                ", escribió: ===<br>"+emconsola.emsele.htmlmsg)
            fileInput.val("")
            hiddenFileInput.val("")
            fileList.empty()
            $("#quitar-cc").click()
            $("#quitar-cco").click()
            $("#existingFilesAtachList").val("")
            $("#emForm input[name=id]").val(emconsola.emsele.id)
            $("#emForm input[name=subject]").val("Re: " + emconsola.emsele.subject)
            $("#emForm input[name=attachment]").val("")
            $("#btnEnviarEmailModal").show()

            $("#emModal").modal("show")
        },
        escribir: function() {
            $("#emForm input[name=id]").val("0")
            $("#emForm textarea[name=to], #emForm input[name=subject]").val("")
            CKEDITOR.instances.input_email_body.setData("")
            fileInput.val("")
            hiddenFileInput.val("")
            fileList.empty()
            $("#quitar-cc").click()
            $("#quitar-cco").click()
            $("#existingFilesAtachList").val("")
            $("#btnEnviarEmailModal").show()
            $("#emModal").modal("show")
        },
        cerrar: function() {
            $(".emlink.btn-success").removeClass("btn-success").addClass("btn-link")
            $("#eminfo, #emadjuntos").html("")
            $(".emsel").removeClass("emsel").addClass("noemsel")
            $(".emselin").removeClass("emselin").addClass("noemselin")
            $("#emiframe").contents().find('body').html("<p>Assertive mail! cliente de correo</p>")
            emconsola.emsele = {}
        },
        enviar: function() {
            $("#spinnerModal").modal("show")

            // ESTABLECE EL NUEVO FILELIST EN EL CAMPO OCULTO PUES SON LOS ARCHIVOS A SUBIR
            hiddenFileInput[0].files = newFileList.files

            var data = $("#emForm").serializefiles()
            $.ajax({
                type: 'POST',
                method: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                url: site_url+'email/enviar',
                data: data,
                dataType: 'json'
            })
            .done(function(data){
                $("#spinnerModal").modal("hide")
                if (typeof data.error != 'undefined') {
                    toastmsg(data.error, "danger")
                } else {
                    $("#emModal").modal("hide")
                    toastmsg(data.msg, "success")
                    window.scrollTo(0, 0)
                }
            })
            .fail(function(data) {
                $("#spinnerModal").modal("hide")
                toastmsg("El servidor de envío no ha contestado, revisa tus datos de acceso.", "danger")
            })
        },
        buscar: function() {
            var email = $("#embuscar").val()
            if (email.length >= 3) {
                $.get('https://'+bago_url+'/email/historia/'+email, {email:email, uid: agente.id, key: agente.token}, function(data){
                    if (typeof data.error != 'undefined') {
                        toastmsg(data.error, "danger")
                    } else {
                        html = ""
                        data.forEach(function(row){
                            emconsola.emdata.data[row.id] = row
                            if (row.type == 'entrante') {
                                clasenuevo = (row.status === true) ? '' : ' <span class="esnuevo">nuevo</span>'
                                html += "<a class='emlink btn-link row text-dark' data-id='" + row.id + "' data-uid='" + row.id_user + "' href='#'>" +
                                    "<div class='col'><div class='row'><div class='col col-auto'>" + moment(row.date).format("DD MMM") +
                                    "</div><div class='col text-right'>" + row.sender + "</div></div><div class='row'><div class='col'><i>"
                                if (row.subject.length>45) {
                                    html += '<i class="far fa-arrow-alt-circle-left"></i> ' + row.subject.substring(0,40) + " ...</i>" + clasenuevo + "</div></div></div></a>"
                                } else {
                                    html += '<i class="far fa-arrow-alt-circle-left"></i> ' + row.subject + "</i>" + clasenuevo + "</div></div></div></a>"
                                }
                            } else {
                                html += "<a class='emlink btn-link row text-dark' data-id='" + row.id + "' data-uid='" + row.id_user + "' href='#'>" +
                                    "<div class='col'><div class='row'><div class='col col-auto'>" + moment(row.date).format("DD MMM") +
                                    "</div><div class='col text-right'>" + row.to + "</div></div><div class='row'><div class='col'><i>"
                                if (row.subject.length>45) {
                                    html += '<i class="far fa-arrow-alt-circle-right"></i> ' + row.subject.substring(0,40) + " ...</i></div></div></div></a>"
                                } else {
                                    html += '<i class="far fa-arrow-alt-circle-right"></i> ' + row.subject + "</i></div></div></div></a>"
                                }
                            }
                        })
                        $("#embures").html(html)
                        $("#emcontainer a[href=#embures]").click()
                    }
                },"json")
                .fail(function(res){
                    if (typeof res.responseJSON.error !== "undefined") {
                        toastmsg(res.responseJSON.error, "danger")
                    } else if (typeof res.error !== "undefined"){
                        toastmsg(res.error, "danger")
                    } else {
                        toastmsg("Error de red, verifica tu conexión.", "danger")
                    }
                })
            } else {
                toastmsg('Mínimo 3 caractéres para buscar')
            }
        },
        adddjuntos: function() {
            if (emconsola.emsele.attachments.length > 0) {
                let atas = emconsola.emsele.attachments.split(",")
                $("#existingFilesAtachList").val(emconsola.emsele.attachments)
                atas.forEach(function(e){
                    const listItem = $('<li>').addClass('list-group-item bg-dark py-1')
                    const listItemContent = $('<div>').addClass('d-flex align-items-center')
                    const iconDiv = '<div class="px-1"><i class="far fa-file-archive"></i></div>'
                    const filenameDiv = $('<div>').addClass('pl-1')
                    const small = $('<small>').text(e)
                    filenameDiv.append(small)
                    const removeButtonDiv = $('<div>').addClass('ml-auto')
                    const removeButton = $('<button>').addClass('btn btn-danger btn-sm py-0').attr({
                        'title': 'Quitar'
                    }).text('X')
                    removeButtonDiv.append(removeButton)
                    listItemContent.append(iconDiv)
                    listItemContent.append(filenameDiv)
                    listItemContent.append(removeButtonDiv)
                    listItem.append(listItemContent)
                    fileList.append(listItem)

                    removeButton.on('click', function() {
                        // Elimina el elemento li padre del botón
                        listItem.remove()
                        let actlist = $("#existingFilesAtachList").val()
                        actlist = "," + actlist + ","
                        actlist = actlist.replace(","+e+",", ",")
                        actlist = actlist.replace(/^,|,$/g, "")
                        $("#existingFilesAtachList").val(actlist)
                    })
                })
            }
        },
    } // Termina objeto emconsola

    emconsola.traer_data(0)
    $(document).on("click", ".emlink", function(e){
        e.preventDefault()
        id = $(this).data("id")
        emconsola.mostrar(id)
        $(".emlink.btn-success").removeClass("btn-success").addClass("btn-link")
        $(this).addClass("btn-success")
        if ($("#emnuevo").offset().top > 200) {
            $("html,body").animate({ scrollTop: $("#emnuevo").offset().top }, 500)
        }
    })
    $(document).on("submit", "#emForm", function(e) {
        e.preventDefault()
        emconsola.enviar()
    })
    $(document).on("click", "#emdotrans", function(e) {
        e.preventDefault()
        emconsola.transferir()
    })
    $(document).on("click", "#emclose", function(e) {
        e.preventDefault()
        emconsola.cerrar()
    })
    $(document).on("click", "#emnuevo", function(e) {
        e.preventDefault()
        newFileList = new DataTransfer()
        totalFileSize = 0
        emconsola.escribir()
    })
    $(document).on("click", "#emreply", function(e) {
        e.preventDefault()
        $("#emForm textarea[name=to]").val(emconsola.emsele.from)
        emconsola.responder()
    })
    $(document).on("click", "#emreplyall", function(e) {
        e.preventDefault()
        $("#emForm textarea[name=to]").val(emconsola.emsele.from)
        if(emconsola.emsele.cc.length > 0){
            $("#emForm textarea[name=cc]").val(emconsola.emsele.cc)
            $("#btn-cc").click()
        }
        emconsola.responder()
    })
    $(document).on("click", "#emforward", function(e) {
        e.preventDefault()
        $("#emForm textarea[name=to]").val("")
        emconsola.responder()
        emconsola.adddjuntos()
    })
    $(document).on("keyup", "#embuscar", function(e){
        var keycode = (e.keyCode ? e.keyCode : e.which)
        if (e.keyCode == 13) { emconsola.buscar() }
    })
    $(document).on("click", "#btn-cc", function(e) {
        e.preventDefault()
        $(this).attr("hidden",true)
        $("#div-cc").removeAttr("hidden")
        $("#textarea-cc").val("")
        $("#textarea-cc").attr("required", true)
    })
    $(document).on("click", "#quitar-cc", function(e) {
        e.preventDefault()
        $("#div-cc").attr("hidden",true)
        $("#btn-cc").removeAttr("hidden")
        $("#textarea-cc").removeAttr("required")
        $("#textarea-cc").val("")
    })
    $(document).on("click", "#btn-cco", function(e) {
        e.preventDefault()
        $(this).attr("hidden",true)
        $("#div-cco").removeAttr("hidden")
        $("#textarea-cco").val("")
        $("#textarea-cco").attr("required", true)
    })
    $(document).on("click", "#quitar-cco", function(e) {
        e.preventDefault()
        $("#div-cco").attr("hidden",true)
        $("#btn-cco").removeAttr("hidden")
        $("#textarea-cco").removeAttr("required")
        $("#textarea-cco").val("")
    })

    fileInput.on('change', function(e) {
        $.each(this.files, function(index, file) {
            const fileName = file.name
            if (!isFileInList(fileName)) {
                if (totalFileSize + file.size <= PostMaxSizeEmail * 1024 * 1024) {

                    const listItem = $('<li>').addClass('list-group-item bg-dark py-1')
                    const listItemContent = $('<div>').addClass('d-flex align-items-center')
                    const iconDiv = '<div class="px-1"><i class="far fa-file-archive"></i></div>'
                    const filenameDiv = $('<div>').addClass('pl-1')
                    const small = $('<small>').text(fileName)
                    filenameDiv.append(small)
                    const removeButtonDiv = $('<div>').addClass('ml-auto')
                    const removeButton = $('<button>').addClass('btn btn-danger btn-sm py-0').attr({
                        'title': 'Quitar'
                    }).text('X')
                    removeButtonDiv.append(removeButton)
                    listItemContent.append(iconDiv)
                    listItemContent.append(filenameDiv)
                    listItemContent.append(removeButtonDiv)
                    listItem.append(listItemContent)
                    fileList.append(listItem)

                    removeButton.on('click', function() {
                        // Actualiza el tamaño total al eliminar un archivo
                        totalFileSize -= file.size
                        // Elimina el elemento li padre del botón
                        listItem.remove()

                        for (let i = 0; i < newFileList.files.length; i++) {
                            const item = newFileList.files[i]
                            // Verificar si el elemento tiene un nombre y coincide con el nombre que deseas eliminar
                            const elementoName = item.name
                            if (elementoName === file.name) {
                                // Eliminar el elemento de la lista
                                newFileList.items.remove(i)
                                break // Salir del bucle después de eliminar el elemento
                            }
                        }
                    })

                    totalFileSize += file.size
                    //AGREGO EL ELEMENTO DEL INPUT FILE A MI LISTA DE ARCHIVOS
                    newFileList.items.add(file)
                } else {
                    toastmsg("La suma de los archivos no debe superar "+PostMaxSizeEmailText+"B.", "danger")
                }
            }
        })
        //LIMPIO EL INPUT FILE POR SI DESPUES DE ELIMINAR EL ARCHIVO VUELVE A CARGAR EL MISMO ARCHIVO, ASI, LO PODRÁ AGREGAR A LA LISTA DE ARCHIVOS A SUBIR
        fileInput.val("")
    })

    function isFileInList(fileName) {
        const items = fileList.find('.list-group-item')
        let exists = false
        items.each(function() {
          const existingFileName = $(this).find('small').text()
          if (existingFileName === fileName) {
            exists = true
            return false // Sale del bucle cuando encuentra un archivo duplicado
          }
        })
        return exists
    }
})


