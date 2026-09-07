$( document ).ready(function() {
	$("body").on("click", ".viewdecliened", function(e){
		var id = $(this).attr("invid");
		investagtionid = $(this).attr("did");
	
		var url = baseURL + "Frontoffice/getdeclinedbyid?id="+id;
		$.ajax({
			async: true,
			dataType: "html",
			url: url,
			type: "GET",
			success: function (data) {
				$("#declinedlistModal").modal("show");
				$("#declinedlistModal .modal-content").html(data);
			},
		});
		
	});
});



(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "./jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

( function() {
	$.validator.addMethod("valueNotEquals", function(value, element, arg){
		    	  return arg !== value;
	}, "Select");

	$.validator.addMethod("ageValidator", function(value, element, arg){
		if(Number(value) >0){ return true; }else{ return false;} 
	}, "Age should be greater than 0");

	$.validator.addMethod("phoneInd", function(value, element, arg){
		var regexPattern=new RegExp(/((\+*)((0[ -]+)*|(91 )*)(\d{12}|\d{10}))|\d{5}([- ]*)\d{6}/);    // regular expression pattern
		return regexPattern.test(value);
	}, "Invalid Phone Number");

	$.validator.addMethod("aadharValidate", function(value, element, arg){
		var regexPattern=new RegExp(/^[2-9]{1}[0-9]{3}\s{1}[0-9]{4}\s{1}[0-9]{4}$/);    // regular expression pattern
		return regexPattern.test(value);
	}, "Invalid Aadhar Number");
	
	$.validator.addMethod("emailValidate", function(value, element, arg){
		if(value == ""){ return true;
			    }else{
		    		var regexPattern=new RegExp(/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i);    // regular expression pattern
			    	return regexPattern.test(value);
			    }
	}, "Invalid Email");
}() );
}));

