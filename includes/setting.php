<?php
/**
 * Created by NGUYEN TRONG CONG - PhpStorm.
 * User: NTC - 2DEV4U.COM
 * Date: 2/11/2018 - 10:52
 * Project Name: block-file-access
 */
if ( ! class_exists( 'BFA_Settings' ) ):
	class BFA_Settings {
		/**
		 * Constructor
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'bfa_settings_admin_menu' ) );

		}

		/* Setting Allow file access wp-content*/
		function bfa_settings_admin_menu() {
			add_options_page( 'Allow File Access', 'Allow File Access', 'manage_options', 'alfc-settings', array(
				$this,
				'bfa_settings_admin_menu_func'
			) );
		}

		function bfa_settings_admin_menu_func() {
			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			// check if the user have submitted the settings
			// wordpress will add the "settings-updated" $_GET parameter to the url
			if ( isset( $_GET['settings-updated'] ) ) {
				// add settings saved message with the class of "updated"
				add_settings_error( 'bfa_messages', 'bfa_message', __( 'Settings Saved', 'bfa' ), 'updated' );
			}
			// show error/update messages
			settings_errors( 'bfa_messages' );
			?>
            <div class="wrap">
		    <?php settings_fields( 'alfc-settings-group' ); ?>
				<?php do_settings_sections( 'alfc-settings-group' ); ?>

                <div class="alfc-settings-wrapper">
                    <!--top menu -->
                    <div class="alfc-settings-header">
                        <h1 class="alfc-page-settings-title"><?= esc_html( get_admin_page_title() ); ?></h1>

                        <div class="alfc-clear clear"></div>
                    </div>
                    <!-- /top menu-->
                    <div class="alfc-settings-container">
                        <?php
                        if ( isset( $_POST['submit'] ) ) {
	                        update_option( 'alfc_link_option', $_POST['alfc_link_option'] );
                        }
                        $alfc_link = ( get_option( 'alfc_link_option' ) ) ? get_option( 'alfc_link_option' ) : "";
                        ?>
                        <div class="alfc-tab-content">
                            <h4>Enter your file link permissions to access</h4>
                            <p>Enter more link separator by a line break</p>
                            <form method="post" action="">
                                <label for="alfc_link_option"></label>
                                <textarea name="alfc_link_option" id="alfc_link_option" class="large-text code" cols="50" rows="10"><?php echo $alfc_link; ?></textarea>
                                <div class="alfc-clear clear"></div>
	                            <?php submit_button(); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}

	}

	$bfa_settings = new BFA_Settings();
endif;