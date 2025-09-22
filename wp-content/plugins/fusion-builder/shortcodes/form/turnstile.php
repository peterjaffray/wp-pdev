<?php
/**
 * Add an element to fusion-builder.
 *
 * @package fusion-builder
 * @since 3.1
 */

if ( fusion_is_element_enabled( 'fusion_form_turnstile' ) ) {

	if ( ! class_exists( 'FusionForm_Turnstile' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @since 3.1
		 */
		class FusionForm_Turnstile extends Fusion_Form_Component {

			/**
			 * Array of forms that use Turnstile.
			 *
			 * @static
			 * @access private
			 * @since 3.11.12
			 * @var array
			 */
			private static $forms = [];

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 3.1
			 */
			public function __construct() {
				add_filter( 'fusion_attr_turnstile-shortcode', [ $this, 'attr' ] );

				parent::__construct( 'fusion_form_turnstile' );
			}

			/**
			 * Gets the default values.
			 *
			 * @static
			 * @access public
			 * @since 3.1
			 * @return array
			 */
			public static function get_element_defaults() {
				$fusion_settings = awb_get_fusion_settings();
				return [
					'appearance' => 'always',
					'theme'      => '',
					'size'       => 'normal',
					'language'   => 'auto',
					'tab_index'  => '',
					'class'      => '',
					'id'         => '',
				];
			}

			/**
			 * Render form field html.
			 *
			 * @access public
			 * @since 3.1
			 * @param string $content The content.
			 * @return string
			 */
			public function render_input_field( $content ) {
				self::$forms[ $this->params['form_number'] ] = isset( self::$forms[ $this->params['form_number'] ] ) ? self::$forms[ $this->params['form_number'] ] + 1 : 1;
				$counter                                     = 1 < self::$forms[ $this->params['form_number'] ] ? $this->params['form_number'] . '-' . self::$forms[ $this->params['form_number'] ] : $this->params['form_number'];

				$html = '<div ' . FusionBuilder::attributes( 'turnstile-shortcode' ) . '></div>';

				if ( 1 === $this->counter ) {
					$this->enqueue_scripts();
				}

				$this->counter++;

				return $html;
			}

			/**
			* Sets the necessary scripts.
			*
			* @access public
			* @since 7.11.6
			* @return void
			*/
			public function enqueue_scripts() {
				if ( fusion_library()->get_option( 'turnstile_site_key' ) && fusion_library()->get_option( 'turnstile_secret_key' ) ) {
					$turnstile_api_url = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

					wp_enqueue_script( 'cloudflare-turnstile-api', $turnstile_api_url, [], FUSION_BUILDER_VERSION, false );
				}
			}


			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 3.1
			 * @return array
			 */
			public function attr() {
				$attr = [
					'class' => 'awb-forms-turnstile cf-turnstile',
				];

				$attr['data-response-field-name'] = 'cf-turnstile-response-' . $this->counter;
				$attr['data-sitekey']             = fusion_library()->get_option( 'turnstile_site_key' );
				$attr['data-appearance']          = $this->args['appearance'];
				$attr['data-theme']               = $this->args['theme'];
				$attr['data-size']                = $this->args['size'];
				$attr['data-language']            = $this->args['language'];

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;
			}

			/**
			 * Used to set any other variables for use on front-end editor template.
			 *
			 * @static
			 * @access public
			 * @since 3.1
			 * @return array
			 */
			public static function get_element_extras() {
				$fusion_settings = awb_get_fusion_settings();
				return [
					'turnstile_site_key'   => $fusion_settings->get( 'turnstile_site_key' ),
					'turnstile_secret_key' => $fusion_settings->get( 'turnstile_secret_key' ),
				];
			}
		}
	}

	new FusionForm_Turnstile();
}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 3.1
 */
function fusion_form_turnstile() {
	$info = [];
	if ( ! fusion_library()->get_option( 'turnstile_site_key' ) || ! fusion_library()->get_option( 'turnstile_secret_key' ) ) {
		if ( ( function_exists( 'fusion_is_preview_frame' ) && fusion_is_preview_frame() ) || ( function_exists( 'fusion_is_builder_frame' ) && fusion_is_builder_frame() ) ) {
			$to_link = '<span class="fusion-panel-shortcut" data-fusion-option="turnstile_site_key">' . esc_html__( 'Turnstile Section', 'fusion-builder' ) . '</span>';
		} else {
			$to_link = '<a href="' . esc_url( awb_get_fusion_settings()->get_setting_link( 'turnstile_site_key' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Turnstile Section', 'fusion-builder' ) . '</a>';
		}
		
		$info = [
			'heading'     => esc_attr__( 'Set Up Needed Keys In Global Options', 'fusion-builder' ),
			'description' =>  sprintf( esc_html__( 'Please make sure to set up the needed keys in %s of Global Options.', 'fusion-builder' ), $to_link ),
			'param_name'  => 'turnstile_important_note_info',
			'type'        => 'custom',
		];
	}

	fusion_builder_map(
		fusion_builder_frontend_data(
			'FusionForm_Turnstile',
			[
				'name'           => esc_attr__( 'Turnstile Field', 'fusion-builder' ),
				'shortcode'      => 'fusion_form_turnstile',
				'icon'           => 'fusiona-cloudflare',
				'form_component' => true,
				'preview'        => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-form-element-preview.php',
				'preview_id'     => 'fusion-builder-block-module-form-element-preview-template',
				'params'         => [
					$info,
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Appearance Mode', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose the Turnstile appearance mode. "Always" will display the widget on page load in any case, while "Interaction Only" will make it visible only when visitor interaction is required.', 'fusion-builder' ),
						'param_name'  => 'appearance',
						'default'     => 'always',
						'value'       => [
							'always'           => esc_attr__( 'Always', 'fusion-builder' ),
							'interaction-only' => esc_attr__( 'Interaction Only', 'fusion-builder' ),
						],
					],					
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Color Scheme', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose the Turnstile color scheme.', 'fusion-builder' ),
						'param_name'  => 'theme',
						'default'     => 'auto',
						'value'       => [
							'auto'  => esc_attr__( 'Auto', 'fusion-builder' ),
							'light' => esc_attr__( 'Light', 'fusion-builder' ),
							'dark'  => esc_attr__( 'Dark', 'fusion-builder' ),
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Widget Size', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose the Turnstile widget size.', 'fusion-builder' ),
						'param_name'  => 'size',
						'default'     => 'normal',
						'value'       => [
							'normal'   => esc_attr__( 'Normal (300px)', 'fusion-builder' ),
							'flexible' => esc_attr__( 'Flexible (100%)', 'fusion-builder' ),
							'compact'  => esc_attr__( 'Compact (150px)', 'fusion-builder' ),
						],
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Language', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose the Turnstile widget language.', 'fusion-builder' ),
						'param_name'  => 'language',
						'default'     => 'auto',
						'value'       => [
							'auto'   => esc_html__( 'Auto (User browser language)', 'fusion-builder' ),
							'ar-eg'  => esc_html__( 'Arabic (Egypt)', 'fusion-builder' ),
							'bg-bg'  => esc_html__( 'Bulgarian (Bulgaria)', 'fusion-builder' ),
							'zh-cn'  => esc_html__( 'Chinese (Simplified, China)', 'fusion-builder' ),
							'zh-tw'  => esc_html__( 'Chinese (Traditional, Taiwan)', 'fusion-builder' ),
							'hr-hr'  => esc_html__( 'Croatian (Croatia)', 'fusion-builder' ),
							'cs-cz'  => esc_html__( 'Czech (Czech Republic)', 'fusion-builder' ),
							'da-dk'  => esc_html__( 'Danish (Denmark)', 'fusion-builder' ),
							'nl-nl'  => esc_html__( 'Dutch (Netherlands)', 'fusion-builder' ),
							'en-us'  => esc_html__( 'English (United States)', 'fusion-builder' ),
							'fa-ir'  => esc_html__( 'Farsi (Iran)', 'fusion-builder' ),
							'fi-fi'  => esc_html__( 'Finnish (Finland)', 'fusion-builder' ),
							'fr-fr'  => esc_html__( 'French (France)', 'fusion-builder' ),
							'de-de'  => esc_html__( 'German (Germany)', 'fusion-builder' ),
							'el-gr'  => esc_html__( 'Greek (Greece)', 'fusion-builder' ),
							'he-il'  => esc_html__( 'Hebrew (Israel)', 'fusion-builder' ),
							'hi-in'  => esc_html__( 'Hindi (India)', 'fusion-builder' ),
							'hu-hu'  => esc_html__( 'Hungarian (Hungary)', 'fusion-builder' ),
							'id-id'  => esc_html__( 'Indonesian (Indonesia)', 'fusion-builder' ),
							'it-it'  => esc_html__( 'Italian (Italy)', 'fusion-builder' ),
							'ja-jp'  => esc_html__( 'Japanese (Japan)', 'fusion-builder' ),
							'tlh'    => esc_html__( 'Klingon (Qo’noS)', 'fusion-builder' ),
							'ko-kr'  => esc_html__( 'Korean (Korea)', 'fusion-builder' ),
							'lt-lt'  => esc_html__( 'Lithuanian (Lithuania)', 'fusion-builder' ),
							'ms-my'  => esc_html__( 'Malay (Malaysia)', 'fusion-builder' ),
							'nb-no'  => esc_html__( 'Norwegian Bokmål (Norway)', 'fusion-builder' ),
							'pl-pl'  => esc_html__( 'Polish (Poland)', 'fusion-builder' ),
							'pt-br'  => esc_html__( 'Portuguese (Brazil)', 'fusion-builder' ),
							'ro-ro'  => esc_html__( 'Romanian (Romania)', 'fusion-builder' ),
							'ru-ru'  => esc_html__( 'Russian (Russia)', 'fusion-builder' ),
							'sr-ba'  => esc_html__( 'Serbian (Bosnia and Herzegovina)', 'fusion-builder' ),
							'sk-sk'  => esc_html__( 'Slovak (Slovakia)', 'fusion-builder' ),
							'sl-si'  => esc_html__( 'Slovenian (Slovenia)', 'fusion-builder' ),
							'es-es'  => esc_html__( 'Spanish (Spain)', 'fusion-builder' ),
							'sv-se'  => esc_html__( 'Swedish (Sweden)', 'fusion-builder' ),
							'tl-ph'  => esc_html__( 'Tagalog (Philippines)', 'fusion-builder' ),
							'th-th'  => esc_html__( 'Thai (Thailand)', 'fusion-builder' ),
							'tr-tr'  => esc_html__( 'Turkish (Turkey)', 'fusion-builder' ),
							'uk-ua'  => esc_html__( 'Ukrainian (Ukraine)', 'fusion-builder' ),
							'vi-vn'  => esc_html__( 'Vietnamese (Vietnam)', 'fusion-builder' ),
						],
					],				
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'Tab Index', 'fusion-builder' ),
						'param_name'  => 'tab_index',
						'value'       => '',
						'description' => esc_attr__( 'Tab index for the form field.', 'fusion-builder' ),
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
						'param_name'  => 'class',
						'value'       => '',
						'description' => esc_attr__( 'Add a class for the form field.', 'fusion-builder' ),
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
						'param_name'  => 'id',
						'value'       => '',
						'description' => esc_attr__( 'Add an ID for the form field.', 'fusion-builder' ),
					],
				],
			]
		)
	);
}
add_action( 'fusion_builder_before_init', 'fusion_form_turnstile' );
