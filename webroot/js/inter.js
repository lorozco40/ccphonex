$(".form-check-input").on("click", function() {
    let target = $(this).attr("data-target");
    if ($(".form-check-input:checked").length > 2) {
        this.checked = false;
    }
    $(".tab-pane").removeClass("active");
    $(target).addClass("active");
});