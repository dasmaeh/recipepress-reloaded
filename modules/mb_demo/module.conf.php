<?php

/* 
 * This file is used to create a translatebal configuration for the module
 */

$module_config = array(
    // Do not translate this! This is the unique identifier also saved to tha database
    // Same as directory name!
    'id'            => 'demo',  
    // REQUIRED: the title of the module
    'title'         => __( 'Demo module', 'recipepress-reloaded' ),
    // REQUIRED: a short description of what this module does
    'description'   => __( 'This is a test module to develop the modules API and demonstrate it\'s functionality.', 'recipepress-reloaded'),
    // REQUIRED: Version of the module
    // not to be translated
    'version'       => '0.1',
    // REQUIRED: the priority at which the module should be loaded
    // higher values mean later loading
    // not to be translated
    'priority'      => 10,
    // REQUIRED: define the module to be optional or always active
    'selectable'    => true,
    // OPTIONAL: the category this module belongs to, select from 'Metadata', 'Core' (currently, more to come), defaulkts to 'None'
    // not to betranslated!
    'category'      => 'Metadata',
    // OPTIONAL: the author of the module
    // not to be translated
    'author'        => 'Jan Köster',
    // OPTIONAL: contact email for the module
    // not to be translated
    'author_mail'   => 'dasmaeh@cbjck.de',
    // OPTIONAL: webpage for the module
    // not to be translated
    'author_url'    => 'https://dasmaeh.de',
    // OPTIONAL url for module documentation
    // not to be translated
    'doc_url'       => 'https://rpr.dasmaeh.de/modules/demo',
);


