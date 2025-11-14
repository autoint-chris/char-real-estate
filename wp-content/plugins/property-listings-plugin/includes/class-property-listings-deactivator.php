<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Property_Listings
 */

class Property_Listings_Deactivator {

    /**
     * Plugin deactivation logic.
     *
     * @since 1.0.0
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Note: We don't delete options here in case the user wants to reactivate
        // Options can be deleted on plugin uninstall if needed
    }
}
