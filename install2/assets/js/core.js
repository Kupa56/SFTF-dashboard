$(function(){
    App.Init();
});



/**
 * Main Namespace
 * App
 */
var App = {};

    /**
     * Initialize
     */
    App.Init = function()
    {
        $("body").on("click", ".next-btn", function() {
            var nextstep = $($(this).data("next"));

            $("html, body").animate({
                scrollTop: 0
            }, 200, function() {
                $(".step").stop(true, true).hide();
                nextstep.stop(true, true).fadeIn(1000);

                $("header").addClass("mini");
            });
        });


        App.Controls();
    }
    

    /**
     * Submit controls
     */
    App.Controls = function()
    {
        var form = $("form#controls");

        $(":input", form).on("focus", function() {
            $(this).removeClass('error');
        });


        $(":input[name='upgrade']").on("change", function() {
            if ($(this).val()) {
                $(".upgrade-only :input", form).prop("disabled", false);
                $(".upgrade-only", form).removeClass("none");

                $(".install-only :input", form).prop("disabled", true);
                $(".install-only", form).addClass("none");
            } else {
                $(".upgrade-only :input", form).prop("disabled", true);
                $(".upgrade-only", form).addClass("none");

                $(".install-only :input", form).prop("disabled", false);
                $(".install-only", form).removeClass("none");
            }
        });


        form.on("submit", function() {
            var submitable = true;
            var errors = [];

            $(":input.required", form).not(":disabled").each(function() {
                if (!$(this).val()) {
                    $(this).addClass("error");
                    submitable = false;
                }
            });

            if (!submitable) {
                errors.push("Fill required fields!");
            }

            if ($(":input[name='user-email']").val() && !isValidEmail($(":input[name='user-email']").val())) {
                $(":input[name='user-email']").addClass("error");
                submitable = false;
                errors.push("Email is not valid!");
            }

            if ($(":input[name='user-password']").val() && $(":input[name='user-password']").val().length < 6) {
                $(":input[name='user-password']").addClass("error");
                submitable = false;
                errors.push("User password must be at least 6 character length!");
            }

            if (!submitable) {
                $(".form-errors", form).html("");

                for (var i=0; i<errors.length; i++) {
                    $(".form-errors", form)
                        .append("<div><i class='mdi mdi-close-circle'></i> "+errors[i]+"</div>");
                }

                $("html, body").animate({
                    scrollTop: 0
                }, 200, function() {
                    $(".form-errors", form).fadeIn(1000);
                });
            } else {
                $("body").addClass('on-progress');

                $.ajax({
                    url: form.attr("action"),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: "install",
                        key: $(":input[name='key']").val(),
                        db_host: $(":input[name='db-host']").val(),
                        db_name: $(":input[name='db-name']").val(),
                        db_username: $(":input[name='db-username']").val(),
                        db_password: $(":input[name='db-password']").val(),

                        google_map_key: $(":input[name='google-map-key']").val(),
                        fcm_key: $(":input[name='fcm-key']").val(),
                        admin_path: $(":input[name='admin-path']").val(),

                        user_username: $(":input[name='user-username']").val(),
                        user_name: $(":input[name='user-name']").val(),
                        user_email: $(":input[name='user-email']").val(),
                        user_password: $(":input[name='user-password']").val(),
                        user_timezone: $(":input[name='user-timezone']").val(),


                        crypto_key: $(":input[name='crypto-key']").val(),


                    },
                    error: function(xhr, ajaxOptions, thrownError) {

                        console.log(xhr);
                        $("body").removeClass('on-progress');

                        $(".form-errors", form)
                            .html("<div><span class='mdi mdi-close-circle'></span> Unexpected error occured!</div>");
                        $("html, body").animate({
                            scrollTop: 0
                        }, 200, function() {
                            $(".form-errors", form).fadeIn(1000);
                        });
                    },

                    success: function(resp) {
                        if (resp.result != 1) {
                            $(".form-errors", form)
                                .html("<div><span class='mdi mdi-close-circle'></span> "+resp.msg+"</div>");
                            $("html, body").animate({
                                scrollTop: 0
                            }, 200, function() {
                                $(".form-errors", form).fadeIn(1000);
                            });
                        } else {
                            var nextstep = $("#success");

                            $(".step").stop(true, true).hide();
                            $("header").hide();

                            $("html, body").animate({
                                scrollTop: 0
                            }, 200, function() {
                                nextstep.stop(true, true).fadeIn(1000);
                            });

                            $("#userInfos").html(resp.msg);
                            $("#redirect").attr("href",resp.redirect);
                        }

                        $("body").removeClass('on-progress');
                    }
                });
            }

            return false;
        })
    }


/* FUNCTIONS */

/**
 * Validate email
 * @param  {String}  email 
 * @return {Boolean}       
 */
function isValidEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
