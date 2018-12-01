	function contactOwnerSubmit(e){
		e.preventDefault();

		var $form = jQuery("#"+e.target.id);
		var $inputs = $form.find('input, textarea');
		var $submitButton = $form.find("button.contact-owner-send");
		var $loader = $form.find("i.fa-refresh");
		$loader.fadeIn('slow');
		var $messages = $form.find('.messages');
		var mailCheck = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		var mailParsed = $form.find('.email').val();
		// validate form data
			var passedInputs = 0;
			// check for empty inputs -- all inputs must be filled
			$inputs.each(function(){
				var inputValue = jQuery(this).val();
				if(inputValue !== ""){
					passedInputs = passedInputs + 1;
				}
			});
			// check for email field -- must be a valid email form

			//check if captcha is turned on
			if($form.find('.input-container.captcha-check').length){
				var $captchaContainer = $form.find('.input-container.captcha-check');
				var data = {"captcha-check": $captchaContainer.find(".user-captcha").val(), "captcha-hash": $captchaContainer.find(".rand-captcha").val()};
				ait.ajax.post("contact-owner-captcha:check", data).done(function(rdata){
					if(rdata.data == true){
						//captcha is OK
						if(passedInputs == $inputs.length && mailCheck.test(mailParsed) ){
							// ajax post -- if data are filled
							var data = {};
							$inputs.each(function(){
								data[jQuery(this).attr('name')] = jQuery(this).val();
							});
							//disable send button
							$submitButton.attr('disabled', true);
							//send email
							ait.ajax.post('contact-owner:send', data).done(function(data){
								if(data.success == true){
									$messages.find('.message-success').fadeIn('fast').delay(3000).fadeOut("fast", function(){
										//regenerate captcha
										regenerateCaptcha($captchaContainer);
										jQuery.colorbox.close();
										$form.find('input[type=text], textarea').each(function(){
											jQuery(this).attr('value', "");
										});
										$submitButton.removeAttr('disabled');
									});
									$loader.fadeOut('slow');
								} else {
									$messages.find('.message-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
									$submitButton.removeAttr('disabled');
									//regenerate captcha
									regenerateCaptcha($captchaContainer);
									$loader.fadeOut('slow');
								}
								
							}).fail(function(){
								$messages.find('.message-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
								$submitButton.removeAttr('disabled');
								//regenerate captcha
								regenerateCaptcha($captchaContainer);
								$loader.fadeOut('slow');
							});
							// display result based on response data
						} else {
							// display bad message result
							$messages.find('.message-error-user').fadeIn('fast').delay(3000).fadeOut("fast");
							//regenerate captcha
							regenerateCaptcha($captchaContainer);
							$loader.fadeOut('slow');
						}

					} else {
						//captcha check failed
						// display bad message result
						$messages.find('.message-error-user').fadeIn('fast').delay(3000).fadeOut("fast");
						//regenerate captcha
						regenerateCaptcha($captchaContainer);
						$loader.fadeOut('slow');

					}
				}).fail(function(rdata){
					//captcha ajax failed
					$messages.find('.message-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
					$submitButton.removeAttr('disabled');
					$loader.fadeOut('slow');
				});
			
			}else{
			
				//no captcha used, send mail

				if(passedInputs == $inputs.length && mailCheck.test(mailParsed) ){
					// ajax post -- if data are filled
					var data = {};
					$inputs.each(function(){
						data[jQuery(this).attr('name')] = jQuery(this).val();
					});
					//disable send button
					$submitButton.attr('disabled', true);
					ait.ajax.post('contact-owner:send', data).done(function(data){
						if(data.success == true){
							$messages.find('.message-success').fadeIn('fast').delay(3000).fadeOut("fast", function(){
								jQuery.colorbox.close();
								$form.find('input[type=text], textarea').each(function(){
									jQuery(this).attr('value', "");
								});
								$submitButton.removeAttr('disabled');
							});
						} else {
							$messages.find('.message-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
							$submitButton.removeAttr('disabled');
						}
						$loader.fadeOut('slow');
					}).fail(function(){
						$messages.find('.message-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
						$submitButton.removeAttr('disabled');
						$loader.fadeOut('slow');
					});
					// display result based on response data
				} else {
					// display bad message result
					$messages.find('.message-error-user').fadeIn('fast').delay(3000).fadeOut("fast");
					$loader.fadeOut('slow');
				}
			}

	}
	
	function regenerateCaptcha( $captchaContainer ) {
		/* new captcha */
		if($captchaContainer.find('img').length > 0){
			var $captchaImage = $captchaContainer.find('img');
			$captchaImage.fadeTo("slow", 0);
			// ajax load new captcha
			ait.ajax.get('contact-owner-captcha:getCaptcha', null).done(function(xhr){
					$captchaContainer.find('input.rand-captcha').val(xhr.data.rand);
					var $imageUrl = xhr.data.url;
					$captchaImage.attr('src', $imageUrl);
					$captchaImage.fadeTo("slow", 1);
			}).fail(function(){
				console.error("get captcha failed");
			});
		}
		/* new captcha */
	}
