//Calidad Campos
const cc = {
    addComentarioForm: () => {
        $("#form_add_comment").submit();
    },
    deleteQuestionForm: (id) => {
        $("#form_delete_field input[name=id_quality_fields]").val(id);
        $("#form_delete_field").submit();
    }
}