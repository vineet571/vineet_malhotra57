<?php
/**
 * Abstract post type registration class
 */
if ( ! class_exists( 'Jet_Woo_Builder_Shortcode_Base' ) ) {

	abstract class Jet_Woo_Builder_Shortcode_Base {

		/**
		 * Information about shortcode
		 *
		 * @var array
		 */
		public $info = array();

		/**
		 * Information about shortcode
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * User attributes
		 *
		 * @var array
		 */
		public $atts = array();

		/**
		 * Initialize post type
		 * @return void
		 */
		public function __construct() {
			add_shortcode( $this->get_tag(), array( $this, 'do_shortcode' ) );
		}

		/**
		 * Returns shortcode tag. Should be rewritten in shortcode class.
		 *
		 * @return string
		 */
		public function get_tag() {
		}

		/**
		 * This function should be rewritten in shortcode class with attributes array.
		 *
		 * @return [type] [description]
		 */
		public function get_atts() {
			return array();
		}

		/**
		 * Retrieve single shortocde argument
		 *
		 * @return void
		 */
		public function get_attr( $name = null ) {

			if ( isset( $this->atts[ $name ] ) ) {
				return $this->atts[ $name ];
			}

			$allowed = $this->get_atts();

			if ( isset( $allowed[ $name ] ) && isset( $allowed[ $name ]['default'] ) ) {
				return $allowed[ $name ]['default'];
			} else {
				return false;
			}

		}

		/**
		 * Return hidden atts array
		 *
		 * @return array
		 */
		public function _hidden_atts() {
			return array(
				'_element_id' => '',
			);
		}

		/**
		 * Get widget settings
		 *
		 * @return array
		 */
		public function get_settings(){
			return $this->settings;
		}

		/**
		 * Set widget settings
		 *
		 * @param array $settings
		 *
		 * @return array
		 */
		public function set_settings( $settings = array() ){
			return $this->settings = $settings;
		}

		/**
		 * This is main shortcode callback and it should be rewritten in shortcode class
		 *
		 * @param  string $content [description]
		 *
		 * @return [type]          [description]
		 */
		public function _shortcode( $content = null ) {
		}

		/**
		 * Print HTML markup if passed text not empty.
		 *
		 * @param  string $text Passed text.
		 * @param  string $format Required markup.
		 * @param  array $args Additional variables to pass into format string.
		 * @param  bool $echo Echo or return.
		 *
		 * @return string|void
		 */
		public function html( $text = null, $format = '%s', $args = array(), $echo = true ) {

			if ( empty( $text ) ) {
				return '';
			}

			$args   = array_merge( array( $text ), $args );
			$result = vsprintf( $format, $args );

			if ( $echo ) {
				echo $result;
			} else {
				return $result;
			}

		}

		/**
		 * Return default shortcode attributes
		 *
		 * @return array
		 */
		public function default_atts() {

			$result = array();

			foreach ( $this->get_atts() as $attr => $data ) {
				$result[ $attr ] = isset( $data['default'] ) ? $data['default'] : false;
			}

			foreach ( $this->_hidden_atts() as $attr => $default ) {
				$result[ $attr ] = $default;
			}

			return $result;
		}

		/**
		 * Shortcode callback
		 *
		 * @return string
		 */
		public function do_shortcode( $atts = array(), $content = null ) {

			$atts              = shortcode_atts( $this->default_atts(), $atts, $this->get_tag() );
			$this->css_classes = array();

			if ( null !== $content ) {
				$content = do_shortcode( $content );
			}

			$this->atts = $atts;

			return $this->_shortcode( $content );
		}

		/**
		 * Get template depends to shortcode slug.
		 *
		 * @param  string $name Template file name (without extension).
		 *
		 * @return string
		 */
		public function get_template( $name ) {
			return jet_woo_builder()->get_template( $this->get_tag() . '/global/' . $name . '.php' );
		}

		/**
	 * Custom query used to filter products by price.
	 *
	 * @since 3.6.0
	 *
	 * @param array    $args Query args.
	 * @param WC_Query $wp_query WC_Query object.
	 *
	 * @return array
	 */
	public function price_filter_post_clauses( $args, $wp_query ) {
		global $wpdb;

		if ( ! isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) ) {
			return $args;
		}

		$current_min_price = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
		$current_max_price = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : PHP_INT_MAX; // WPCS: input var ok, CSRF ok.

		/**
		 * Adjust if the store taxes are not displayed how they are stored.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
				$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
			}
		}

		$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
		$args['where'] .= $wpdb->prepare(
			' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
			$current_min_price,
			$current_max_price
		);

		return $args;
	}

	/**
	 * Join wc_product_meta_lookup to posts if not already joined.
	 *
	 * @param string $sql SQL join.
	 * @return string
	 */
	private function append_product_sorting_table_join( $sql ) {
		global $wpdb;

		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}

	}
}
