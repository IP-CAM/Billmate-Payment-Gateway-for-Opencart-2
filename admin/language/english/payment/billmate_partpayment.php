<?php
$_['heading_title'] = 'Billmate Part Payment';
$_['heading_partpayment'] = 'Billmate Part Payment';
$_['text_billmate_partpayment'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/bm_delbetalning_l.png" alt="Billmate" title="Billmate" height="35px" />';


// Text
$_['text_payment']          = 'Payment';
$_['tab_log']          = 'Logs';
$_['text_success']          = 'Success: You have modified Billmate Payment module!';
$_['text_billmate_invoice']   = '';
$_['text_live']             = 'No';
$_['text_beta']             = 'Yes';
$_['text_sweden']           = 'Sweden';
$_['text_norway']           = 'Norway';
$_['text_finland']          = 'Finland';
$_['text_denmark']          = 'Denmark';
$_['entry_description']     = 'Description:';
$_['entry_test']         = 'Test Mode:';
$_['latest_release']     = 'There is a new version released for this plugin';

// Entry
$_['entry_logo']         = 'Logo to be displayed in the invoice';
$_['entry_logo_help']         = 'Enter the name of the logo (shown in Billmate Online). Leave empty if you only have one logo.';

$_['entry_merchant']        = 'Billmate ID:';
$_['entry_merchant_help']   = 'To use Billmates services, you\'ll need a Billmate ID';
$_['entry_secret']          = 'Billmate Key:';
$_['entry_secret_help']     = 'To use Billmates services, you\'ll need a Billmate Key';
$_['entry_server']          = 'Testmode:';
$_['entry_mintotal']           = 'Minimum Total:';
$_['entry_mintotal_help']   = 'The minimum checkout total the order must reach before this payment method becomes active.';
$_['entry_maxtotal']           = 'Maximum Total:';
$_['entry_maxtotal_help']   =  'The maximum checkout total the order must reach before this payment method becomes active.';
$_['entry_pending_status']  = 'Pending Status:';
$_['entry_accepted_status'] = 'Accepted Status:';
$_['entry_geo_zone']        = 'Geo Zone:';
$_['entry_status']          = 'Activated:';
$_['entry_sort_order']      = 'Sort Order:';
$_['entry_invoice_fee']      = 'Invoice fee:';
$_['entry_invoice_fee_tax']      = 'Invoice fee tax class';
$_['entry_available_countries'] = 'Available countries (autocomplete)';

// Error
$_['error_permission']      = 'Warning: You do not have permission to modify payment Billmate Part Payment!';
$_['regen_pclasses'] = 'Save & Regen Pclasses';
$_['text_pclasses_updated'] = 'Successful update: fetched {count} PClasses from Billmate.';
$_['text_pclasses_updated_link'] = 'Would you like to view them?';
$_['no_pclasses_found'] = 'Couldnt find any saved paymentplans';
$_['error_credentials']  = 'Please check your credentials';
$_['error_merchant_id']     = 'Billmate ID missing';
$_['error_secret']     = 'Billmate key missing';

