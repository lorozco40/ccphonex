const { createApp } = Vue

createApp({
    data() {
        return {
            espera: false,
            ses: {id: 0, name: 'Anónimo', msgs: ''},
            msgs: '',
            viendochat: false,
            chatinfo: {},
            ins: '',
            errores: 0,
        }
    },
    methods: {
        sendmsg() {
            let msg = this.$refs.cit.value.trim()
            if (msg == '') return
            powsockfun.send({a: 'chat', dir: 'i', msg: this.$refs.cit.value.trim()})
            this.msgs += "<div class=\"u2e\">" + this.$refs.cit.value.trim() + "</div>"
            this.$refs.chatmsgs.innerHTML = this.msgs
            this.$refs.chatmsgs.scrollTop = this.$refs.chatmsgs.scrollHeight
            this.$refs.cit.value = ''
        },
        toggleViews() {
            this.viendochat = !this.viendochat
            if (this.viendochat) {
                if (this.ses.id == 0) {
                    this.powsockcon()
                }
                document.getElementsByTagName("body")[0].style.width  = "260px"
                document.getElementsByTagName("body")[0].style.height = "415px"
                this.$refs.cit.focus()
            } else {
                document.getElementsByTagName("body")[0].style.width  = "50px"
                document.getElementsByTagName("body")[0].style.height = "50px"
            }
        },
        powsockcon() {
            let powsock = new WebSocket("wss://" + this.$refs.bago_url.value + "/ws")
            powsockfun = {
                send: msg => {
                    msg.acsid = this.ses.id
                    msg.ins = this.ins
                    // console.log("Sale mensaje:")
                    // console.log(msg)
                    msg = JSON.stringify(msg)
                    powsock.send(msg)
                },
            }
            powsock.onopen = () => {
                // console.log("Powsock conectado chat")
                this.espera = true
                powsockfun.send({a: 'chatreg'})
            }
            powsock.onmessage = e => {
                this.powsockmanagemsg(JSON.parse(e.data))
            }
            powsock.onclose = () => {
                console.log("Powsock cerrado, Reconectando en 5 segundos ...")
                this.espera = true
                setTimeout(() => {
                    this.powsockcon()
                }, 5000)
            }
            powsock.onerror = error => {
                console.log("Error en la conexion de Powsock")
                console.error(error)
            }
        },
        powsockmanagemsg(msg) {
            // console.log('Mensaje recibido:')
            // console.log(msg)
            switch (msg.a) {
                case 'bas':
                    this.ses = msg.ses
                    msg.dfs.forEach(df => { this.chatinfo[df.name] = df })
                    if (msg.ses.id_user) this.espera = false
                    this.msgs = this.chatinfo.saludo.value + "<br><br>"
                    this.$refs.chatmsgs.innerHTML = this.msgs
                    break
                case 'updses':
                    if (msg.ses.id_user) this.espera = false
                    this.ses = msg.ses
                    break
                case 'achat':
                    this.manageachat(msg)
                    break
                case 'error':
                    this.espera = false
                    this.$refs.chatmsgs.innerHTML = "<div>" + msg.msg + "</div><br>"
                default:
                    if (this.ses.id < 1 && this.errores > 0) {
                        // Si no hay sesión, reintento crearla en 5 segundos
                        this.$refs.chatmsgs.innerHTML += "<div>Intentando nuévamente ... " + 5 - this.errores + "</div>"
                        this.ses.id = 0
                        setTimeout(() => {
                            powsockfun.send({a: 'chatreg'})
                            this.errores++
                        }, 5000)
                    }
            }
        },
        manageachat(msg) {
            switch (msg.b) {
                case 'chat':
                    this.msgs += "<div class=\"e2u\">" + msg.msg + "</div>"
                    this.$refs.chatmsgs.innerHTML = this.msgs
                    this.$refs.chatmsgs.scrollTop = this.$refs.chatmsgs.scrollHeight
                    break
                case 'fin':
                    this.msgs = "<div>" + this.chatinfo.adios.value + "</div>"
                    this.$refs.chatmsgs.innerHTML = this.msgs
                    this.ses = {id: 0, name: 'Anónimo', msgs: ''}
                    break
                default:
                    console.log("Mensaje de error o desconocido recibido")
                    console.log(msg)
            }
        },

    },
    mounted() {
        this.ins = this.$refs.ins.value
    }
}).mount('#app')
