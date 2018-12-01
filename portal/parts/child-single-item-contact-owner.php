{var $disabled = 'yes'}

{* SETTINGS AND DATA *}
	{var $settings = $options->theme->item}
{* SETTINGS AND DATA *}

{if $meta->contactOwnerBtn && $meta->email}
	{var $disabled = ''}
{/if}
<?php $layout_type = str_replace(chr(34), '', $layout); ?>
<div n:class="contact-owner-container, $disabled ? contact-owner-disabled">

	{if !$disabled}
	<a href="#contact-owner-popup-form-<?php echo $layout_type  ?>-<?php echo $item->id ?>" id="contact-owner-popup-button-<?php echo $layout_type  ?>-{$item->id}" class="contact-owner-popup-button">{$settings->contactOwnerButtonTitle|trimWords:10}</a>
	<div class="contact-owner-popup-form-container" style="display: none">

		<form id="contact-owner-popup-form-<?php echo $layout_type  ?>-{$item->id}" class="contact-owner-popup-form" onSubmit="javascript:contactOwnerSubmit(event);">
			<h3>{$settings->contactOwnerButtonTitle}</h3>
			<input type="hidden" name="response-email-address" value="{$meta->email}">
			<input type="hidden" name="response-email-content" value="{$settings->contactOwnerMailForm}">
			{if $settings->contactOwnerMailFromName}
			<input type="hidden" name="response-email-sender-name" value="{$settings->contactOwnerMailFromName}">
			{/if}

			{if $settings->contactOwnerMailFromEmail}
			<input type="hidden" name="response-email-sender-address" value="{$settings->contactOwnerMailFromEmail}">
			{else}
			<input type="hidden" name="response-email-sender-address" value="{get_option('admin_email')}">
			{/if}
			
			<div class="input-container">
				<input type="text" class="input name" name="user-name" value="" placeholder="{$settings->contactOwnerInputNameLabel}" id="user-name-<?php echo $layout_type  ?>-{$item->id}">
				{if isset($settings->contactOwnerInputNameHelper) && $settings->contactOwnerInputNameHelper != ""}
					<span class="input-helper">{!$settings->contactOwnerInputNameHelper}</span>
				{/if}
			</div>

			<div class="input-container">
				<input type="text" class="input email" name="user-email" value="" placeholder="{$settings->contactOwnerInputEmailLabel}" id="user-email-<?php echo $layout_type  ?>-{$item->id}">
				{if isset($settings->contactOwnerInputEmailHelper) && $settings->contactOwnerInputEmailHelper != ""}
					<span class="input-helper">{!$settings->contactOwnerInputEmailHelper}</span>
				{/if}
			</div>

			<div class="input-container">
				<input type="text" class="input subject" name="response-email-subject" value="" placeholder="{$settings->contactOwnerInputSubjectLabel}" id="user-subject-<?php echo $layout_type  ?>-{$item->id}">
				{if isset($settings->contactOwnerInputSubjectHelper) && $settings->contactOwnerInputSubjectHelper != ""}
					<span class="input-helper">{!$settings->contactOwnerInputSubjectHelper}</span>
				{/if}
			</div>

			<div class="input-container">
				<textarea class="user-message" name="user-message" cols="30" rows="4" placeholder="{$settings->contactOwnerInputMessageLabel}" id="user-message-<?php echo $layout_type  ?>-{$item->id}"></textarea>
				{if isset($settings->contactOwnerInputMessageHelper) && $settings->contactOwnerInputMessageHelper != ""}
					<span class="input-helper">{!$settings->contactOwnerInputMessageHelper}</span>
				{/if}
			</div>

			{*CAPTCHA*}
			{if $settings->contactOwnerCaptcha && class_exists("AitReallySimpleCaptcha") }
				{var $captcha = new AitReallySimpleCaptcha() }
				{var $captcha->tmp_dir = aitPaths()->dir->cache . '/captcha' }
				{var $cacheUrl = aitPaths()->url->cache . '/captcha' }
				{var $rand = rand() }
				{var $img = $captcha->generate_image('ait-contact-owner-captcha-'.$rand, $captcha->generate_random_word()) }
				{var $imgUrl = $cacheUrl . "/" . $img }
				<div class="input-container captcha-check">
					<img src="{$imgUrl}" alt="captcha-input"/>
					<input type="text" class="input user-captcha" name="user-captcha" value="" placeholder="{$settings->contactOwnerInputCaptchaLabel}">
					<input type="hidden" class="rand-captcha" name="rand" value="{$rand}" />
				</div>
			
			{/if}

			<div class="input-container btn">
				<button class="contact-owner-send" type="submit">{$settings->contactOwnerSendButtonLabel}</button>
				<i class="fa fa-refresh fa-spin" style="margin-left: 10px; display: none;"></i>
			</div>

			<div class="messages">
				<div class="message message-success" style="display: none">{$settings->contactOwnerMessageSuccess}</div>
				<div class="message message-error-user" style="display: none">{$settings->contactOwnerMessageErrorUser}</div>
				<div class="message message-error-server" style="display: none">{$settings->contactOwnerMessageErrorServer}</div>
			</div>
		</form>

	</div>
	<script type="text/javascript" n:syntax="off">
	jQuery(document).ready(function(){
		jQuery("#contact-owner-popup-button-<?php echo $layout_type  ?>-<?php echo $item->id ?>").colorbox({ inline:true, href:"#contact-owner-popup-form-<?php echo $layout_type  ?>-<?php echo $item->id ?>" });
	});
	</script>
	{else}
	<a href="#contact-owner-popup-form-<?php echo $layout_type  ?>-<?php echo $item->id ?>" id="contact-owner-popup-button-<?php echo $layout_type  ?>-{$item->id}" class="contact-owner-popup-button">{$settings->contactOwnerButtonDisabledTitle|trimWords:10}</a>
	{/if}
</div>
