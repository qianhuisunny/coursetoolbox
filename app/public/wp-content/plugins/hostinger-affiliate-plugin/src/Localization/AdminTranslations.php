<?php

namespace Hostinger\AffiliatePlugin\Localization;

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class AdminTranslations {
    public static function get_values(): array {
        return array(
            'connect_your_amazon_account'                                     => __( 'Connect Your Amazon Account', 'hostinger-affiliate-plugin' ),
            'fill_api_details_dashboard'                                      => __( 'Fill API details from Amazon Product Advertising dashboard to start', 'hostinger-affiliate-plugin' ),
            'connect_account_cta_heading'                                     => __( 'Log in to Amazon Associate dashboard', 'hostinger-affiliate-plugin' ),
            'connect_account_cta_desc'                                        => __( 'In order to get the Amazon API information, you need to log in to your <b>Amazon Associate</b> account.', 'hostinger-affiliate-plugin' ),
            'connect_account_cta_button_text'                                 => __( 'Login', 'hostinger-affiliate-plugin' ),
            'settings_adding_access_and_secret_key_will_make_search_accurate' => __( 'Adding an Access key and Secret key will make your search results more accurate.', 'hostinger-affiliate-plugin' ),
            'settings_find_your_api_credentials'                              => __( 'Find your API credentials', 'hostinger-affiliate-plugin' ),
            'settings_optional'                                               => __( 'Optional', 'hostinger-affiliate-plugin' ),
            'settings_select_country_first'                                   => __( 'Select country first to find matching tracking ID', 'hostinger-affiliate-plugin' ),
            'settings_api_settings'                                           => __( 'API Settings', 'hostinger-affiliate-plugin' ),
            'settings_field_access_key'                                       => __( 'Access key', 'hostinger-affiliate-plugin' ),
            'settings_secret_key'                                             => __( 'Secret key', 'hostinger-affiliate-plugin' ),
            'settings_field_language'                                         => __( 'Language', 'hostinger-affiliate-plugin' ),
            'settings_field_country'                                          => __( 'Country', 'hostinger-affiliate-plugin' ),
            'settings_field_language_of_preference'                           => __( 'Language of Preference', 'hostinger-affiliate-plugin' ),
            'settings_label_choose_country'                                   => __( 'Choose Amazon region ...', 'hostinger-affiliate-plugin' ),
            'settings_field_tracking_id'                                      => __( 'Tracking ID', 'hostinger-affiliate-plugin' ),
            'settings_field_access_key_mix_error'                              => __( 'Access key must be combination of letters and numbers', 'hostinger-affiliate-plugin' ),
            'settings_field_access_key_format_error'                           => __( 'Access key cannot contain special characters', 'hostinger-affiliate-plugin' ),
            'settings_field_access_key_length_error'                           => __( 'Access key must be 20 characters long', 'hostinger-affiliate-plugin' ),
            'settings_field_secret_key_length_error'                           => __( 'Secret key must be 40 characters long', 'hostinger-affiliate-plugin' ),
            'settings_field_secret_key_format_error'                           => __( 'Secret key contains of numbers, letters and special characters', 'hostinger-affiliate-plugin' ),
            'settings_field_submit'                                           => __( 'Connect Amazon account', 'hostinger-affiliate-plugin' ),
            'settings_save'                                                   => __( 'Save', 'hostinger-affiliate-plugin' ),
            'connect'                                                         => __( 'Connect', 'hostinger-affiliate-plugin' ),
            'api_status'                                                      => __( 'API status', 'hostinger-affiliate-plugin' ),
            'link_your_amazon_account_description'                            => __( 'Link your Amazon account to your website, start promoting products, and earn rewards. No API key required.', 'hostinger-affiliate-plugin' ),
            'api_status_connected'                                            => __( 'Connected', 'hostinger-affiliate-plugin' ),
            'api_status_disconnected'                                         => __( 'Disconnected', 'hostinger-affiliate-plugin' ),
            'api_status_connected_to'                                         => __( 'Connected to Partner ID:', 'hostinger-affiliate-plugin' ),
            'create_a_new_post'                                               => __( 'Create a new post', 'hostinger-affiliate-plugin' ),
            'api_status_disconnect'                                           => __( 'Disconnect', 'hostinger-affiliate-plugin' ),
            'welcome_card_title'                                              => __( 'Welcome to Hostinger Amazon Affiliate Connector!', 'hostinger-affiliate-plugin' ),
            'welcome_card_subtitle'                                           => __( 'Unlock the full potential of your website – monetize it with our plugin to earn more.', 'hostinger-affiliate-plugin' ),
            'welcome_card_button'                                             => __( 'Start Earning Now', 'hostinger-affiliate-plugin' ),
            'connect_amazon_associates_account'                               => __( 'Connect  your Amazon Associates Account', 'hostinger-affiliate-plugin' ),
            'start_earning_title'                                             => __( 'Start Earning in Four Easy Steps', 'hostinger-affiliate-plugin' ),
            'start_earning_subtitle'                                          => __( 'Follow this step-by-step guide to start earning your affiliate commission in no time.', 'hostinger-affiliate-plugin' ),
            'start_earning_item_1_title'                                      => __( 'Connect Your Amazon Account', 'hostinger-affiliate-plugin' ),
            'start_earning_item_1_desc'                                       => __( 'Insert API code inside the Amazon Product Advertising dashboard.', 'hostinger-affiliate-plugin' ),
            'start_earning_item_2_title'                                      => __( 'Add a new post', 'hostinger-affiliate-plugin' ),
            'start_earning_item_2_desc'                                       => __( 'Write down your content that you want to link your Amazon product with.', 'hostinger-affiliate-plugin' ),
            'start_earning_item_3_title'                                      => __( 'Link Amazon Products', 'hostinger-affiliate-plugin' ),
            'start_earning_item_3_desc'                                       => __( 'Search for <b>Link to Amazon Products</b> in Gutenberg Blocks, find the product(s) you want to add, and publish the post.', 'hostinger-affiliate-plugin' ),
            'start_earning_item_4_title'                                      => __( 'Earn Commissions', 'hostinger-affiliate-plugin' ),
            'start_earning_item_4_desc'                                       => __( 'That\'s it – every time visitors buy the product from your post, you will earn a commission from Amazon.', 'hostinger-affiliate-plugin' ),
            'start_earning_button'                                            => __( 'Connect With Your Amazon Account', 'hostinger-affiliate-plugin' ),
            'template_choice_title'                                           => __( 'Three Templates for Your Product(s) Block', 'hostinger-affiliate-plugin' ),
            'template_choice_subtitle'                                        => __( 'Choose any template for your needs  – all of them are integrated with the Gutenberg editor.', 'hostinger-affiliate-plugin' ),
            'template_choice_item_1_title'                                    => __( 'Single Product Card', 'hostinger-affiliate-plugin' ),
            'template_choice_item_1_subtitle'                                 => __( 'Put focus on one product only.', 'hostinger-affiliate-plugin' ),
            'template_choice_item_2_title'                                    => __( 'Multiple Product List', 'hostinger-affiliate-plugin' ),
            'template_choice_item_2_subtitle'                                 => __( 'Add a few products and describe them briefly.', 'hostinger-affiliate-plugin' ),
            'template_choice_item_3_title'                                    => __( 'Comparison Table', 'hostinger-affiliate-plugin' ),
            'template_choice_item_3_subtitle'                                 => __( 'Add multiple similar products and compare them.', 'hostinger-affiliate-plugin' ),
            'template_choice_button'                                          => __( 'Get Started', 'hostinger-affiliate-plugin' ),
            'settings_title'                                                  => __( 'Settings', 'hostinger-affiliate-plugin' ),
            'settings_subtitle'                                               => __( 'Manage your Amazon API and other configurations', 'hostinger-affiliate-plugin' ),
            'open_guide'                                                      => __( 'Open guide', 'hostinger-affiliate-plugin' ),
            'create_table'                                                    => __( 'New comparison table', 'hostinger-affiliate-plugin' ),
            'table_data_title'                                                => __( 'Your comparison tables', 'hostinger-affiliate-plugin' ),
            'table_data_subtitle'                                             => __( 'Create and manage your comparison tables.', 'hostinger-affiliate-plugin' ),
            'table_data_coming_soon'                                          => __( 'Coming soon', 'hostinger-affiliate-plugin' ),
            'nothing_found'                                                   => __( 'Nothing found', 'hostinger-affiliate-plugin' ),
            'nothing_found_no_data'                                           => __( 'Create your first comparison table', 'hostinger-affiliate-plugin' ),
            'try_other_results'                                               => __( 'Try searching for something else', 'hostinger-affiliate-plugin' ),
            'try_other_results_no_data'                                       => __( 'Choose which products to compare and set up your table\'s rows.', 'hostinger-affiliate-plugin' ),
            'copied_successfully'                                             => __( 'Copied successfully!', 'hostinger-affiliate-plugin' ),
            'table_data_collections_configure'                                => __( 'Configure', 'hostinger-affiliate-plugin' ),
            'table_data_collections_manage'                                   => __( 'Manage table', 'hostinger-affiliate-plugin' ),
            'tutorial_title'                                                  => __( 'Tutorial', 'hostinger-affiliate-plugin' ),
            'amazon_affiliate'                                                => __( 'Amazon Affiliate', 'hostinger-affiliate-plugin' ),
            'tutorial_subtitle'                                               => __( 'Edit your Amazon API details', 'hostinger-affiliate-plugin' ),
            'learn_more_howto_title'                                          => __( 'Learn more on how to use this plugin', 'hostinger-affiliate-plugin' ),
            'learn_more_howto_subtitle'                                       => __( 'Learn how to use Hostinger\'s Amazon Affiliate with our tutorials and documentations', 'hostinger-affiliate-plugin' ),
            'learn_how_to_use_plugin'                                         => __( 'Learn how to use our plugin', 'hostinger-affiliate-plugin' ),
            'got_it'                                                          => __( 'Got it', 'hostinger-affiliate-plugin' ),
            'check_out_our_guides'                                            => __( 'Check out our quick guide for tips on how to start using this plugin and start earning.', 'hostinger-affiliate-plugin' ),
            'back'                                                            => __( 'Back', 'hostinger-affiliate-plugin' ),
            'menu'                                                            => __( 'Menu', 'hostinger-affiliate-plugin' ),
            'find_your_tracking_id'                                           => __( 'Find your Tracking ID', 'hostinger-affiliate-plugin' ),
            'general'                                                         => __( 'General', 'hostinger-affiliate-plugin' ),
            'save_changes'                                                    => __( 'Save changes', 'hostinger-affiliate-plugin' ),
            'save_changes_question'                                           => __( 'Save changes?', 'hostinger-affiliate-plugin' ),
            'unchanged_changes_text'                                          => __( 'You have unsaved changes that will be lost if you leave.', 'hostinger-affiliate-plugin' ),
            'leave_without_saving'                                            => __( 'Leave without saving', 'hostinger-affiliate-plugin' ),
            'save_and_leave'                                                  => __( 'Save & leave', 'hostinger-affiliate-plugin' ),
            'tutorial_video_by'                                               => __( 'Tutorial video by', 'hostinger-affiliate-plugin' ),
            'hostinger_academy'                                               => __( 'Hostinger Academy', 'hostinger-affiliate-plugin' ),
            'video_title'                                                     => __( 'How to Set Up Amazon Affiliate with Hostinger Amazon Affiliate Connector - Step by step guide', 'hostinger-affiliate-plugin' ),
            'tutorial_howto_title'                                            => __( 'How to use this plugin?', 'hostinger-affiliate-plugin' ),
            'tutorial_item_1_title'                                           => __( 'Create a new post', 'hostinger-affiliate-plugin' ),
            'tutorial_item_1_subtitle'                                        => __( 'First thing first, you need to create a post. Make sure to create the post with the specific niche to be more related with your visitors.', 'hostinger-affiliate-plugin' ),
            'tutorial_item_2_title'                                           => __( 'Add “Link Amazon product(s)” in block editor', 'hostinger-affiliate-plugin' ),
            'tutorial_item_2_subtitle'                                        => __( 'Inside Block Editor, you can call Hostinger Amazon Affiliate in the block options. You can call it by typing “/” or click the add button in the right side of the paragraph.', 'hostinger-affiliate-plugin' ),
            'tutorial_item_3_title'                                           => __( 'Choose the layout for the product(s) block', 'hostinger-affiliate-plugin' ),
            'tutorial_item_3_subtitle'                                        => __( 'You need to choose the main layout for your product(s) block based on your needs. You can choose between single product, multiple product, or comparison.', 'hostinger-affiliate-plugin' ),
            'tutorial_item_4_title'                                           => __( 'Add the product(s)', 'hostinger-affiliate-plugin' ),
            'tutorial_item_4_subtitle'                                        => __( 'After choosing the layout, you need to find the product(s) that you want to promote in your post. You can copy-paste the ASIN or search the product(s) manually.', 'hostinger-affiliate-plugin' ),
            'tutorial_item_5_title'                                           => __( 'Publish the post', 'hostinger-affiliate-plugin' ),
            'tutorial_item_5_subtitle'                                        => __( 'That’s it! After your post published, whenever your visitor buy the product from your post, you will earn commissions.', 'hostinger-affiliate-plugin' ),
            'tutorial_read_full_doc'                                          => __( 'Read full documentation', 'hostinger-affiliate-plugin' ),
            'tutorial_create_new_post'                                        => __( 'Create a new post', 'hostinger-affiliate-plugin' ),
            'table_collection_nothing_found_description'                      => __( 'Table data will be shown in this page', 'hostinger-affiliate-plugin' ),
            'table_collection_add_new_table'                                  => __( 'Create table', 'hostinger-affiliate-plugin' ),
            'table_collection_your_tables'                                    => __( 'Your tables', 'hostinger-affiliate-plugin' ),
            'table_collection_new_table'                                      => __( 'New table', 'hostinger-affiliate-plugin' ),
            'table_collection_search_placeholder'                             => __( 'Search your table collections', 'hostinger-affiliate-plugin' ),
            'search_modal_keyword_missing'                                    => __( 'Please enter a keyword for search!', 'hostinger-affiliate-plugin' ),
            'search_modal_error_searching'                                    => __( 'There was an error searching for items: ', 'hostinger-affiliate-plugin' ),
            'fatal_error_while_processing'                                    => __( 'There was fatal server error while processing your request.', 'hostinger-affiliate-plugin' ),
            'api_disconnected'                                                => __( 'Amazon api was disconnected!', 'hostinger-affiliate-plugin' ),
            'api_settings_saved'                                              => __( 'Amazon settings were successfully saved!', 'hostinger-affiliate-plugin' ),
            'api_error_while_saving'                                          => __( 'There was an error saving your settings: ', 'hostinger-affiliate-plugin' ),
            'create_table_collection_enter_table_name'                        => __( 'Table name', 'hostinger-affiliate-plugin' ),
            'create_table_collection_add_compare_products'                    => __( 'Add products', 'hostinger-affiliate-plugin' ),
            'create_table_collection_edit_compare_products'                   => __( 'Products to be compared', 'hostinger-affiliate-plugin' ),
            'create_table_collection_add_row_data'                            => __( 'Add the product data', 'hostinger-affiliate-plugin' ),
            'create_table_collection_edit_row_data'                           => __( 'Product data', 'hostinger-affiliate-plugin' ),
            'create_table_collection_add_table_data'                          => __( 'Add table', 'hostinger-affiliate-plugin' ),
            'table_collection_table_name'                                     => __( 'Enter your table name', 'hostinger-affiliate-plugin' ),
            'continue'                                                        => __( 'Continue', 'hostinger-affiliate-plugin' ),
            'close'                                                           => __( 'Close', 'hostinger-affiliate-plugin' ),
            'cancel'                                                          => __( 'Cancel', 'hostinger-affiliate-plugin' ),
            'delete'                                                          => __( 'Delete', 'hostinger-affiliate-plugin' ),
            'delete_table'                                                    => __( 'Delete table', 'hostinger-affiliate-plugin' ),
            'or'                                                              => __( 'or', 'hostinger-affiliate-plugin' ),
            'table_collection_asin_invalid'                                   => __( 'Invalid ASIN', 'hostinger-affiliate-plugin' ),
            'table_collection_are_you_sure_delete_table'                      => __( 'You can also unpublish your table. Deleting it will cause you to lose all added products.', 'hostinger-affiliate-plugin' ),
            'table_collection_delete_table'                                   => __( 'Delete this table?', 'hostinger-affiliate-plugin' ),
            'table_collection_delete_product_row'                             => __( 'Delete product?', 'hostinger-affiliate-plugin' ),
            'table_collection_are_you_sure_delete_product_row'                => __( 'You can also hide products so they don’t appear in the table. Products that you delete will need to be added again.', 'hostinger-affiliate-plugin' ),
            'table_collection_saved'                                          => __( 'Table was successfully saved', 'hostinger-affiliate-plugin' ),
            'settings_field_is_required_error'                                => __( 'This field is required', 'hostinger-affiliate-plugin' ),
            'settings_field_is_required_tracking_id_format_error'             => __( 'Tracking ID format wrong. Lowercase letters, digits and _-. special chars are allowed.', 'hostinger-affiliate-plugin' ),
            'table_collection_created'                                        => __( 'Table was successfully created', 'hostinger-affiliate-plugin' ),
            'table_collection_field_is_required_error'                        => __( 'This field is required', 'hostinger-affiliate-plugin' ),
            'dropdown_no_options'                                             => __( 'No options', 'hostinger-affiliate-plugin' ),
            'table_collection_search_in_amazon'                               => __( 'Search for the product in Amazon', 'hostinger-affiliate-plugin' ),
            'table_collection_add_asin'                                       => __( 'Add', 'hostinger-affiliate-plugin' ),
            'table_collection_add_product'                                    => __( 'Add product', 'hostinger-affiliate-plugin' ),
            'table_collection_how_to_do_that'                                 => __( 'How to do that?', 'hostinger-affiliate-plugin' ),
            'table_collection_quick_way_to_add_block_editor'                  => __( 'Quick way to add this table inside the Block Editor.', 'hostinger-affiliate-plugin' ),
            'table_collection_shortcode'                                      => __( 'Shortcode', 'hostinger-affiliate-plugin' ),
            'table_collection_deleted'                                        => __( 'Table was successfully deleted', 'hostinger-affiliate-plugin' ),
            'table_collection_determine_who_will_see'                         => __( 'Determine who will see the table', 'hostinger-affiliate-plugin' ),
            'table_collection_visiblity'                                      => __( 'Visibility', 'hostinger-affiliate-plugin' ),
            'table_collection_published'                                      => __( 'Published', 'hostinger-affiliate-plugin' ),
            'table_collection_date_created'                                   => __( 'Date created', 'hostinger-affiliate-plugin' ),
            'table_collection_unpublish'                                      => __( 'Unpublish', 'hostinger-affiliate-plugin' ),
            'table_collection_publish'                                        => __( 'Publish', 'hostinger-affiliate-plugin' ),
            'table_collection_status'                                         => __( 'Status', 'hostinger-affiliate-plugin' ),
            'table_collection_add_row'                                        => __( 'Add row', 'hostinger-affiliate-plugin' ),
            'table_collection_add_highlight'                                  => __( 'Add highlight', 'hostinger-affiliate-plugin' ),
            'table_collection_highlight_settings'                             => __( 'Highlight settings', 'hostinger-affiliate-plugin' ),
            'table_collection_remove_highlight'                               => __( 'Remove highlight', 'hostinger-affiliate-plugin' ),
            'table_collection_text_label'                                     => __( 'Label', 'hostinger-affiliate-plugin' ),
            'table_collection_text_label_placeholder'                         => __( 'Enter text label. For example: Best Price', 'hostinger-affiliate-plugin' ),
            'table_collection_enter_asin'                                     => __( 'Search for products or enter the ASIN code', 'hostinger-affiliate-plugin' ),
            'table_collection_row_data'                                       => __( 'Row data', 'hostinger-affiliate-plugin' ),
            'table_collection_save_changes'                                   => __( 'Save changes', 'hostinger-affiliate-plugin' ),
            'table_collection_general_settings'                               => __( 'General settings', 'hostinger-affiliate-plugin' ),
            'table_collection_add_at_least_2_products'                        => __( 'Add at least 2 products to continue.', 'hostinger-affiliate-plugin' ),
            'create_table_collection_name_empty'                              => __( 'You need to enter table name.', 'hostinger-affiliate-plugin' ),
            'create_table_collection_at_least_two_products'                   => __( 'Add at least two products to continue', 'hostinger-affiliate-plugin' ),
            'create_table_collection_at_least_two_products_enabled'           => __( 'Enable at least two products to continue', 'hostinger-affiliate-plugin' ),
            'create_table_collection_too_many_products'                       => __( 'You have added too many products. Only 5 are allowed.', 'hostinger-affiliate-plugin' ),
            'create_table_collection_at_least_one_feature'                    => __( 'Add at least one product data row.', 'hostinger-affiliate-plugin' ),
            'create_table_collection_fill_all_names'                          => __( 'Name field not added to all product data rows.', 'hostinger-affiliate-plugin' ),
            'create_table_collection_select_all_types'                        => __( 'Type field not added to all product data rows.', 'hostinger-affiliate-plugin' ),
            'create_table_collection_at_least_one_row'                        => __( 'You need to add at least one row (title, select) to create a table. Check if all fields are filled.', 'hostinger-affiliate-plugin' ),
            'table_configuration_add_new_table'                               => __( 'Add a new table configuration', 'hostinger-affiliate-plugin' ),
            'table_configuration_use_saved_table_config'                      => __( 'Use the saved table configuration', 'hostinger-affiliate-plugin' ),
            'table_configuration_use_saved_table_config_description'          => __( 'You can edit the existing table configuration after this step', 'hostinger-affiliate-plugin' ),
            'table_configuration_choose_configuration'                        => __( 'Choose the table configuration', 'hostinger-affiliate-plugin' ),
            'list_no_matching_options'                                        => __( 'No matching options', 'hostinger-affiliate-plugin' ),
            'edit_table_configuration_save_changes_requirements'              => __( 'You need to add at least one row, two enabled products (max 5 products) to save changes. Check if all added fields are filled.', 'hostinger-affiliate-plugin' ),
            // translators: %s: ASIN.
            'duplicated_asin_stripped'                                        => __( 'Duplicate ASIN: %s was not added.', 'hostinger-affiliate-plugin' ),
            'table_collection_row_option_title'                               => __( 'Title', 'hostinger-affiliate-plugin' ),
            'table_collection_row_option_thumb'                               => __( 'Thumbnail', 'hostinger-affiliate-plugin' ),
            'table_collection_row_option_price'                               => __( 'Price', 'hostinger-affiliate-plugin' ),
            'table_collection_row_option_amazon_button'                       => __( 'More information', 'hostinger-affiliate-plugin' ),
            'asin_tooltip'                                                    => __( 'ASIN is a unique 10-digit code Amazon assigns to products in its catalog. You can find it on the product detail page.', 'hostinger-affiliate-plugin' ),
            'table_name'                                                      => __( 'Table name', 'hostinger-affiliate-plugin' ),
            'products'                                                        => __( 'Products', 'hostinger-affiliate-plugin' ),
            'rows'                                                            => __( 'Rows', 'hostinger-affiliate-plugin' ),
            'what_is_asin'                                                    => __( 'What\'s an ASIN code?', 'hostinger-affiliate-plugin' ),
            'product_visible'                                                 => __( 'Product visible', 'hostinger-affiliate-plugin' ),
            'table_collection_display_up_to'                                  => __( 'Table displays up to 5 products', 'hostinger-affiliate-plugin' ),
            'table_collection_paste_shortcode_text'                           => __( 'Paste the shortcode in Block Editor where you want the table to appear.', 'hostinger-affiliate-plugin' ),
            'name'                                                            => __( 'Name', 'hostinger-affiliate-plugin' ),
            'type'                                                            => __( 'Type', 'hostinger-affiliate-plugin' ),
            'australia'                                                       => __( 'Australia', 'hostinger-affiliate-plugin' ),
            'belgium'                                                         => __( 'Belgium', 'hostinger-affiliate-plugin' ),
            'brazil'                                                          => __( 'Brazil', 'hostinger-affiliate-plugin' ),
            'canada'                                                          => __( 'Canada', 'hostinger-affiliate-plugin' ),
            'egypt'                                                           => __( 'Egypt', 'hostinger-affiliate-plugin' ),
            'france'                                                          => __( 'France', 'hostinger-affiliate-plugin' ),
            'germany'                                                         => __( 'Germany', 'hostinger-affiliate-plugin' ),
            'india'                                                           => __( 'India', 'hostinger-affiliate-plugin' ),
            'italy'                                                           => __( 'Italy', 'hostinger-affiliate-plugin' ),
            'japan'                                                           => __( 'Japan', 'hostinger-affiliate-plugin' ),
            'mexico'                                                          => __( 'Mexico', 'hostinger-affiliate-plugin' ),
            'netherlands'                                                     => __( 'Netherlands', 'hostinger-affiliate-plugin' ),
            'poland'                                                          => __( 'Poland', 'hostinger-affiliate-plugin' ),
            'singapore'                                                       => __( 'Singapore', 'hostinger-affiliate-plugin' ),
            'saudi_arabia'                                                    => __( 'Saudi Arabia', 'hostinger-affiliate-plugin' ),
            'spain'                                                           => __( 'Spain', 'hostinger-affiliate-plugin' ),
            'sweden'                                                          => __( 'Sweden', 'hostinger-affiliate-plugin' ),
            'turkey'                                                          => __( 'Turkey', 'hostinger-affiliate-plugin' ),
            'united_arab_emirates'                                            => __( 'United Arab Emirates', 'hostinger-affiliate-plugin' ),
            'united_kingdom'                                                  => __( 'United Kingdom', 'hostinger-affiliate-plugin' ),
            'united_states'                                                   => __( 'United States', 'hostinger-affiliate-plugin' ),
        );
    }
}
