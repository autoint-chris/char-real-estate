<?php
/**
 * Register the Property custom post type.
 *
 * @package Property_Listings
 */

class Property_Post_Type {

    /**
     * The post type slug.
     *
     * @since 1.0.0
     * @var string
     */
    const POST_TYPE = 'property';

    /**
     * Register the custom post type.
     *
     * @since 1.0.0
     */
    public function register() {
        $labels = array(
            'name' => _x('Properties', 'Post Type General Name', 'property-listings'),
            'singular_name' => _x('Property', 'Post Type Singular Name', 'property-listings'),
            'menu_name' => __('Properties', 'property-listings'),
            'name_admin_bar' => __('Property', 'property-listings'),
            'archives' => __('Property Archives', 'property-listings'),
            'attributes' => __('Property Attributes', 'property-listings'),
            'parent_item_colon' => __('Parent Property:', 'property-listings'),
            'all_items' => __('All Properties', 'property-listings'),
            'add_new_item' => __('Add New Property', 'property-listings'),
            'add_new' => __('Add New', 'property-listings'),
            'new_item' => __('New Property', 'property-listings'),
            'edit_item' => __('Edit Property', 'property-listings'),
            'update_item' => __('Update Property', 'property-listings'),
            'view_item' => __('View Property', 'property-listings'),
            'view_items' => __('View Properties', 'property-listings'),
            'search_items' => __('Search Property', 'property-listings'),
            'not_found' => __('Not found', 'property-listings'),
            'not_found_in_trash' => __('Not found in Trash', 'property-listings'),
            'featured_image' => __('Featured Image', 'property-listings'),
            'set_featured_image' => __('Set featured image', 'property-listings'),
            'remove_featured_image' => __('Remove featured image', 'property-listings'),
            'use_featured_image' => __('Use as featured image', 'property-listings'),
            'insert_into_item' => __('Insert into property', 'property-listings'),
            'uploaded_to_this_item' => __('Uploaded to this property', 'property-listings'),
            'items_list' => __('Properties list', 'property-listings'),
            'items_list_navigation' => __('Properties list navigation', 'property-listings'),
            'filter_items_list' => __('Filter properties list', 'property-listings'),
        );

        $args = array(
            'label' => __('Property', 'property-listings'),
            'description' => __('Property listings with image integration', 'property-listings'),
            'labels' => $labels,
            'supports' => array('revisions', 'author'),
            'taxonomies' => array('property_type', 'property_status'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-building',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'properties'),
        );

        register_post_type(self::POST_TYPE, $args);

        // Register taxonomies
        $this->register_taxonomies();
    }

    /**
     * Register custom taxonomies for properties.
     *
     * @since 1.0.0
     */
    private function register_taxonomies() {
        // Property Type taxonomy (e.g., House, Apartment, Condo)
        $type_labels = array(
            'name' => _x('Property Types', 'Taxonomy General Name', 'property-listings'),
            'singular_name' => _x('Property Type', 'Taxonomy Singular Name', 'property-listings'),
            'menu_name' => __('Property Types', 'property-listings'),
            'all_items' => __('All Types', 'property-listings'),
            'parent_item' => __('Parent Type', 'property-listings'),
            'parent_item_colon' => __('Parent Type:', 'property-listings'),
            'new_item_name' => __('New Type Name', 'property-listings'),
            'add_new_item' => __('Add New Type', 'property-listings'),
            'edit_item' => __('Edit Type', 'property-listings'),
            'update_item' => __('Update Type', 'property-listings'),
            'view_item' => __('View Type', 'property-listings'),
            'separate_items_with_commas' => __('Separate types with commas', 'property-listings'),
            'add_or_remove_items' => __('Add or remove types', 'property-listings'),
            'choose_from_most_used' => __('Choose from the most used', 'property-listings'),
            'popular_items' => __('Popular Types', 'property-listings'),
            'search_items' => __('Search Types', 'property-listings'),
            'not_found' => __('Not Found', 'property-listings'),
        );

        register_taxonomy('property_type', array(self::POST_TYPE), array(
            'labels' => $type_labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'property-type'),
        ));

        // Property Status taxonomy (e.g., For Sale, For Rent, Sold)
        $status_labels = array(
            'name' => _x('Property Status', 'Taxonomy General Name', 'property-listings'),
            'singular_name' => _x('Status', 'Taxonomy Singular Name', 'property-listings'),
            'menu_name' => __('Status', 'property-listings'),
            'all_items' => __('All Statuses', 'property-listings'),
            'parent_item' => __('Parent Status', 'property-listings'),
            'parent_item_colon' => __('Parent Status:', 'property-listings'),
            'new_item_name' => __('New Status Name', 'property-listings'),
            'add_new_item' => __('Add New Status', 'property-listings'),
            'edit_item' => __('Edit Status', 'property-listings'),
            'update_item' => __('Update Status', 'property-listings'),
            'view_item' => __('View Status', 'property-listings'),
            'separate_items_with_commas' => __('Separate statuses with commas', 'property-listings'),
            'add_or_remove_items' => __('Add or remove statuses', 'property-listings'),
            'choose_from_most_used' => __('Choose from the most used', 'property-listings'),
            'popular_items' => __('Popular Statuses', 'property-listings'),
            'search_items' => __('Search Statuses', 'property-listings'),
            'not_found' => __('Not Found', 'property-listings'),
        );

        register_taxonomy('property_status', array(self::POST_TYPE), array(
            'labels' => $status_labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'property-status'),
        ));
    }
}
