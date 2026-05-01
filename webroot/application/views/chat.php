<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assertive chat</title>
        <link rel="stylesheet" href="<?=site_url('css/chat.css')?>" />
    </head>
    <body>
        <main id="app">
            <input type="hidden" ref="bago_url" value="<?=getenv('BAGO_JS_URL')?>">
            <input type="hidden" ref="ins" value="<?=$ins?>">
            <div id="auchatbtn" class="btn" :class="{'d-none': viendochat}" @click="toggleViews">
                <img src="<?=site_url('assets/img/diademo.png')?>" alt="Chat">
            </div>
            <div id="auchat" class="dtogle" :class="{'d-none': !viendochat}">
                <div id="chat-header" @click="toggleViews"><div>Assertive chat</div><div></div><div>X</div></div>
                <div class="chat-messages" :class="{'d-none': !espera}">Espera un momento por favor, un agente te atenderá!</div>
                <div class="chat-messages" :class="{'d-none': espera}" ref="chatmsgs"></div>
                <div id="chat-input">
                    <textarea id="chat-input-text" ref="cit" @keyup.enter="sendmsg" autofocus></textarea>
                    <button @click="sendmsg" id="chat-input-send" class="btn">
                        <img src="<?=site_url('assets/img/enviar.png')?>" alt="Enviar">
                    </button>
                </div>
            </div>
        </main>
        <?php if (ENVIRONMENT == 'production'): ?>
            <script src="<?=site_url('js/vue.prod.js')?>"></script>
        <?php else: ?>
            <script src="https://cdn.jsdelivr.net/npm/vue@3.4.38/dist/vue.global.min.js"></script>
        <?php endif; ?>
        <script src="<?=site_url('js/chat.js?v='.time())?>"></script>
    </body>
</html>