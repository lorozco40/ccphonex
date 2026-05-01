<style>
body {
  background-color: #000000;
  color: #f4f4f4;
}
.centro {
  margin-top: 50px;
  display: flex;
  justify-content: center;
}
.wave {
  width: 5px;
  height: 100px;
  background: linear-gradient(45deg, cyan, #fff);
  margin: 10px;
  animation: wave 1s linear infinite;
  border-radius: 20px;
}
.wave:nth-child(2) {
  animation-delay: 0.1s;
}
.wave:nth-child(3) {
  animation-delay: 0.2s;
}
.wave:nth-child(4) {
  animation-delay: 0.3s;
}
.wave:nth-child(5) {
  animation-delay: 0.4s;
}
.wave:nth-child(6) {
  animation-delay: 0.5s;
}
.wave:nth-child(7) {
  animation-delay: 0.6s;
}
.wave:nth-child(8) {
  animation-delay: 0.7s;
}
.wave:nth-child(9) {
  animation-delay: 0.8s;
}
.wave:nth-child(10) {
  animation-delay: 0.9s;
}

@keyframes wave {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1);
  }
  100% {
    transform: scale(0);
  }
}
</style>
<script type="text/javascript">
    <?php if(empty($data)) $data = [] ?>
    <?php if(empty($data['nombre'])) $data['nombre'] = 'Sin datos'; ?>
    <?php if(empty($data['email'])) $data['email'] = 'Sin datos'; ?>
    cid = JSON.parse('<?=json_encode($data)?>');
</script>
<div id="msg"></div>
