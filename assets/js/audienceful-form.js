(function($){
    $(document).on("submit", ".audienceful-form", function(e){
        e.preventDefault();

        let form = $(this);
        let email = form.find(".audienceful-email").val();
        let messageBox = form.closest(".audienceful-wrapper").find(".audienceful-message");
        let button = form.find(".audienceful-submit");
        let nonce = form.find("input[name='audienceful_form_nonce']").val();


        button.addClass("loading").prop("disabled", true);

        $.post(audienceful_ajax.ajax_url, {
            action: "audienceful_submit",
            email: email,
            audienceful_form_nonce: nonce
        }, function(res){
            if(res.success){
                messageBox.removeClass("error").addClass("success").text(res.data.message);
                form.find(".audienceful-email").val("");
            } else {
                messageBox.removeClass("success").addClass("error").text(res.data.message);
            }
            button.removeClass("loading").prop("disabled", false);
        });
    });
})(jQuery);
