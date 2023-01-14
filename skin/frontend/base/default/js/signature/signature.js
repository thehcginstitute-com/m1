	function validate_signature()
	{
		if(jQuery(".signature input.output").val()!='' || jQuery(".signature input.name").val()!='')
		{
			var base_url = window.location.protocol + "//" + window.location.host + "/";
			var output = jQuery(".signature input.output").val();
			var name = jQuery(".signature input.name").val();
			var form = jQuery('#co-payment-form');
			var input_name = jQuery("<input>").attr({'type':'hidden', 'name':'signature_name'}).val(name);
			jQuery(form).append(jQuery(input_name));
			var input_output = jQuery("<input>").attr({'type':'hidden', 'name':'output'}).val(output);
			jQuery(form).append(jQuery(input_output));
			review.save();
		}
		else
		{
			alert('Signature is required field');
		}	
	}
	
	function validate_multiple_signature()
	{
		if(jQuery(".signature input.output").val()!='' || jQuery(".signature input.name").val()!='')
		{
			var output = jQuery(".signature input.output").val();
			var name = jQuery(".signature input.name").val();
			var form = jQuery('div.multiple-checkout > form');
			var input_name = jQuery("<input>").attr({'type':'hidden', 'name':'signature_name'}).val(name);
			jQuery(form).append(jQuery(input_name));
			var input_output = jQuery("<input>").attr({'type':'hidden', 'name':'output'}).val(output);
			jQuery(form).append(jQuery(input_output));
			return showLoader();
		}
		else
		{
			alert('Signature is required field');
			return false;
		}	
	}