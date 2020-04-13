/**
 * Used in form UI validation. If a field is empty, then highlight that specific
 * field with border in red and show a message as required else if the field is
 * not empty then highlight with green border.
 * 
 * @param {object}
 *            messageElement The element where the message should be shown
 * @param {object}
 *            field The input field that is to be validated
 * @return {boolean} true if value present or false if empty
 */
function isEmpty(messageElement, field) {
	valid = true;
	if ($("#" + field + ".required").length <= 0) {
		return true;
	}
	$("#" + messageElement).html(" ");
	$("#" + field).css("border-color", "#2ecc71");
	var fname = $("#" + field).val();
	if ($("#" + field).attr("type") == "checkbox") {
		if ($("#" + field + ":checked").length <= 0) {
			var message = $("#" + messageElement).data("required-message");
			$("#" + messageElement).html(message);
			$("#" + field).css("border-color", "#E46B66");
			valid = false;
		}
	} else {
		if (fname == "") {
			var message = $("#" + messageElement).data("required-message");
			$("#" + messageElement).html(message);
			$("#" + field).css("border-color", "#E46B66");
			valid = false;
		}
	}
	return valid;
}

/**
 * Validates if the input value is in email format. If false, shows a red border
 * around the input field with an error message.
 * 
 * @param {object}
 *            messageElement The element where the message should be shown
 * @param {object}
 *            field The input field that is to be validated
 * @return {boolean} true if in email format else false
 */
function isValidEmailFormat(messageElement, field) {
	valid = true;
	var emailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var email = $("#" + field).val();
	if ($("#" + field + ".required").length <= 0 && email == "") {
		return true;
	}
	$("#" + messageElement).html(" ");
	$("#" + field).css("border-color", "#2ecc71");
	if (!emailRegex.test(email)) {
		var message = $("#" + messageElement).data("validate-message");
		$("#" + messageElement).html(message);
		$("#" + field).css("border-color", "#E46B66");
		valid = false;
	}
	return valid;
}
/**
 * Facade function to validate the email field, checks for both empty and valid
 * format.
 * 
 * @returns {boolean} true on pass else fail.
 */
function validateEmail() {
	var valid = true;
	valid = (isEmpty("email-info", "pp-email"))
			&& (isValidEmailFormat("email-info", "pp-email"));
	return valid;
}

/**
 * validates the complete form fields. this is the entry function called when
 * AJAX submit is done. If you want to add more field/validation, this is the
 * place to do.
 * 
 * @returns {boolean} true on pass else fail.
 */
function validate() {
	var valid = true;
	var nameValid = true;
	var emailValid = true;

	$("input").removeClass("error-field");
	$("textarea").removeClass("error-field");

	nameValid = isEmpty("name-info", "pp-name");
	if (nameValid == false) {
		$("#pp-name").addClass("error-field");
	}

	emailValid = validateEmail();
	if (emailValid == false) {
		$("#pp-email").addClass("error-field");
	}

	if (nameValid == false || emailValid == false) {
		valid = false;
		$(".error-field").first().focus();
	}
	return valid;
}

/**
 * AJAX entry point for form submission.
 * 
 */
$(document).ready(function(e) {
	$(".phppot-form").on('submit', (function(e) {
		e.preventDefault();
		var form = $(this).serialize();

		var valid = validate();
		if (valid == true) {
			$("#phppot-message").hide();
			$('#phppot-btn-send').hide();
			$('#phppot-loader-icon').css("display", "inline-block");
			$.ajax({
				url : "ajax/subscribe-ep.php",
				type : "POST",
				dataType : 'json',
				data : new FormData(this),
				contentType : false,
				cache : false,
				processData : false,

				success : function(response) {

					$('#phppot-btn-send').hide();
					$('#phppot-loader-icon').hide();
					$("#phppot-message").css("display", "inline-block");
					if (response.type == "message") {
						$("#phppot-message").attr("class", "success");
						$("#phppot-message").html(response.text);
					} else if (response.type == "error") {
						$('#phppot-btn-send').show();
						$("#phppot-message").attr("class", "error");
						$("#phppot-message").html(response.text);
					} else {
						$('#phppot-btn-send').show();
						$("#phppot-message").attr("class", "error");
						$("#phppot-message").html("Error." + response.text);
					}

					if (response.type == "message") {
						$('input').val('');
					}
				},

				error : function(jqXHR, errorThrown) {
					var message = jqXHR.responseText;
					$("#phppot-message").css("display", "inline-block");
					$('#phppot-loader-icon').hide();
					$('#phppot-btn-send').show();
					$("#phppot-message").attr("class", "error");
					$("#phppot-message").html(message);
				}
			});
		}
	}));

});