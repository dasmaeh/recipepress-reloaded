<?php

/*
  Title: Notes
  Category: Core
  Author: Jan Köster
  Author Mail: dasmaeh@cbjck.de
  Author URL: https://dasmaeh.de
  Documentation URL: https://rpr.dasmaeh.de/modules/demo
  Version: 0.1
  Description: This module adds a description ection to your recipes
 */

class RPR_Module_Description extends RPR_Module {

    /**
     * Load all files required for the module
     */
    public function load_dependencies() {
        
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_admin_hooks($loader) {
        if (is_a($loader, 'RPR_Loader')) {
            //echo "Got a valid loader";
            // Add metabox for this module
            $loader->add_action('do_meta_boxes', $this, 'metabox_description', 10);
            // Save this modules recipe data:
            $loader->add_action('save_post', $this, 'save_recipe_description', 10, 2);
            // Add option fields for this module
            //$loader->add_action( 'init', $this, 'add_module_options' );
        }
    }

    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_public_hooks($loader) {
        if (is_a($loader, 'RPR_Loader')) {
            //echo "Got a valid loader";
        }
    }

    public function metabox_description() {
        // Add advanced metabox for nutritional information
        add_meta_box(
                'recipe_description_meta_box', __('Description', 'recipepress-reloaded'), array($this, 'do_metabox_description'), 'rpr_recipe', 'normal', 'high'
        );
    }

    public function do_metabox_description($recipe) {
        $description = get_post_meta($recipe->ID, "rpr_recipe_description", true);
        $options = array(
            'textarea_rows' => 4
        );
        $options['media_buttons'] = true;

        wp_editor($description, 'rpr_recipe_description', $options);
        wp_nonce_field( 'rpr_save_recipe_description', 'rpr_nonce_description' );

    }

    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_description($recipe_id, $recipe = NULL) {
        // Get the data submitted by the form:
        $data = $_POST;

        // Disable this action so can't be called twice in parallel
        remove_action('save_post', array($this, 'save_recipe_description'));

        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        if ($this->check_before_saving($recipe, $data['rpr_nonce_description'], 'rpr_save_recipe_description')) {
            // save the data
            $fields = array(
                'rpr_recipe_description'
            );
            $this->save_fields($fields, $data, $recipe);
        }

        // Re-enable this action so it can be called again:
        add_action('save_post', array($this, 'save_recipe_description'));
    }

    public function get_path() {
        return dirname(__FILE__);
    }

     /**
     * Return the structured data related to this module encoded as an array
     * Core function will create JSON-LD schmema from this and other module's
     * data
     * For more infomration on structured data see: 
     * http://1.schemaorgae.appspot.com/NutritionInformation
     */
    public function get_structured_data($recipe_id, $recipe) {
        $json = array();
        if( isset( $recipe['rpr_recipe_description'] ) &&  $recipe['rpr_recipe_description'] != '' ){
            $description = strip_tags($recipe['rpr_recipe_description'][0]);
            $description = preg_replace("/\s+/", " ", $description);
            $json['description'] = esc_html( $description );
        }
        
        return $json;
    }

}
