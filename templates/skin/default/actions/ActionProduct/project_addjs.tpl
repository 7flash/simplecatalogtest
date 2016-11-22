{literal}
<script>

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

	/* **************************************************************/
	/* ******************* START - FORM SUBMIT  *********************/
	/* **************************************************************/

	console.log(555);


	isProcessForm = false;

	$(".js_c_form_edit").submit(function(e){

		e.preventDefault();

		console.log(222);
		// Check if the form is being processed now
		if(isProcessForm) {
			return false;
		}

		isProcessForm = true;

		var obThisForm = $(this);

		// Нажатая кнопка
		var obSaveButton = $("button.js_c_button_save_changes.clicked", ".js_c_form_edit");


		return false;

		/* *************** START - CHECK FIELDS ***************** */

		if(obSaveButton.data("tab_name") == "main") {

			/**
			// Проверяем поля вкладки "Основные данные"
			var obCheckProfile = checkProfile();

			if(obCheckProfile.error) {

				$("html, body").animate({
					scrollTop: $("#js_form_player_name").offset().top
				}, 600);

				$("button.js_c_button_save_changes.clicked", "#js_form_player").removeClass("clicked");

				isProcessForm = false;
				return false;
			}
			*/
		}

		if(obSaveButton.data("tab_name") == "passport") {
			/*
			// Проверяем поля вкладки "Паспортные данные"
			var obCheckPassport = checkPassport();

			if (obCheckPassport.error) {
				$("button.js_c_button_save_changes.clicked", "#js_form_player").removeClass("clicked");
				isProcessForm = false;
				return false;
			}
			*/
		}

		/* *************** END - CHECK FIELDS ***************** */

		/* *************** START - AJAX SUBMIT ***************** */

		obSaveButton.attr("disabled", "disabled");

		var obImgLoader = obSaveButton.next("img.js_c_step_loader");
		obImgLoader.show(); // show image loader

		var obMesSuccess = $(".js_c_submit_mes_success.top").html("&nbsp;").hide();
		var obMesError = $(".js_c_submit_mes_error.top").html("&nbsp;").hide();

		/**
		// Адрес сабмита формы
		var submit_url = "";
		if(obSaveButton.data("tab_name") == "profile") {
			submit_url = "/player/warface/save_profile/";
		} else if(obSaveButton.data("tab_name") == "gamenick") {
			submit_url = "/player/warface/save_nick/";
		} else if(obSaveButton.data("tab_name") == "passport") {
			submit_url = "/player/warface/save_passport/";
		}
		*/

		obThisForm.ajaxSubmit({
			dataType: "json",
			//url: submit_url,
			success: function(data){

				if(data.res == 'ok') {

					// Убрать у всех элементов класс js_c_changed в нутри вкладки, где нажата кнопка "Сохранить изменения"
					//$(".js_c_changed", "#player_" + obSaveButton.data("tab_name")).removeClass("js_c_changed");

					// show success
					obMesError.html("&nbsp;").hide();
					if (data.mes_success != undefined) {
						obMesSuccess.html(data.mes_success).show();
						$("html, body").animate({
							scrollTop: obMesSuccess.offset().top
						}, 600);
					}

					// Hide Save Button
					//obSaveButton.removeAttr("disabled").removeClass("clicked").removeClass("js_c_button_visible");
					obSaveButton.removeAttr("disabled").removeClass("clicked");

					// Remove massage - not save changed fields
					//$("div.js_c_notsave_error", "#js_form_player").html("&nbsp;");

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

});



function checkTabMain() {

	var error = false;

	var addUrlParams = "";

	// Никнейм на сайте
	var obName = $(".js_field_projectname", ".js_c_form_main");
	var name = obName.val();

	if (name == '') {
		error = true;
		$("#js_player_err_name").text("Поле не заполнено").show();
		obName.addClass("field_border_error");
	} else if (!/^[A-Za-zА-Яа-яЁё0-9_"\s-]+$/.test(name)) {
		error = true;
		$("#js_player_err_name").text("Введены недопустимые символы").show();
		obName.addClass("field_border_error");
	} else {
		$("#js_player_err_name").text("").hide();
		obName.removeClass("field_border_error");
	}

	/*
	addUrlParams = addUrlParams + "&form[nick]=" + name;

	var obCountry = $('#js_form_player_idCountry');
	var idCountry = obCountry.val();

	if (idCountry == '') {
		error = true;
		$("#js_player_err_idCountry").text("Поле не заполнено").show();
		obCountry.parent("div.controls").addClass("field_border_error");
	} else {
		$("#js_player_err_idCountry").text("").hide();
		obCountry.parent("div.controls").removeClass("field_border_error");
	}
	*/
	addUrlParams = addUrlParams + "&form[idCountry]=" + idCountry;

	return {"error" : error, "addUrlParams" : addUrlParams};
}

</script>
{/literal}