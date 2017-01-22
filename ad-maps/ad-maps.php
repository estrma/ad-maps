<?php
/*
Plugin Name: AD-maps
Plugin URI: http://akcjademokracja.pl
Version: 1.1
Author: e
Author URI: http://tausendsassa.pl
*/



class Templater {


	protected $plugin_slug;


	private static $instance;


	protected $templates;



	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new Templater();
		}

		return self::$instance;

	}


	private function __construct() {

		$this->templates = array();


		add_filter(
			'page_attributes_dropdown_pages_args',
			array( $this, 'register_project_templates' )
		);


		add_filter(
			'wp_insert_post_data',
			array( $this, 'register_project_templates' )
		);


		add_filter(
			'template_include',
			array( $this, 'view_project_template' )
		);


		$this->templates = array(
			'ad-maps-template.php' => 'Maps',
		);

	}



	public function register_project_templates( $atts ) {

		$theme = wp_get_theme();

		$cache_key = 'page_templates-' . md5( $theme->get_theme_root() . '/' . $theme->get_stylesheet() );

		$templates = $theme->get_page_templates();

		$templates = array_merge( $templates, $this->templates );


		wp_cache_set( $cache_key, $templates, 'themes', 300 );

		add_filter( 'theme_page_templates', function( $page_templates ) use ( $templates ) {
			return $templates;
		});

		return $atts;

	}


	public function view_project_template( $template ) {

		global $post;

		if ( ! isset( $this->templates[ get_post_meta(
				$post->ID, '_wp_page_template', true
			) ] )
		) {

			return $template;

		}

		$file = plugin_dir_path( __FILE__ ) . get_post_meta(
				$post->ID, '_wp_page_template', true
			);

		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		return $template;

	}

}

add_action( 'plugins_loaded', array( 'Templater', 'get_instance' ) );

function map_load_scripts() {

    wp_enqueue_style( 'map-style',  plugin_dir_url( __FILE__ )  . '/lib/assets/css/style.css', [], random_int(111,999));
    /*      wp_enqueue_script( 'map-jquery',  get_template_directory_uri() . '/map/lib/assets/vendor/jquery.min.js', false, true);*/
    wp_enqueue_script( 'map-d3',   plugin_dir_url( __FILE__ )  . '/lib/assets/vendor/d3.min.js', ['jquery'], false, true);
    wp_enqueue_script( 'map-scripts',  plugin_dir_url( __FILE__ )  . '/lib/assets/js/app.min.js', ['jquery','map-d3'], false, true);
}

add_action( 'wp_enqueue_scripts', 'map_load_scripts' );


function json_get_meta( $value ) {
    global $post;

    $field = get_post_meta( $post->ID, $value, true );
    if ( ! empty( $field ) ) {
        return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
    } else {
        return false;
    }
}

function json_add_meta_box() {
  
if ( 'ad-maps-template.php' == get_post_meta( $_GET['post'], '_wp_page_template', true ) ) {
    add_meta_box(
        'json-json',
        __( 'JSON', 'json' ),
        'json_html',
        'page',
        'side',
        'high'
    );
  }
}


function json_html( $post) {
    wp_nonce_field( '_json_nonce', 'json_nonce' ); ?>

    <p>
    <label for="json_url"><?php _e( 'url', 'json' ); ?></label><br>
    <input type="text" name="json_url" id="json_url" value="<?php echo json_get_meta( 'json_url' ); ?>">
    </p><?php
}

function json_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['json_nonce'] ) || ! wp_verify_nonce( $_POST['json_nonce'], '_json_nonce' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['json_url'] ) )
        update_post_meta( $post_id, 'json_url', esc_attr( $_POST['json_url'] ) );
}
add_action( 'save_post', 'json_save' );

    add_action( 'add_meta_boxes', 'json_add_meta_box' );

