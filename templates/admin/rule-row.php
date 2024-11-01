<tr id="<?php echo $index ?>"
    class="yith_wcgeoip_rule_row">
    <input type="hidden"
           name="_rules[<?php echo $index ?>][rule_ID]"
           id="_rules_<?php echo $index ?>_rule_ID"
           class="_rule"
           value="<?php echo isset( $rule_row['ID'] ) ? $rule_row['ID'] : 'new' ?>"
           placeholder="">
    <input type="hidden"
           name="_rules[<?php echo $index ?>][order]"
           id="_rules_<?php echo $index ?>_rule_order"
           class="_rule _rule_order"
           value="<?php echo isset( $rule_row['order'] ) ? $rule_row['order'] : $index ?>"
           placeholder="">
    <td class="drag-icon">
        <i class="dashicons dashicons-move"></i>
    </td>
    <td class="form-field _rules_<?php echo $index ?>_country_field option-country forminp">
		<?php $countries = WC()->countries->countries; ?>
        <select
            name="_rules[<?php echo $index ?>][country]"
            id="_rules_<?php echo $index ?>_country"
            class="wc-enhanced-select _rule"
            data-placeholder="<?php esc_attr_e( 'Choose country;', 'yith-geoip-language-redirect-for-woocommerce' ); ?>"
            title="<?php esc_attr_e( 'Country', 'yith-geoip-language-redirect-for-woocommerce' ) ?>"
        >
			<?php
			if ( ! empty( $countries ) ) {
				foreach ( $countries as $key => $val ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, isset( $rule_row['country'] ) ? $rule_row['country'] : '' ) . '>' . $val . '</option>';
				}
			}
			?>
        </select>
    </td>
    <td class="form-field _rules_<?php echo $index ?>_country_excluded_field option-country_excluded">
        <input type="checkbox" class="checkbox _rule" style=""
               name="_rules[<?php echo $index ?>][country_excluded]"
               id="_rules_<?php echo $index ?>_country_excluded"
			<?php
			checked( isset( $rule_row['country_excluded'] ) && '1' == $rule_row['country_excluded'] ) ?> >
    </td>
	<?php
	do_action( 'yith_wcgeoip_rule_before_origin', $index, $rule_row );
	?>
    <td class="form-field _rules_<?php echo $index ?>_origin_field option-origin"
        colspan="<?php echo apply_filters( 'yith_wcgeoip_option_origin_colspan', 2 ) ?>"
    >
        <input type="text" style=""
               name="_rules[<?php echo $index ?>][origin]"
               id="_rules_<?php echo $index ?>_origin"
               class="finder _rule"
               value="<?php echo isset( $rule_row['origin'] ) ? $rule_row['origin'] : '' ?>"
               placeholder="" required="false">
    </td>
	<?php
	do_action( 'yith_wcgeoip_rule_before_destination', $index, $rule_row );
	?>
    <td class="form-field _rules_<?php echo $index ?>_destination_field option-destinantion"
        colspan="<?php echo apply_filters( 'yith_wcgeoip_option_destination_colspan', 2 ) ?>"
    >
        <input type="text" style=""
               name="_rules[<?php echo $index ?>][destination]"
               id="_rules_<?php echo $index ?>_destination"
               class="finder _rule"
               value="<?php echo isset( $rule_row['destination'] ) ? $rule_row['destination'] : '%path%' ?>"
               placeholder="" required="false">
    </td>
    <td class="form-field _rules_<?php echo $index ?>_status_field option-status">
        <select name="_rules[<?php echo $index ?>][status]"
                id="_rules_<?php echo $index ?>_status"
                class="_rule">
			<?php
			$status_list = yith_wcgeoip_get_status_list();
			foreach ( $status_list as $key => $status_item ) {
				?>
                <option value="<?php echo $key ?>" <?php selected( isset( $rule_row['status'] ) && $rule_row['status'] == $key ) ?> ><?php echo $key . ' - ' . $status_item['desc'] ?></option>
				<?php
			}
			?>
        </select>
    </td>
    <td class="form-field _rules_<?php echo $index ?>_only_one_field option-only_one">
        <input type="checkbox" class="checkbox _rule" style=""
               name="_rules[<?php echo $index ?>][only_one]"
               id="_rules_<?php echo $index ?>_only_one"
			<?php
			checked( isset( $rule_row['only_one'] ) && '1' == $rule_row['only_one'] ) ?> >
    </td>
	<?php
	do_action( 'yith_wcgeoip_rule_before_option_actions', $index, $rule_row );
	?>
    <td class="option-actions">
        <button class="button remove_rule"><?php echo __( 'Remove', 'yith-geoip-language-redirect-for-woocommerce' ) ?></button>
    </td>
</tr>