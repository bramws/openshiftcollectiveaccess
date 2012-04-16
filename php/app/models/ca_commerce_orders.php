<?php
/** ---------------------------------------------------------------------
 * app/models/ca_commerce_orders.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage models
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
 
 /**
   *
   */
require_once(__CA_MODELS_DIR__.'/ca_commerce_transactions.php');

BaseModel::$s_ca_models_definitions['ca_commerce_orders'] = array(
 	'NAME_SINGULAR' 	=> _t('order'),
 	'NAME_PLURAL' 		=> _t('orders'),
 	'FIELDS' 			=> array(
 		'order_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Order id', 'DESCRIPTION' => 'Identifier for order'
		),
		'transaction_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'DISPLAY_FIELD' => array('ca_commerce_transactions.short_description'),
				'DISPLAY_ORDERBY' => array('ca_commerce_transactions.created_on'),
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Transaction'), 'DESCRIPTION' => _t('Indicates the transaction to which the communication belongs.')
		),
		'order_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT,
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Order status'), 'DESCRIPTION' => _t('Status of order.'),
				'BOUNDS_CHOICE_LIST' => array(
					_t('open') => 0,
					_t('placed') => 1,
					_t('fulfilled') => 2,
					_t('closed') => 3
				)
		),
		
		'shipping_fname' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('First name'), 'DESCRIPTION' => _t('Ship to: first name.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'shipping_lname' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Last name'), 'DESCRIPTION' => _t('Ship to: last name.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'shipping_organization' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Organization'), 'DESCRIPTION' => _t('Ship to: organization.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'shipping_address1' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Address 1'), 'DESCRIPTION' => _t('Ship to: address - first line.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'shipping_address2' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Address 2'), 'DESCRIPTION' => _t('Ship to: address - second line.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'shipping_city' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('City'), 'DESCRIPTION' => _t('Ship to: city.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'shipping_address2' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('State/province'), 'DESCRIPTION' => _t('Ship to: state/province.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'shipping_postal_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Postal code'), 'DESCRIPTION' => _t('Ship to: postal code.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'shipping_country' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Country'), 'DESCRIPTION' => _t('Ship to: country.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'shipping_phone' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Phone'), 'DESCRIPTION' => _t('Ship to: phone.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'shipping_fax' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Fax'), 'DESCRIPTION' => _t('Ship to: fax.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'shipping_email' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Email'), 'DESCRIPTION' => _t('Ship to: email.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		
		'billing_fname' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('First name'), 'DESCRIPTION' => _t('Bill to: first name.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'billing_lname' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Last name'), 'DESCRIPTION' => _t('Bill to: last name.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'billing_organization' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Organization'), 'DESCRIPTION' => _t('Bill to: organization.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'billing_address1' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Address 1'), 'DESCRIPTION' => _t('Bill to: address - first line.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'billing_address2' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Address 2'), 'DESCRIPTION' => _t('Bill to: address - second line.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'billing_city' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('City'), 'DESCRIPTION' => _t('Bill to: city.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'billing_address2' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('State/province'), 'DESCRIPTION' => _t('Bill to: state/province.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'billing_postal_code' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Postal code'), 'DESCRIPTION' => _t('Bill to: postal code.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'billing_country' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Country'), 'DESCRIPTION' => _t('Bill to: country.'),
				'BOUNDS_LENGTH' => array(1,255)
		),
		'billing_phone' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Phone'), 'DESCRIPTION' => _t('Bill to: phone.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'billing_fax' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Fax'), 'DESCRIPTION' => _t('Bill to: fax.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'billing_email' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Email'), 'DESCRIPTION' => _t('Bill to: email.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		
		'payment_method' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Payment method'), 'DESCRIPTION' => _t('Method of payment.'),
				'BOUNDS_CHOICE_LIST' => array(
					_t('None') => 0,
					_t('Credit card') => 1,
					_t('Check') => 2,
					_t('Purchase order') => 3,
					_t('Cash') => 4
				)
		),
		'payment_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Payment status'), 'DESCRIPTION' => _t('Status of payment.'),
				'BOUNDS_CHOICE_LIST' => array(
					_t('Awaiting payment') => 0,
					_t('Processing') => 1,
					_t('Send invoice - awaiting reply') => 2,
					_t('Declined') => 3,
					_t('Received') => 4
				)
		),
		'payment_details' => array(
				'FIELD_TYPE' => FT_VARS, 'DISPLAY_TYPE' => DT_OMIT,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Payment details', 'DESCRIPTION' => 'Details of payment sent to payment gateway'
		),
		'payment_response' => array(
				'FIELD_TYPE' => FT_VARS, 'DISPLAY_TYPE' => DT_OMIT,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Payment response', 'DESCRIPTION' => 'Response from payment gateway'
		),
		'payment_received_on' => array(
				'FIELD_TYPE' => FT_DATETIME, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Payment received on'), 'DESCRIPTION' => _t('Date/time payment was received.'),
		),
		
  		'shipping_method' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Shipping method'), 'DESCRIPTION' => _t('Method by which order was shipped. The is the default method and can be overridden by the fulfillment method chosen on a per-item basis.'),
				'BOUNDS_CHOICE_LIST' => array(
					_t('USPS') => 0,
					_t('FEDEX Ground') => 1,
					_t('FEDEX Second Day') => 2,
					_t('FEDEX Overnight') => 3,
					_t('UPS Ground') => 4,
					_t('UPS Second Day') => 5,
					_t('UPS Overnight') => 6
				)
		),
  		'shipping_cost' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Shipping cost'), 'DESCRIPTION' => _t('Cost of shipping charged for the order.'),
		),
		'handling_cost' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Handling cost'), 'DESCRIPTION' => _t('Cost of handling charged for the order.'),
		),
		'shipping_notes' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 80, 'DISPLAY_HEIGHT' => 8,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Shipping notes'), 'DESCRIPTION' => _t('Notes pertaining to the shipment of the order.'),
				'BOUNDS_LENGTH' => array(1,65535)
		),
		'shipping_date' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Date order is scheduled to ship on'), 'DESCRIPTION' => _t('Date/time the order will be shipped.'),
		),
  		'shipped_on_date' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => true, 
				'DEFAULT' => '',
				'LABEL' => _t('Date order shipped on'), 'DESCRIPTION' => _t('Date/time the order was shipped.'),
		),
		'created_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Order created on'), 'DESCRIPTION' => _t('Date/time the order was created.'),
		),
 	)
);

