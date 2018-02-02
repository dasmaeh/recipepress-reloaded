<?php
/**
 * The options page functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The general options tab of the options page, display saved data
 *
 * Adds a section to the tab, displaying the data
 *
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage  	recipepress-reloaded/admin/views
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Module_Notes {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
	
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Add settings to existing section:
        $oFactory->addSettingFields(
            'advanced', 
            array(
                'field_id'          => 'notes_headline',
                'type'              => 'text',
                'title'             => __( 'Notes headline', 'recipepress-reloaded' ),
                'description'       => __( 'Use this to change the headline of the notes section in te frontend', 'recipepress-reloaded'),
                'tip'               => __( 'If this works, dynamic loading of module option does work!', 'recipepress-reloaded'),
                'default'           => __( "Notes", 'recipepress-reloaded' ),
            )
        );
    }

}