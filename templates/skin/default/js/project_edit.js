
(function($){
    jQuery.fn.lightTabs = function(options){
        var createTabs = function(){
            tabs = this;
            i = 0;

            showPage = function(i){
                $(tabs).children("div").children("div").hide();
                $(tabs).children("div").children("div").eq(i).show();
                $(tabs).children("ul").children("li").removeClass("active");
                $(tabs).children("ul").children("li").eq(i).addClass("active");
            }

            showPage(0);

            $(tabs).children("ul").children("li").each(function(index, element){
                $(element).attr("data-page", i);
                i++;
            });

            $(tabs).children("ul").children("li").click(function(){
                showPage(parseInt($(this).attr("data-page")));
            });
        };
        return this.each(createTabs);
    };
})(jQuery);

$(document).ready(function() {

    $("button.js_c_button_save_changes", ".js_c_form_edit").bind("click", function(){
        // Если уже была нажата какая-то кнопка
        if($("button.js_c_button_save_changes.clicked", ".js_c_form_edit").length) {
            return false;
        }

        if(!$(this).hasClass("clicked")) {
            $(this).addClass("clicked");
        }
    });

    // Click on tab
    $(".js_c_tab_li").bind("click", function (e) {
        e.preventDefault();
        // Remove all mesages
        $(".js_c_submit_mes_success").html("&nbsp;").hide();
        $(".js_c_submit_mes_error").html("&nbsp;").hide();
    });

    /* **************************************************************/
    /* ******************* START - FORM SUBMIT  *********************/
    /* **************************************************************/

    isProcessForm = false;

    $(".js_c_form_edit").submit(function(){

        // Check if the form is being processed now
        if(isProcessForm) {
            return false;
        }

        isProcessForm = true;

        var obThisForm = $(this);

        // Нажатая кнопка
        var obSaveButton = $("button.js_c_button_save_changes.clicked", ".js_c_form_edit");

        /* *************** START - CHECK FIELDS ***************** */

        if(obSaveButton.data("tab_name") == "main") {
            // Проверяем поля вкладки "Основные данные"
            var obCheckMain = checkTabMain();
            if(obCheckMain.error) {
                $("html, body").animate({
                    scrollTop: $(".js_c_field_projectname", ".js_c_form_main").offset().top
                }, 600);
                $("button.js_c_button_save_changes.clicked", ".js_c_form_main").removeClass("clicked");
                isProcessForm = false;
                return false;
            }
        }

        if(obSaveButton.data("tab_name") == "detail") {
            // Проверяем поля вкладки "Детали"
            var obCheckDetail = checkTabDetail();
            if(obCheckDetail.error) {
                $("html, body").animate({
                    scrollTop: $(".js_c_field_videolink", ".js_c_form_detail").offset().top
                }, 600);
                $("button.js_c_button_save_changes.clicked", ".js_c_form_detail").removeClass("clicked");
                isProcessForm = false;
                return false;
            }
        }



        /* *************** END - CHECK FIELDS ***************** */

        /* *************** START - AJAX SUBMIT ***************** */

        obSaveButton.attr("disabled", "disabled");

        var obImgLoader = obSaveButton.next("img.js_c_step_loader");
        obImgLoader.show(); // show image loader

        var obMesSuccess = $(".js_c_submit_mes_success.top").html("&nbsp;").hide();
        var obMesError = $(".js_c_submit_mes_error.top").html("&nbsp;").hide();

        obThisForm.ajaxSubmitNew({
            dataType: "json",
            success: function(data){

                if(data.res == 'ok') {

                    // show success
                    obMesError.html("&nbsp;").hide();
                    if (data.mes_success != undefined) {
                        obMesSuccess.html(data.mes_success).show();
                        $("html, body").animate({
                            scrollTop: obMesSuccess.offset().top
                        }, 600);
                    }

                    // Hide Save Button
                    obSaveButton.removeAttr("disabled").removeClass("clicked");

                    if (obSaveButton.data("tab_name") == "main"
                        && data.product_url != undefined && data.product_url_full != undefined
                        ) {
                        $(".js_c_field_product_url", obThisForm).val(data.product_url);
                        $("a.js_c_link_detail_page").attr("href", data.product_url_full)
                    }

                } else {
                    // show error
                    obMesSuccess.html("&nbsp;").hide();
                    if (data.mes_error != undefined) {
                        obMesError.html(data.mes_error).show();
                        $("html, body").animate({
                            scrollTop: obMesError.offset().top
                        }, 600);
                    }

                    // Enable save Button
                    obSaveButton.removeAttr("disabled").removeClass("clicked");
                }

                obImgLoader.hide();
                $(".js_c_save_error").html(""); // remove inner tab messages messages
                isProcessForm = false;
            },
            error: function(data){
                obImgLoader.hide();
                obSaveButton.removeAttr("disabled").removeClass("clicked");

                obMesSuccess.html("&nbsp;").hide();
                obMesError.html("Попробуйте сохранить снова").show();
                $("html, body").animate({
                    scrollTop: obMesError.offset().top
                }, 600);

                $(".js_c_save_error").html(""); // remove inner tab messages messages
                isProcessForm = false;
            }
        });

        /* *************** END - AJAX SUBMIT ***************** */

        return false;
    });

    /* **************************************************************/
    /* ******************* END - FORM SUBMIT  *********************/
    /* **************************************************************/

    function checkTabMain() {

        var error = false;

        var obForm = $(".js_c_form_main");

        var obName = $(".js_c_field_projectname", obForm);
        var val = obName.val();
        if (val == '') {
            error = true;
            $(".js_c_err_projectname", obForm).text("Поле не заполнено").show();
            obName.addClass("field_border_error");
        } else if (!/^[A-Za-zА-Яа-яЁё0-9_"\s-]+$/.test(val)) {
            error = true;
            $(".js_c_err_projectname", obForm).text("Введены недопустимые символы").show();
            obName.addClass("field_border_error");
        } else {
            $(".js_c_err_projectname", obForm).text("").hide();
            obName.removeClass("field_border_error");
        }

        var obPreviewtext = $(".js_c_field_previewtext", obForm);
        var val = $.trim(obPreviewtext.val());
        if (val == '') {
            error = true;
            $(".js_c_err_previewtext", obForm).text("Поле не заполнено").show();
            obPreviewtext.addClass("field_border_error");
        } else {
            $(".js_c_err_previewtext", obForm).text("").hide();
            obPreviewtext.removeClass("field_border_error");
        }

        var obSum = $(".js_c_field_sum", obForm);
        var val = $.trim(obSum.val());
        if (val == '') {
            error = true;
            $(".js_c_err_sum", obForm).text("Поле не заполнено").show();
            obSum.addClass("field_border_error");
        } else if (!/^[\d]+$/.test(val)) {
            error = true;
            $(".js_c_err_sum", obForm).text("Введены недопустимые символы").show();
            obSum.addClass("field_border_error");
        } else {
            $(".js_c_err_sum", obForm).text("").hide();
            obSum.removeClass("field_border_error");
        }

        var obDateuntil = $(".js_c_field_dateuntil", obForm);
        var val = $.trim(obDateuntil.val());
        if (val == '') {
            error = true;
            $(".js_c_err_dateuntil", obForm).text("Поле не заполнено").show();
            obDateuntil.addClass("field_border_error");
        } else if (!/^[\d]{4}-[\d]{2}-[\d]{2}$/.test(val)) {
            error = true;
            $(".js_c_err_dateuntil", obForm).text("Введены недопустимые символы").show();
            obDateuntil.addClass("field_border_error");
        } else {
            $(".js_c_err_dateuntil", obForm).text("").hide();
            obDateuntil.removeClass("field_border_error");
        }

        var obProductUrl = $(".js_c_field_product_url", obForm);
        var val = $.trim(obProductUrl.val());
        if (val == '') {
            error = true;
            $(".js_c_err_product_url", obForm).text("Поле не заполнено").show();
            obProductUrl.addClass("field_border_error");
        } else if (!/^[A-Za-z0-9_-]{3,}$/.test(val)) {
            error = true;
            $(".js_c_err_product_url", obForm).text("Введены недопустимые символы или длина поля неверна").show();
            obProductUrl.addClass("field_border_error");
        } else {
            $(".js_c_err_product_url", obForm).text("").hide();
            obProductUrl.removeClass("field_border_error");
        }

        var obCategselect = $('.js_c_field_categselect');
        var category_id = obCategselect.val();

        if (category_id > 0) {
            $(".js_c_err_categselect", obForm).text("").hide();
            obCategselect.removeClass("field_border_error");
        } else {
            error = true;
            $(".js_c_err_categselect", obForm).text("Поле не заполнено").show();
            obCategselect.addClass("field_border_error");
        }

        return {"error" : error};
    }

    function checkTabDetail() {

        var error = false;

        var obForm = $(".js_c_form_detail");

        var obVideolink = $(".js_c_field_videolink", obForm);
        var val = obVideolink.val();
        if (val != '') {
            if (!/^https:\/\/www\.youtube\.com\/watch\?v=[a-zA-Z0-9‌​_\-]{11}$/.test(val)) {
                error = true;
                $(".js_c_err_videolink", obForm).text("Введены недопустимые символы").show();
                obVideolink.addClass("field_border_error");
            } else {
                $(".js_c_err_videolink", obForm).text("").hide();
                obVideolink.removeClass("field_border_error");
            }
        } else {
            $(".js_c_err_videolink", obForm).text("").hide();
            obVideolink.removeClass("field_border_error");
        }

        /*
         var obDetailtext = $(".js_c_field_detailtext", obForm);
         var val = $.trim(obDetailtext.val());
         if (val == '') {
         error = true;
         $(".js_c_err_detailtext", obForm).text("Поле не заполнено").show();
         obDetailtext.addClass("field_border_error");
         } else {
         $(".js_c_err_detailtext", obForm).text("").hide();
         obDetailtext.removeClass("field_border_error");
         }
         */

        try {
            var detailtextData = editor_detailtext.getData();
            $(".js_c_field_detailtext", obForm).val(detailtextData);
        } catch (e) {}

        return {"error" : error};
    }

    var editor_detailtext = CKEDITOR.replace(
        new CKEDITOR.dom.element(document.getElementById("js_id_field_detailtext")),
        {
            extraPlugins: 'uploadimage,image2,youtube',
            height: 300,
            removePlugins: 'iframe',
            toolbar : [
                {name: 'text', items: ["Bold", "Italic", "Underline"]},
                {name: 'insert', items: ["Image", "Youtube"]},
                {name: 'justify', items: ["JustifyLeft", "JustifyCenter", "JustifyRight"]}
            ],
            filebrowserImageUploadUrl: '/sc_images/ajax-upload-editor-images/?security_ls_key='+LIVESTREET_SECURITY_KEY+'&target_id='+GF_PROJECT_ID,
            image2_alignClasses: [ 'image-align-left', 'image-align-center', 'image-align-right' ],
            image2_disableResizer: true
        }
    );
});
