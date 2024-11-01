<form id="yith_wcgeoip_rules_form"
      method="post">
    <div id="yith_wcgeoip_rules_panel">
        <input type="hidden"
               name="_data_to_save"
               id="_data_to_save"
               class="_data">
        <input type="hidden"
               name="_data_to_remove"
               id="_data_to_remove"
               class="_data">
        <table class="rules-options wp-list-table widefat">
            <thead>
            <tr>
                <th><i class="dashicons dashicons-move"></i></th>
                <th class="option-country"><?php _e( 'Country', 'yith-geoip-language-redirect-for-woocommerce' ) ?></th>
                <th class="option-country_excluded"><?php _e( 'Country excluded', 'yith-geoip-language-redirect-for-woocommerce' );
					echo
					wc_help_tip( __( 'Exclude the selected country and apply the rule to all other countries',
						'yith-geoip-language-redirect-for-woocommerce' ), false ); ?></th>
                <th class="option-origin" colspan="2"><?php _e( 'Origin', 'yith-geoip-language-redirect-for-woocommerce' ) ?></th>
                <th class="option-destination" colspan="2"><?php _e( 'Destination', 'yith-geoip-language-redirect-for-woocommerce' ) ?></th>
                <th class="option-status"><?php _e( 'HTTP Status Code', 'yith-geoip-language-redirect-for-woocommerce' ) ?></th>
                <th class="option-only_one"><?php _e( 'First time redirect', 'yith-geoip-language-redirect-for-woocommerce' );
					echo wc_help_tip(
						__( 'Check this if you want to redirect users only the first time they visit the URL. No redirect will be performed until the cookie expires',
							'yith-geoip-language-redirect-for-woocommerce' ), false ); ?></th>
				<?php do_action( 'yith_wcgeoip_rule_before_option_actions_title' ) ?>
                <th class="option-actions"><?php _e( 'Actions', 'yith-geoip-language-redirect-for-woocommerce' ) ?></th>
            </tr>
            </thead>

            <tbody class="yith_geoip_table_rules">
			<?php
			foreach ( $rules as $index => $rule_row ) {
				$args = array(
					'index'    => $index,
					'rule_row' => $rule_row,
				);
				yith_wcgeoip_get_template( 'rule-row', $args, 'admin' );
			}
			?>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="<?php echo apply_filters( 'yith_wcgeoip_rules_add_rule_button_colspan', 9 ); ?>">
                    <button id="_add_rule_row" class="button add_rule_button">
                        <i class="dashicons dashicons-plus"></i>
						<?php _e( 'New rule', 'yith-geoip-language-redirect-for-woocommerce' ) ?>
                    </button>
                </td>
                <td>
                    <button id="_save_rules" class="button save_rules button-primary button-large">
						<?php _e( 'Save rules', 'yith-geoip-language-redirect-for-woocommerce' ) ?>
                    </button>
                </td>
            </tr>
            </tfoot>
        </table>

    </div>
</form>