<?php
/*
Plugin Name: ￼Shopp Customer Discount Use Rule
Plugin URI: ￼https://cgd.io
Description:  Add rule option to limit the number of times one customer (determined by email address and previous orders) can use a discount code.
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2013-2016 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class CGD_ShoppAdvancedDiscountRules￼ {
	private $_codesapplied = 0;

	public function __construct() {} // silence is golden?

	function start() {
		add_action('shopp_init', array($this, 'shopp_init') );
	}

	function shopp_init() {
		// Add discount rule to admin dropdown
		add_filter('shopp_discount_rules', array($this, 'add_discount_rules_admin') );

		// Set discount rule logic
		add_filter('shopp_discount_conditions', array($this, 'set_discount_rules_logic') );

		// Filter the rule subject
		add_filter('shopp_discounts_subject_' . sanitize_key('Customer discount use count'), array($this, 'set_discount_rule_subject'), 10, 2 );

		// Catch number of codes applied at submit
		add_action('shopp_process_checkout', array($this, 'catch_number_of_applied_codes'), 1 );

		// Validate discounts during checkout submit
		add_filter('shopp_checkout_processed', array($this, 'detect_unmatched_promos'), 10, 1 );
	}

	function add_discount_rules_admin( $rules ) {
		$rules['Customer discount use count'] = 'Customer discount use count';

		return $rules;
	}

	function set_discount_rules_logic( $conditions ) {
		$conditions['Cart']['Customer discount use count'] = array('logic' => array('boolean', 'amount'), 'value' => 'number');
		$conditions['Cart Item']['Customer discount use count'] = array('logic' => array('boolean', 'amount'), 'value' => 'number');

		return $conditions;
	}

	function set_discount_rule_subject( $subject, $promo ) {
		global $wpdb;

		$purchase_table = ShoppDatabaseObject::tablename(ShoppPurchase::$table);
		$subject = 0;

		if ( ! empty( ShoppCustomer()->email ) && is_email( ShoppCustomer()->email ) ) {
			$orders = $wpdb->get_results( $wpdb->prepare("SELECT id FROM $purchase_table WHERE email = %s", ShoppCustomer()->email) );

			foreach($orders as $o) {
				$discounts = shopp_meta($o->id, 'purchase', 'discounts');

				foreach($discounts as $d) {
					if ( $d->id == $promo->id ) {
						$subject = $subject + 1;
					}
				}
			}
		}

		return $subject;
	}

	function catch_number_of_applied_codes() {
		$this->_codesapplied = count( ShoppOrder()->Discounts->codes() );
	}

	function detect_unmatched_promos() {
		$post_match_count = count( ShoppOrder()->Discounts->codes() );

		if ( $this->_codesapplied > $post_match_count ) {
			shopp_add_error(__('One or more promotions no longer applies.', 'Shopp'), SHOPP_TRXN_ERR);
		}
	}
}

$CGD_ShoppAdvancedDiscountRules￼ = new CGD_ShoppAdvancedDiscountRules￼();
$CGD_ShoppAdvancedDiscountRules￼->start();
