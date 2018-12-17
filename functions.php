<?php // Opening PHP tag - do not insert any code or spaces before opening tag

// Your php code starts here
function wh_redirect_to_home()
{
    if (! current_user_can('administrator') ) {
    	if (is_user_logged_in() && (is_home() || is_page() || is_single()) && !(count_user_posts( get_current_user_id(), "ait-item"  ) > 0) )
	    {
	        wp_redirect(esc_url(home_url('/wp-admin/post-new.php?post_type=ait-item')));
	        exit();
	    }
    }

    
}

add_action('template_redirect', 'wh_redirect_to_home');

/********************* Include Child Styles and Scripts **************************************/
function br_enqueue_assets() {
	global $wp_query;

	//if (is_home() || is_front_page()) {
		wp_enqueue_script( 'contact-owner-homepage',  get_stylesheet_directory_uri() . '/js/contact-owner-homepage.js',array( 'jquery' ), '1.0', true );
	//}
}

add_action( 'wp_enqueue_scripts', 'br_enqueue_assets' );

function my_admin_scripts() {
   	wp_enqueue_script( 'jquery-ui-accordion');
   	//wp_enqueue_style( 'admin_css', get_stylesheet_directory_uri() . '/css/admin-css.css', false, '1.0.0' );
   	wp_enqueue_style( 'jquery-ui-custom-css', get_stylesheet_directory_uri() . '/css/base/jquery-ui-1.9.2.custom.css');
}
add_action( 'admin_enqueue_scripts', 'my_admin_scripts' );

// Function to change sender name
function br_sender_name( $original_email_from ) {
    return 'PortalSolar.com';
}
 
// Hooking up our functions to WordPress filters 
add_filter( 'wp_mail_from_name', 'br_sender_name' );

/********************* Activar capabilities para todos los paquetes **************************************/
function br_activate_capabilities_packages($value='')
{
	global $wp_roles;
	$packages = new ThemePackages();
	$roles = getThemeUserRoles();

		foreach ($roles as $rol) {
			$features = array(
				'package_feature_editor',
				'package_feature_editor_media',
				'package_feature_image',
				'package_feature_excerpt',
				'package_feature_comments',
				'package_feature_author',
				'package_feature_yoastSeo',
				'_ait-item_item-data_headerType',
				'_ait-item_item-data_headerType-image',
				'_ait-item_item-data_map',
				'_ait-item_item-data_telephone',
				'_ait-item_item-data_telephoneAdditional',
				'_ait-item_item-data_email',
				'_ait-item_item-data_showEmail',
				'_ait-item_item-data_contactOwnerBtn',
				'_ait-item_item-data_web',
				'_ait-item_item-data_webLinkLabel',
				'_ait-item_item-data_itemOpeningHours',
				'_ait-item_item-data_itemSocialIcons',
				'_ait-item_item-data_itemGallery',
				'_ait-item_item-data_itemFeatures',
				'_ait-item_item-author_author',
			);
			foreach($features as $feature){
				$wp_roles->role_objects[$rol->name]->add_cap($feature, true);
				//$wp_roles->add_cap($rol->name,$feature);
				//echo $rol->name;
				//var_dump($rol);
			}


	}

	//$result = getThemeUserRoles();
   	//var_dump($result);
}

add_action( 'after_setup_theme', 'br_activate_capabilities_packages');

/********************* Opciones de Visibilidad de Paquetes (Formulario Propio) **************************************/

function br_add_menu_paquetes() {
	//add_menu_page( 'Visibilidad de Paquetes', 'Visibilidad de Paquetes','manage_options', 'br_pack_settings', 'br_pack_settings_screen' );
	add_submenu_page( 'ait-theme-options','Visibilidad de Paquetes', 'Visibilidad de Paquetes','manage_options', 'br_pack_display', 'br_pack_display_screen' );
}

add_action('admin_menu', 'br_add_menu_paquetes');


function br_registrar_opciones_paquetes() {
	register_setting( 'br_pack_display_group', 'br_pack_display');
}

add_action('admin_init', 'br_registrar_opciones_paquetes');

function br_pack_display_screen() {
	global $wp_roles;
	$packages = new ThemePackages();
	$roles = getThemeUserRoles();
	$features = array(
		'Content' => 'content',
		'Mapa' => 'map',
		'Datos Contacto' => 'item-address',
		'Botón de Contacto con la Empresa' => 'contact-owner',
		'Horario de Apertura' => 'opening-hours',
		'Compartir en Redes Sociales' => 'item-social',
		'Galería de Imágenes' => 'gallery',
		'Campos Destacados' => 'item-features',
		'Get Directions' => 'get-directions',
		'Campos Adicionales (Item Extension)' => 'item-extension',
		'Petición de Propiedad' => 'claim-listing',
		'Comentarios' => 'reviews',
		'Ofertas Especiales' => 'special-offers',
		'Próximos Eventos' => 'upcoming-events'
	);
	?>
	<script>
	  jQuery( function() {
	    jQuery( "#accordion" ).accordion({
	    	collapsible: true
	    });
	  } );
	 </script>
	<div class="wrap">
		<form method="post" id="br_setting_pack" action="options.php">
			<?php
			settings_fields('br_pack_display_group');
			do_settings_sections( 'br_pack_display_group' );
			$options = get_option('br_pack_display');

			if ( current_user_can( 'manage_options' ) ) {
			?>
				<h2><?php _e('Configuración de Visibilidad de Paquetes' ); ?></h2>
				<p><?php _e('Por favor seleccione los campos que será visibles en cada empresa.'); ?></p>
				<div id="accordion">				
				<!--form fields will go here -->
				<?php foreach ($roles as $rol) { ?>
					<h3><?php echo $packages->getPackageBySlug($rol->name)->getName() ?></h3>
					<div class="seccion-paquete collapsible">
						<table class="form-table">
						<?php foreach($features as $name => $feature){ ?>
							<tr>
								<th scope="row">
									<label for="br_pack_display[<?php echo $rol->name ?>][<?php echo $feature ?>]"> <?php _e($name); ?> </label>
								</th>
								<td> 
									<input name="br_pack_display[<?php echo $rol->name ?>][<?php echo $feature ?>]" type="checkbox" value="1" <?php checked($options[$rol->name][$feature], '1'); ?> /> 
								</td>
							</tr>
						<?php } ?>
						</table>
					</div>
				<?php } ?>
				<!--form fields will go here -->
				</div>
				<p class="submit">
				<input type="submit" value="<?php esc_attr_e( 'Update Options' ); ?>" class="button-primary" />
				</p>
			<?php } // if current_user_can() ?>
		</form>
	</div>
<?php
}


/********************* Opciones de Visibilidad de Paquetes (Formulario Propio) **************************************/

