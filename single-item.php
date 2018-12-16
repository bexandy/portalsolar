{block content}

	{loop as $post}
		{* SETTINGS AND DATA *}
		{var $meta = $post->meta('item-data')}
		{var $settings = $options->theme->item}
		{var $user_meta = get_userdata($post->author->id)}
		{var $user_roles = $user_meta->roles}

		<?php 
		foreach ($user_roles as $rol) {
			if (isThemeUserRole($rol)) {
				$authorPack = $rol;
			}			
		} 
		$isAuthorAdmin = false;
		if ( in_array( 'administrator', $user_roles, true ) ) {
    		$isAuthorAdmin = true;
		}
		?>

		{var $pack_display = get_option('br_pack_display')}
		{* SETTINGS AND DATA *}

		{*RICH SNIPPET WRAP*}
		<div class="item-content-wrap" itemscope itemtype="http://schema.org/LocalBusiness">
			<meta itemprop="name" content="{$post->title}">
			<meta itemprop="image" content="{$post->imageUrl}">
			{if  ($pack_display[$authorPack]['item-address'] || $isAuthorAdmin)}
			{if $meta->map['address']}
			<meta itemprop="address" content="{$meta->map['address']}">
			{/if}
			{/if}
		{*RICH SNIPPET WRAP*}

			{* CONTENT SECTION *}
			<div class="item-content">
				{if ($pack_display[$authorPack]['gallery'] || $isAuthorAdmin)}
					{if $meta->displayGallery && !empty($meta->gallery)}
						{* GALLERY SECTION *}
						{includePart portal/parts/single-item-gallery, meta => $meta}
						{* GALLERY SECTION *}
					{else}
						{includePart portal/parts/single-item-featured-img, meta => $meta}
					{/if}
				{/if}
				
				{if ($pack_display[$authorPack]['content'] || $isAuthorAdmin)}
				<div class="entry-content-wrap" itemprop="description">
					<div class="entry-content">
					{if $post->hasContent}
						{!$post->content}
					{else}
						{!$post->excerpt}
					{/if}
					</div>
				</div>
				{/if}

				{if ($pack_display[$authorPack]['item-features'] || $isAuthorAdmin)}
					{if (!$meta->displayGallery || ($meta->displayGallery && (empty($meta->gallery) && !$post->hasImage)))}
						{* FEATURES SECTION *}
						{includePart portal/parts/single-item-features}
						{* FEATURES SECTION *}
					{/if}
				{/if}
				

			</div>
			{* CONTENT SECTION *}


			<div class="column-grid column-grid-3">
				<div class="column column-span-1 column-narrow column-first">
					{if ($pack_display[$authorPack]['opening-hours'] || $isAuthorAdmin)}
					{* OPENING HOURS SECTION *}
					{includePart portal/parts/single-item-opening-hours}
					{* OPENING HOURS SECTION *}
					{/if}
				</div>

				<div class="column column-span-2 column-narrow column-last">
					{if ($pack_display[$authorPack]['item-address'] || $isAuthorAdmin)}
					{* ADDRESS SECTION *}
					{includePart portal/parts/single-item-address}
					{* ADDRESS SECTION *}
					{/if}

					{if ($meta->contactOwnerBtn and $meta->email) or (defined('AIT_GET_DIRECTIONS_ENABLED'))}
					<div class="contact-buttons-container">
					{if ($pack_display[$authorPack]['contact-owner'] || $isAuthorAdmin)}
					{* CONTACT OWNER SECTION *}
					{includePart portal/parts/single-item-contact-owner}
					{* CONTACT OWNER SECTION *}
					{/if}

					{if ($pack_display[$authorPack]['get-directions'] || $isAuthorAdmin)}
					{* GET DIRECTIONS SECTION *}
					{if defined('AIT_GET_DIRECTIONS_ENABLED')}
						{includePart portal/parts/get-directions-button}
					{/if}
					{* GET DIRECTIONS SECTION *}
					{/if}

					</div>
					{/if}
				</div>
			</div>

			{if ($pack_display[$authorPack]['item-extension'] || $isAuthorAdmin)}
			{* ITEM EXTENSION *}
			{if defined('AIT_EXTENSION_ENABLED')}
				{includePart portal/parts/item-extension}
			{/if}
			{* ITEM EXTENSION *}
			{/if}

			{if ($pack_display[$authorPack]['claim-listing'] || $isAuthorAdmin)}
			{* CLAIM LISTING SECTION *}
			{if defined('AIT_CLAIM_LISTING_ENABLED')}
				{includePart portal/parts/claim-listing}
			{/if}
			{* CLAIM LISTING SECTION *}
			{/if}

			{if ($pack_display[$authorPack]['map'] || $isAuthorAdmin)}
			{* MAP SECTION *}
			{includePart portal/parts/single-item-map}
			{* MAP SECTION *}
			{/if}

			{if ($pack_display[$authorPack]['get-directions'] || $isAuthorAdmin)}
			{* GET DIRECTIONS SECTION *}
			{if defined('AIT_GET_DIRECTIONS_ENABLED')}
				{includePart portal/parts/get-directions-container}
			{/if}
			{* GET DIRECTIONS SECTION *}
			{/if}
			
			{if ($pack_display[$authorPack]['item-social'] || $isAuthorAdmin)}
			{* SOCIAL SECTION *}
			{includePart portal/parts/single-item-social}
			{* SOCIAL SECTION *}
			{/if}

			{if ($pack_display[$authorPack]['reviews'] || $isAuthorAdmin)}
			{* REVIEWS SECTION *}
			{if defined('AIT_REVIEWS_ENABLED')}
			{includePart portal/parts/single-item-reviews}
			{/if}
			{* REVIEWS SECTION *}
			{/if}

			{if ($pack_display[$authorPack]['special-offers'] || $isAuthorAdmin)}
			{* SPECIAL OFFERS SECTION *}
			{if (defined('AIT_SPECIAL_OFFERS_ENABLED'))}
				{includePart parts/single-item-special-offers}
			{/if}
			{* SPECIAL OFFERS SECTION *}
			{/if}

			{if ($pack_display[$authorPack]['upcoming-events'] || $isAuthorAdmin)}
			{* UPCOMING EVENTS SECTION *}
			{if (defined('AIT_EVENTS_PRO_ENABLED')) && AitEventsPro::getEventsByItem($post->id)->found_posts}
				{includePart portal/parts/single-item-events, itemId => $post->id}
			{/if}
			{* UPCOMING EVENTS SECTION *}
			{/if}

		{*RICH SNIPPET WRAP*}
		</div>
		{*RICH SNIPPET WRAP*}

	{/loop}
