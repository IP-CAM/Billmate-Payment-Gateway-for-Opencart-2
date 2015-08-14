<?php
// Heading

$_['heading_title']      = 'Billmate Card';
$_['heading_cardpay'] = 'Billmate Card';
$_['text_billmate_cardpay'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/bm_kort_l.png" alt="Billmate" title="Billmate" height="35px" />';

// Text
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified Billmate Card payment module!';
$_['text_all_countries'] = 'All countries';

// Entry
$_['entry_merchant_id']     = 'Billmate ID:';
$_['entry_merchant_help']   = '(estore id) to use for the Billmate service (provided by Billmate).';
$_['latest_release']     = 'There is a new version released for this plugin';

$_['entry_secret']     = 'Billmate Key:';
$_['entry_secret_help']     = 'Shared secret to use with the Billmate service (provided by Billmate).';

$_['entry_description']     = 'Description:';

$_['entry_test']         = 'Test Mode:';
$_['entry_prompt_name']  = 'Display Name:';
$_['entry_3dsecure']     = 'Enable 3D Secure:';
$_['entry_total']        = 'Total:';
$_['help_total']         = '<br><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_order_status'] = 'Order Status:';
$_['entry_order_cancel_status'] = 'Cancelled Order Status:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';
$_['entry_available_countries'] = 'Available countries (autocomplete)';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify payment Billmate Card!';
$_['error_merchant_id']     = 'Billmate ID missing';
$_['error_secret']     = 'Billmate key missing';
$_['error_credentials']  = 'Please check your credentials';


$_['entry_transaction_method'] = 'Transaction Method';
$_['entry_billmate_cardpay_authorization'] = 'Authorization';
$_['entry_billmate_cardpay_sale'] = 'Sale';
