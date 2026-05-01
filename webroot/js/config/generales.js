var ft = {"1":["dd-mm-yyyy","d-m-Y"],"2":["mm-dd-yyyy","m-d-Y"],"3":["yyyy-mm-dd","Y-m-d"]}
$(document).ready(function(){
    $(document).on("change", "#FormatoFechaMysql", function(){
        var tipo = $(this).find(":selected").data("tipo");
        console.log(tipo);
        $("#FormatoFechaJs").val(ft[tipo][0]);
        $("#FormatoFechaInput").val(ft[tipo][1]);
    })
})
