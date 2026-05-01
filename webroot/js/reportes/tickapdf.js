var campanas = {};

$(document).ready(function(){
    $(document).on("change", "#campanas", function(){
        $("#forms, #plant").html('');
        let idc = $(this).val();
        for (var i = 0; i < forms.length; i++) {
            if (forms[i].id_campaign == idc) {
                $("#forms").append("<option value='"+forms[i].id+"'>"+forms[i].name+"</option>");
            }
        }
        console.log(plant);
        for (var i = 0; i < plant.length; i++) {
            if (plant[i].id_campaign == idc) {
                $("#plant").append("<option value='"+plant[i].file+"'>"+plant[i].name+"</option>");
            }
        }
    });
})
