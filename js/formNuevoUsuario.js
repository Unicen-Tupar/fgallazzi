$(document).ready(function() {

	var options = {
		url : "index.php",
		type : "POST",
		dataType : "json",
		data : {action:"alta_nuevo_usuario_by_ajax"},
		success : function(data){
			if ('success' in data && data.success){
				$('#modal-nuevo-usuario').modal('hide');
				FORM_LOGIN_TRASTOS.show();
			}else{
				if ('errorMsj' in data){
					SHOW_ERRORS.show('#form_nuevo_usuario',data.errorMsj);

				}
				if ('info' in data){
					$('#modal-nuevo-usuario').modal('hide');
					DIALOG.show(data.info);
				}
			}
		},
		beforeSubmit : function(){
			SHOW_ERRORS.hide('#form_nuevo_usuario');
		}
	};

	$.validator.setDefaults({
	  	debug: false,
	 	success: "valid"
	});

	$( "#form_nuevo_usuario" ).validate({
		errorClass: "has-error",
		validClass: "has-success",
		errorElement : "span",
		ignore : false,
		highlight: function(element, errorClass, validClass) {
		    $(element).parents('div.marca').addClass(errorClass).removeClass(validClass);
		},
		unhighlight: function(element, errorClass, validClass) {
		    $(element).parents('div.marca').removeClass(errorClass).addClass(validClass);
		},
		submitHandler: function(form) {

			$(form).ajaxSubmit(options);
		},
	  	rules: {
	  	  	'nombre': {
	  	    	required: true,
	  	    	maxlength: 50
	  	  	},
	  	  	'apellido': {
	  	    	required: true,
	  	    	maxlength: 50
	  	  	},
	  	  	'email': {
	  	    	required: true,
	  	    	maxlength: 100
	  	  	},
	  	  	'telefono': {
	  	    	required: true,
	  	    	maxlength: 50
	  	  	},
	  	  	'password': {
	  	    	required: true,
	  	    	minlength : 6,
	  	    	maxlength: 50
	  	  	},
	  	  	'passwordConfirm': {
	  	    	required: true,
	  	    	minlength : 6,
	  	    	maxlength : 50
	  	  	}
	  	},
	  	messages : {
	  		'nombre': {
	  	    	required: "Este dato es obligatorio",
	  	    	maxlength: "Maximo de 50 caracteres"
	  	  	},
	  	  	'apellido': {
	  	    	required: "Este dato es obligatorio",
	  	    	maxlength: "Maximo de 50 caracteres"
	  	  	},
	  	  	'email': {
	  	    	required: "Este dato es obligatorio",
	  	    	maxlength: "Maximo de 70 caracteres"
	  	  	},
	  	  	'telefono': {
	  	    	required: "Este dato es obligatorio",
	  	    	maxlength: "Maximo de 50 caracteres"
	  	  	},
	  	  	'password': {
	  	    	required: "Este dato es obligatorio",
	  	    	maxlength: "Maximo de 50 caracteres",
	  	    	minlength: "Minimo 6 caracteres"
	  	  	},
	  	  	'passwordConfirm': {
	  	    	required: "Por favor indique su contraseña",
	  	    	maxlength: "Maximo de 50 caracteres",
	  	    	minlength: "Minimo 6 caracteres"
	  	  	}
	  	}
	});

	$('#form_nuevo_usuario').ajaxForm(options);

	
});	