class ca_commerce_orders extends BaseModel {
	# ---------------------------------
	# --- Object attribute properties
	# ---------------------------------
	# Describe structure of content object's properties - eg. database fields and their
	# associated types, what modes are supported, et al.
	#

	# ------------------------------------------------------
	# --- Basic object parameters
	# ------------------------------------------------------
	# what table does this class represent?
	protected $TABLE = 'ca_commerce_orders';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'order_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('order_id');

	# When the list of "list fields" above contains more than one field,
	# the LIST_DELIMITER text is displayed between fields as a delimiter.
	# This is typically a comma or space, but can be any string you like
	protected $LIST_DELIMITER = ' ';


	# What you'd call a single record from this table (eg. a "person")
	protected $NAME_SINGULAR;

	# What you'd call more than one record from this table (eg. "people")
	protected $NAME_PLURAL;

	# List of fields to sort listing of records by; you can use 
	# SQL 'ASC' and 'DESC' here if you like.
	protected $ORDER_BY = array('order_id');

	# If you want to order records arbitrarily, add a numeric field to the table and place
	# its name here. The generic list scripts can then use it to order table records.
	protected $RANK = '';
	
	# ------------------------------------------------------
	# Hierarchical table properties
	# ------------------------------------------------------
	protected $HIERARCHY_TYPE				=	null;
	protected $HIERARCHY_LEFT_INDEX_FLD 	= 	null;
	protected $HIERARCHY_RIGHT_INDEX_FLD 	= 	null;
	protected $HIERARCHY_PARENT_ID_FLD		=	null;
	protected $HIERARCHY_DEFINITION_TABLE	=	null;
	protected $HIERARCHY_ID_FLD				=	null;
	protected $HIERARCHY_POLY_TABLE			=	null;
	
	# ------------------------------------------------------
	# Change logging
	# ------------------------------------------------------
	protected $UNIT_ID_FIELD = null;
	protected $LOG_CHANGES_TO_SELF = false;
	protected $LOG_CHANGES_USING_AS_SUBJECT = array(
		"FOREIGN_KEYS" => array(
		
		),
		"RELATED_TABLES" => array(
		
		)
	);	
	
	
	# ------------------------------------------------------
	# $FIELDS contains information about each field in the table. The order in which the fields
	# are listed here is the order in which they will be returned using getFields()

	protected $FIELDS;
	
	# ----------------------------------------
	public function __construct($pn_id=null) {
		parent::__construct($pn_id);
	}
	# ----------------------------------------
}
?>