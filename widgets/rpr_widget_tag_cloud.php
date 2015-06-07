<?php
/**
 * The Tags widget replaces the default WordPress Tag Cloud widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_tag_cloud() function. It's derived from the Hybrid_tags Widget by Justin Tadlock <justin@justintadlock.com>
 * from the http://themehybrid.com/recipe-press-reloaded project
 *
 * @package    RecipePress reloaded
 * @subpackage Widgets
 * @author     Jan Köster <dasmaeh@rp-reloaded.net>
 * @copyright  Copyright (c) 2015 Jan Köster
 * @link       http://rp-reloaded.net
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Tags Widget Class
 *
 * @since 0.7.6
 */
class RPR_Widget_Tag_Cloud extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'tags',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your tags. Ingredients on the exclude list are automatically removed.', 'recipe-press-reloaded' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 800,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'rpr-tag-cloud',               // $this->id_base
			__( 'Tag Cloud', 'recipe-press-reloaded' ), // $this->name
			$widget_options,             // $this->widget_options
			$control_options             // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 0.6.0
	 */
	function widget( $sidebar, $instance ) {
		global $rpr_option;
		
		extract( $sidebar );

		/* Set the $args for wp_tag_cloud() to the $instance array. */
		$args = $instance;

		/* Exclude ingredients if necessary */
		if( is_array($$instance['taxonomy']) && in_array('rpr_ingredient', $instance['taxonomy'])){// } == 'rpr_ingredient' ){
			$args['exclude'] = $rpr_option['ingredients_exclude_list'];
		}
		
		
		/* Make sure empty callbacks aren't passed for custom functions. */
		$args['topic_count_text_callback'] = !empty( $args['topic_count_text_callback'] ) ? $args['topic_count_text_callback'] : 'default_topic_count_text';
		$args['topic_count_scale_callback'] = !empty( $args['topic_count_scale_callback'] ) ? $args['topic_count_scale_callback'] : 'default_topic_count_scale';

		/* If the separator is empty, set it to the default new line. */
		$args['separator'] = !empty( $args['separator'] ) ? $args['separator'] : "\n";

		/* Overwrite the echo argument. */
		$args['echo'] = false;

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Get the tag cloud. */
		$tags = str_replace( array( "\r", "\n", "\t" ), ' ', wp_tag_cloud( $args ) );

		/* If $format should be flat, wrap it in the <p> element. */
		if ( 'flat' == $instance['format'] ) {
			$classes = array( 'term-cloud' );

			foreach ( $instance['taxonomy'] as $tax )
				$classes[] = sanitize_html_class( "{$tax}-cloud" );

			$tags = '<p class="' . join( $classes, ' ' ) . '">' . $tags . '</p>';
		}

		/* Output the tag cloud. */
		echo $tags;

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		$instance['title']                      = strip_tags( $new_instance['title'] );
		$instance['smallest']                   = strip_tags( $new_instance['smallest'] );
		$instance['largest']                    = strip_tags( $new_instance['largest'] );
		$instance['number']                     = strip_tags( $new_instance['number'] );
		$instance['separator']                  = strip_tags( $new_instance['separator'] );
		$instance['name__like']                 = strip_tags( $new_instance['name__like'] );
		$instance['search']                     = strip_tags( $new_instance['search'] );
		$instance['child_of']                   = strip_tags( $new_instance['child_of'] );
		$instance['parent']                     = strip_tags( $new_instance['parent'] );
		$instance['topic_count_text_callback']  = strip_tags( $new_instance['topic_count_text_callback'] );
		$instance['topic_count_scale_callback'] = strip_tags( $new_instance['topic_count_scale_callback'] );

		$instance['include'] = preg_replace( '/[^0-9,]/', '', $new_instance['include'] );
		$instance['exclude'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude'] );

		$instance['unit']     = $new_instance['unit'];
		$instance['format']   = $new_instance['format'];
		$instance['orderby']  = $new_instance['orderby'];
		$instance['order']    = $new_instance['order'];
		$instance['taxonomy'] = $new_instance['taxonomy'];
		$instance['link']     = $new_instance['link'];

		$instance['pad_counts'] = ( isset( $new_instance['pad_counts'] ) ? 1 : 0 );
		$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title'                      => esc_attr__( 'Tag Cloud', 'recipe-press-reloaded' ),
			'order'                      => 'ASC',
			'orderby'                    => 'name',
			'format'                     => 'flat',
			'include'                    => '',
			'exclude'                    => '',
			'unit'                       => 'pt',
			'smallest'                   => 8,
			'largest'                    => 22,
			'link'                       => 'view',
			'number'                     => 0,
			'separator'                  => ' ',
			'child_of'                   => '',
			'parent'                     => '',
			'taxonomy'                   => array( 'post_tag' ),
			'hide_empty'                 => 1,
			'pad_counts'                 => false,
			'search'                     => '',
			'name__like'                 => '',
			'topic_count_text_callback'  => 'default_topic_count_text',
			'topic_count_scale_callback' => 'default_topic_count_scale',
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		/* <select> element options. */
		$taxonomies = get_taxonomies( array( 'show_tagcloud' => true ), 'objects' );

		/*$link = array( 
			'view' => esc_attr__( 'View', 'recipe-press-reloaded' ), 
			'edit' => esc_attr__( 'Edit', 'recipe-press-reloaded' ) 
		);*/

		$format = array( 
			'flat' => esc_attr__( 'Flat', 'recipe-press-reloaded' ), 
			'list' => esc_attr__( 'List', 'recipe-press-reloaded' ) 
		);

		$order = array( 
			'ASC'  => esc_attr__( 'Ascending', 'recipe-press-reloaded' ), 
			'DESC' => esc_attr__( 'Descending', 'recipe-press-reloaded' ), 
			'RAND' => esc_attr__( 'Random', 'recipe-press-reloaded' ) 
		);

		$orderby = array( 
			'count' => esc_attr__( 'Count', 'recipe-press-reloaded' ), 
			'name'  => esc_attr__( 'Name', 'recipe-press-reloaded' ) 
		);

		$unit = array( 
			'pt' => 'pt', 
			'px' => 'px', 
			'em' => 'em', 
			'%'  => '%' 
		);

		?>

		<div class="rpr-widget-controls columns-2 column-first">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><code><?php _e( 'Title:', 'recipe-press-reloaded' ); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><code>taxonomy</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>[]" size="4" multiple="multiple">
				<?php foreach ( $taxonomies as $taxonomy ) { ?>
					<option value="<?php echo $taxonomy->name; ?>" <?php selected( in_array( $taxonomy->name, (array)$instance['taxonomy'] ) ); ?>><?php echo $taxonomy->labels->singular_name; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><code>format</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
				<?php foreach ( $format as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['format'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><code>order</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach ( $order as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['order'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code>orderby</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach ( $orderby as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['orderby'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		</div>

		<div class="rpr-widget-controls columns-2 column-last">
		<!--<p>
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><code>include</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" />
		</p>-->
		<!--<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><code>exclude</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
		</p>-->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><code>number</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><code>largest</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'largest' ); ?>" name="<?php echo $this->get_field_name( 'largest' ); ?>" value="<?php echo esc_attr( $instance['largest'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><code>smallest</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'smallest' ); ?>" name="<?php echo $this->get_field_name( 'smallest' ); ?>" value="<?php echo esc_attr( $instance['smallest'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'unit' ); ?>"><code>unit</code></label> 
			<select class="smallfat" id="<?php echo $this->get_field_id( 'unit' ); ?>" name="<?php echo $this->get_field_name( 'unit' ); ?>">
				<?php foreach ( $unit as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['unit'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><code>separator</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" value="<?php echo esc_attr( $instance['separator'] ); ?>" />
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>