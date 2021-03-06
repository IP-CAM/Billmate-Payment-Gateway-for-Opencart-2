<?php

require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';

class ControllerPaymentBillmateBankpay extends Controller {
	public function cancel(){

        if(version_compare(VERSION,'2.0.0','>='))
            $this->response->redirect($this->url->link('checkout/checkout'));
        else
		    $this->redirect($this->url->link('checkout/checkout'));
	}
	public function index() {
        $this->language->load('payment/billmate_bankpay');

        if( !empty($this->session->data['order_created']) ) $this->session->data['order_created'] = '';
				
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_wait'] = $this->language->get('text_wait');
        $this->load->model('checkout/order');

        $data['description'] = $this->config->get('billmate_cardpay_description');


        if(version_compare(VERSION,'2.0.0','>=')){

            $prefix = (version_compare(VERSION,'2.2.0','>=')) ? '' : 'default/template/';
            $preTemplate = (version_compare(VERSION,'2.2.0','>=')) ? '' : $this->config->get('config_template') . '/template/';

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oc2/billmate_bankpay.tpl')) {
                return $this->load->view($preTemplate. 'payment/oc2/billmate_bankpay.tpl',$data);
            } else {

                return $this->load->view($prefix.'payment/oc2/billmate_bankpay.tpl',$data);
            }
        } else {
            $this->data = $data;
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay.tpl';
            } else {
                $this->template = 'default/template/payment/billmate_bankpay.tpl';
            }

            $this->render();
        }



	}
	
	public function accept() {
		$this->language->load('payment/billmate_bankpay');

		$error_msg = '';

		$post = empty($this->request->post)? $this->request->get : $this->request->post;
        $eid = (int)$this->config->get('billmate_bankpay_merchant_id');

        $key = $this->config->get('billmate_bankpay_secret');

        require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
        $k = new BillMate($eid,$key);
        if(is_array($post))
        {
            foreach($post as $key => $value)
                $post[$key] = htmlspecialchars_decode($value,ENT_COMPAT);
        }

        $post = $k->verify_hash($post);
		if(isset($post['orderid']) && isset($post['status']) ) {



                        	$order_id = $post['orderid'];
                        	$this->load->model('checkout/order');
                        	$order_info = $this->model_checkout_order->getOrder($order_id);
                        
                        	if ($order_info) {
                                if (($post['status'] == 'Paid' ) && $order_info['order_status_id'] != $this->config->get('billmate_bankpay_order_status_id') && !$this->cache->get('order'.$order_id)) {
                                    $this->cache->set('order'.$order_id,1);
                                    if(version_compare(VERSION,'<','2.0'))
                                        $this->model_checkout_order->confirm($order_id, $this->config->get('billmate_bankpay_order_status_id'));

                                    $msg = '';
                                    if (isset($post['number'])) {
                                            $msg .= 'invoice_id: ' . $post['number'] . "\n";
                                    }
                                    if( isset($post['status'])) {
                                            $msg .= 'status: '. $post['status'] . "\n";
                                    }
                                    if(version_compare(VERSION,'2.0','<'))
                                        $this->model_checkout_order->update($order_id, $this->config->get('billmate_bankpay_order_status_id'), $msg, false);
                                    else
                                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('billmate_bankpay_order_status_id',$msg,true));


                                    $this->cache->delete('order'.$order_id);
                                } else if($post['status'] == 'Pending'){
                                    $this->cache->set('order'.$order_id,1);
                                    if(version_compare(VERSION,'2.0','<'))
                                        $this->model_checkout_order->confirm($order_id, 1);
                                    /* Set STatus to pending */
                                    $msg = '';
                                    if (isset($post['number'])) {
                                        $msg .= 'invoice_id: ' . $post['number'] . "\n";
                                    }
                                    if( isset($post['status'])) {
                                        $msg .= 'status: '. $post['status'] . "\n";
                                    }
                                    if(version_compare(VERSION,'2.0.0','>='))
                                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('billmate_bankpay_order_status_id'),$msg,false);
                                    else
                                        $this->model_checkout_order->confirm($order_id, $this->config->get('billmate_bankpay_order_status_id'), $msg, 1);


                                    $this->cache->delete('order'.$order_id);
                                } else if($post['status'] == 'Failed'){
                                    $error_msg = $this->language->get('text_failed');
                                }
                                else {
                                    $error_msg = ($order_info['order_status_id'] == $this->config->get('billmate_bankpay_order_status_id')) ? '' :$this->language->get('text_declined');
                                }
                        	} else {
					$error_msg = $this->language->get('text_unable');
				}

		} else {
			$error_msg = $this->language->get('text_failed');
		}

        if($post['status'] == 'Cancelled'){
            $error_msg = $post['error_message'];
        }
		if( $error_msg != '' ) {
			$data['heading_title'] = $this->language->get('text_failed');
                        $data['text_message'] = sprintf($this->language->get('text_error_msg'), $error_msg, $this->url->link('information/contact'));
                        $data['button_continue'] = $this->language->get('button_continue');
                        $data['continue'] = $this->url->link('common/home');
                        if(version_compare(VERSION,'2.0.0','>=')){

                            $data['column_left'] = $this->load->controller('common/column_left');
                            $data['header'] = $this->load->controller('common/header');
                            $data['footer'] = $this->load->controller('common/footer');
                            $data['content_top'] = $this->load->controller('common/content_top');
                            $data['content_bottom'] = $this->load->controller('common/content_bottom');
                            $data['column_right'] = $this->load->controller('common/column_right');
                            $prefix = (version_compare(VERSION,'2.2.0','>=')) ? '' : 'default/template/';
                            $preTemplate = (version_compare(VERSION,'2.2.0','>=')) ? '' : $this->config->get('config_template') . '/template/';

                            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                                return $this->load->view($preTemplate . 'payment/billmate_bankpay_failure.tpl',$data);
                            } else {
                                return $this->load->view($prefix.'payment/billmate_bankpay_failure.tpl',$data);
                            }
                        } else {
                            $this->data = $data;
                            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                                $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl';
                            } else {
                                $this->template = 'default/template/payment/billmate_bankpay_failure.tpl';
                            }

                            $this->children = array(
                                'common/column_left',
                                'common/column_right',
                                'common/content_top',
                                'common/content_bottom',
                                'common/footer',
                                'common/header'
                            );

                            $this->response->setOutput($this->render());
                        }
		} else {
			try{
                if(version_compare(VERSION,'2.0.0','>='))
                    $this->response->redirect($this->url->link('checkout/success'));
                else
                    $this->redirect($this->url->link('checkout/success'));
			}catch(Exception $ex ){
					$data['heading_title'] = $this->language->get('text_failed');
					$data['text_message'] = sprintf($this->language->get('text_error_msg'), $ex->getMessage(), $this->url->link('information/contact'));
					$data['button_continue'] = $this->language->get('button_continue');
					$data['continue'] = $this->url->link('common/home');

                if(version_compare(VERSION,'2.0.0','>=')){

                    $data['column_left'] = $this->load->controller('common/column_left');
                    $data['header'] = $this->load->controller('common/header');
                    $data['footer'] = $this->load->controller('common/footer');
                    $data['content_top'] = $this->load->controller('common/content_top');
                    $data['content_bottom'] = $this->load->controller('common/content_bottom');
                    $data['column_right'] = $this->load->controller('common/column_right');
                    $prefix = (version_compare(VERSION,'2.2.0','>=')) ? '' : 'default/template/';
                    $preTemplate = (version_compare(VERSION,'2.2.0','>=')) ? '' : $this->config->get('config_template') . '/template/';
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                        return $this->load->view($preTemplate . 'payment/billmate_bankpay_failure.tpl',$data);
                    } else {
                        return $this->load->view($prefix.'payment/billmate_bankpay_failure.tpl',$data);
                    }
                } else {
                    $this->data = $data;
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl';
                    } else {
                        $this->template = 'default/template/payment/billmate_bankpay_failure.tpl';
                    }

                    $this->children = array(
                        'common/column_left',
                        'common/column_right',
                        'common/content_top',
                        'common/content_bottom',
                        'common/footer',
                        'common/header'
                    );

                    $this->response->setOutput($this->render());
                }
			}
		}
	}

    public function fixStupidOpencartClean($data){
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$key] = $this->fixStupidOpencartClean($value);
            }
        } else {
            $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
        }
        return $data;
    }
	public function callback() {

        $_POST = file_get_contents('php://input');
        if (empty($_POST)) {
            $post = $_GET;
            foreach ($post AS $key => $val) {
                $post[$key] = urldecode($val);
            }
        } else {
            $_POST = $this->fixStupidOpencartClean($_POST);
            $post = $_POST;
        }

        $this->request->post = $post;
        $this->load->model('checkout/order');
        $eid = (int)$this->config->get('billmate_bankpay_merchant_id');

        $key = $this->config->get('billmate_bankpay_secret');

        require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
        $k = new BillMate($eid,$key);
        $post = $k->verify_hash($post);

        if(isset($post['orderid']) && isset($post['status']) && isset($post['number'])){


            $order_info = $this->model_checkout_order->getOrder($post['orderid']);



            if(($post['status'] == 'Paid' && ($order_info && $order_info['order_status_id'] != $this->config->get('billmate_bankpay_order_status_id'))) && (NULL == $this->cache->get('order'.$post['orderid']))){
                $this->cache->set('order'.$post['orderid'],1);

                $order_id = $post['orderid'];
                if(version_compare(VERSION,'2.0','<'))
                    $this->model_checkout_order->confirm($order_id, $this->config->get('billmate_bankpay_order_status_id'));

                $msg = '';
                $msg .= 'invoice_id: ' . $post['number'] . "\n";
                $msg .= 'status: '. $post['status'] . "\n";
                if(version_compare(VERSION,'2.0.0','>='))
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('billmate_bankpay_order_status_id'),$msg,false);
                else
                    $this->model_checkout_order->update($order_id, $this->config->get('billmate_bankpay_order_status_id'), $msg, false);

                $this->cache->delete('order'.$post['orderid']);

            }else if(($post['status'] == 'Pending' && ($order_info && $order_info['order_status_id'] != $this->config->get('billmate_bankpay_order_status_id'))) && (NULL === $this->cache->get('order'.$post['orderid']))){
                $this->cache->set('order'.$post['orderid'],1);

                $order_id = $post['orderid'];
                if(version_compare(VERSION,'<','2.0'))
                    $this->model_checkout_order->confirm($order_id, 1);
                /* Set STatus to pending */
                $msg = '';
                if (isset($post['number'])) {
                    $msg .= 'invoice_id: ' . $post['number'] . "\n";
                }
                if( isset($post['status'])) {
                    $msg .= 'status: '. $post['status'] . "\n";
                }
                if(version_compare(VERSION,'<','2.0'))
                    $this->model_checkout_order->update($order_id, 1,$msg, false);
                else
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('billmate_bankpay_order_status_id',$msg,false));

                $this->cache->delete('order'.$post['orderid']);
            } elseif(NULL != $this->cache->get('order'.$post['orderid'])) {

                die('ERROR');
            }
        }

        if(version_compare(VERSION,'2.0.0','>=')){
            $prefix = (version_compare(VERSION,'2.2.0','>=')) ? '' : 'default/template/';
            $preTemplate = (version_compare(VERSION,'2.2.0','>=')) ? '' : $this->config->get('config_template') . '/template/';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl')) {
                return $this->load->view($preTemplate . 'payment/billmate_bankpay_callback.tpl');
            } else {
                return $this->load->view($prefix.'payment/billmate_bankpay_callback.tpl');
            }
        }else {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl';
            } else {
                $this->template = 'default/template/payment/billmate_bankpay_callback.tpl';
            }
            $this->response->setOutput($this->render());
        }
	}
	public function sendinvoice(){
        $this->language->load('payment/billmate_bankpay');


        $post = empty($this->request->post)? $this->request->get : $this->request->post;
		
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;
		
		if( !empty( $post['order_id'] ) ){
			$order_id = $post['order_id'];
		} else {
			$order_id = $this->session->data['order_id'];
		}
		// Fix for checkouts that creates new orders for every action.
		// Check if order is Created AND old_order_id is equal to $order_id
		if( !empty($this->session->data['order_created']) && isset($this->session->data['old_order_id']) && $this->session->data['old_order_id'] == $order_id ) return;

		// If order_id not equal old_order_id reset order_api_called to force order to be sent again.
		if(isset($this->session->data['old_order_id']) && $this->session->data['old_order_id'] != $order_id){

			$this->session->data['order_api_called'] = '';
		}
        $this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if( !empty( $this->session->data["shipping_method"] ) )
		$shipping_method = $this->session->data["shipping_method"];
		
		require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
		
		$eid = (int)$this->config->get('billmate_bankpay_merchant_id');
		
		$key = $this->config->get('billmate_bankpay_secret');
		$ssl = true;

		$debug = false;

        if(!defined('BILLMATE_SERVER')) define('BILLMATE_SERVER','2.1.10');
        if(!defined('BILLMATE_CLIENT')) define('BILLMATE_CLIENT','Opencart:Billmate:2.2.0');
        if(!defined('BILLMATE_LANGUAGE')) define('BILLMATE_LANGUAGE',($this->language->get('code') == 'se') ? 'sv' : $this->language->get('code'));
		$k = new BillMate($eid,$key,$ssl,$this->config->get('billmate_bankpay_test') == 1 ,$debug);
		$values['PaymentData'] = array(
            'method' => 16,
            'currency' => $this->session->data['currency'],
            'language' => ($this->language->get('code') == 'se') ? 'sv' : $this->language->get('code'),
            'country' => 'SE',
            'autoactivate' => 0,
            'orderid' => $order_id,
            'logo' => (strlen($this->config->get('billmate_bankpay_logo')) > 0) ? $this->config->get('billmate_bankpay_logo') : ''
        );

		$values['PaymentInfo'] = array(
            'paymentdate' => date('Y-m-d')
        );

        $values['Card'] = array(
            'callbackurl' => $this->url->link('payment/billmate_bankpay/callback', '', true),
            'accepturl' => $this->url->link('payment/billmate_bankpay/accept', '', true),
            'cancelurl' => $this->url->link('payment/billmate_bankpay/cancel', '', true),
            'returnmethod' => 'GET'
        );
        $values['Customer']['nr'] = $this->customer->getId();
		$values['Customer']['Shipping'] = array(
			'email'           => $order_info['email'],
			'firstname'           => $order_info['shipping_firstname'],
			'lastname'           => $order_info['shipping_lastname'],
			'company'         => $order_info['shipping_company'],
			'street'          => $order_info['shipping_address_1'],
			'zip'             => $order_info['shipping_postcode'],
			'city'            => $order_info['shipping_city'],
			'country'         => $order_info['shipping_iso_code_2'],
            'phone'           => $order_info['telephone']
		);
		
		$values['Customer']['Billing'] = array(
			'email'           => $order_info['email'],
			'firstname'           => $order_info['payment_firstname'],
			'lastname'           => $order_info['payment_lastname'],
			'company'         => $order_info['payment_company'],
			'street'          => $order_info['payment_address_1'],
			'zip'             => $order_info['payment_postcode'],
			'city'            => $order_info['payment_city'],
			'country'         => $order_info['payment_iso_code_2'],
            'phone'           => $order_info['telephone']


        );

		$products = $this->cart->getProducts();
		$prepareDiscount = array();
		$subtotal = 0;
		$prepareProductDiscount = array();
		$productTotal = 0;
        $orderTotal = 0;
        $taxTotal = 0;
        $myocRounding = 0;

        foreach ($products as $product) {

            $product_total_qty = $product['quantity'];

            if ($product['minimum'] > $product_total_qty) {
                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
            }
            $rates=0;

            $price = $product['price'];
            $price = $this->currency->format($price, $order_info['currency_code'], $order_info['currency_value'], false);
            $tax_rates = $this->tax->getRates($price,$product['tax_class_id']);
            foreach($tax_rates as $rate){
                $rates+= $rate['rate'];
            }
            $title = $product['name'];
            if(count($product['option']) > 0){
                foreach($product['option'] as $option){

                    if(version_compare(VERSION,'2.0','>=')){
                        $title .= ' - ' . $option['name'] . ': ' . $option['value'];
                    } else {
                        $title .= ' - ' . $option['name'] . ': ' . $option['option_value'];
                    }
                }
            }
            $productValue = $this->currency->format($price, $order_info['currency_code'], $order_info['currency_value'], false);
            $values['Articles'][] = array(
                'quantity'   => (int)$product_total_qty,
                'artnr'    => $product['model'],
                'title'    => $title,
                'aprice'    => $price * 100,
                'taxrate'      => (float)($rates),
                'discount' => 0.0,
                'withouttax'    => $product_total_qty * ($price *100),

            );
            $orderTotal += $product_total_qty * ($price *100);
            $taxTotal += ($product_total_qty * ($price *100)) * ($rates/100);

            $subtotal += ($price * 100) * $product_total_qty;
            $productTotal += ($price * 100) * $product_total_qty;
            if(isset($prepareDiscount[$rates])){
                $prepareDiscount[$rates] += ($price * 100) * $product_total_qty;
            } else {
                $prepareDiscount[$rates] = ($price * 100) * $product_total_qty;
            }
            if(isset($prepareProductDiscount[$rates])){
                $prepareProductDiscount[$rates] += ($price * 100) * $product_total_qty;
            } else {
                $prepareProductDiscount[$rates] = ($price * 100) * $product_total_qty;
            }
        }


        /** Gift vouchers */
        if (    isset($this->session->data['vouchers']) AND
                is_array($this->session->data['vouchers']) AND
                count($this->session->data['vouchers']) > 0
        ) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                if (    isset($voucher['amount']) AND
                        isset($voucher['description']) AND
                        $voucher['amount'] > 0 AND
                        $voucher['description'] != ''
                ) {
                    /**
                     * Have 1 quantity, 0 tax and is not affected by discounts and only increase value of $orderTotal
                     */
                    $_aprice = $voucher['amount'] * 100;
                    $values['Articles'][] = array(
                        'quantity'      => 1,
                        'artnr'         => $key,
                        'title'         => $voucher['description'],
                        'aprice'        => round($_aprice),
                        'taxrate'       => 0,
                        'discount'      => 0,
                        'withouttax'    => $_aprice
                    );
                    $orderTotal += $_aprice;
                }
            }
        }


        $totals = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = ".$order_id);
        $billmate_tax = array();
        $total_data = array();
        $total = 0;
        $totals = $totals->rows;

        foreach ($totals as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                if(version_compare(VERSION,'2.3','>=') && $result['code'] != 'billmate_fee') {
                    $this->load->model('extension/total/' . $result['code']);
                } else {
                    $this->load->model('total/' . $result['code']);

                }

                $taxes = array();

                if (function_exists('create_function') && version_compare(phpversion(), "7.2", "<")) {
                    // Temporarily disable error handling
                    $func = create_function('','');
                    $oldhandler = set_error_handler($func);
                }

                $totalArr = false;
                if(version_compare(VERSION,'2.2','>=')){
                    $totalArr = array('total_data' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);
                    if(version_compare(VERSION,'2.3','>=') && $result['code'] != 'billmate_fee'){
                        $this->{'model_extension_total_'.$result['code']}->getTotal($totalArr);

                    } else {
                        $this->{'model_total_'.$result['code']}->getTotal($totalArr);

                    }
                }
                else
                    $this->{'model_total_'.$result['code']}->getTotal($total_data, $total, $taxes);

                if (function_exists('create_function') && version_compare(phpversion(), "7.2", "<")) {
                    // Re-enable error handling
                    set_error_handler($oldhandler);
                }

                $amount = 0;
                if(isset($totalArr) && $totalArr != false)
                    extract($totalArr);
                foreach ($taxes as $tax_id => $value) {
                    $amount += $value;
                }

                $billmate_tax[$result['code']] = $amount;
            }
        }

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];

            if (isset($billmate_tax[$value['code']])) {
                if ($billmate_tax[$value['code']]) {
                    $totals[$key]['tax_rate'] = abs($billmate_tax[$value['code']] / $value['value'] * 100);
                } else {
                    $totals[$key]['tax_rate'] = 0;
                }
            } else {
                $totals[$key]['tax_rate'] = '0';
            }
        }

        foreach ($totals as $total) {
            if ($total['code'] != 'sub_total' && $total['code'] != 'tax' && $total['code'] != 'total' && $total['code'] != 'coupon') {

                $total['value'] = round( $total['value'], 2 );
                $totalTypeTotal = $this->currency->format($total['value']*100, $order_info['currency_code'], $order_info['currency_value'], false);
                if($total['code'] != 'billmate_fee' && $total['code'] != 'shipping'){
                    if($total['code'] != 'myoc_price_rounding') {
                        $values['Articles'][] = array(
                            'quantity' => 1,
                            'artnr' => '',
                            'title' => $total['title'],
                            'aprice' => $totalTypeTotal,
                            'taxrate' => (float)$total['tax_rate'],
                            'discount' => 0.0,
                            'withouttax' => $totalTypeTotal,
                        );
                        $orderTotal += $totalTypeTotal;
                        $taxTotal += $totalTypeTotal * ($total['tax_rate'] / 100);
                    } else {
                        $myocRounding = $totalTypeTotal;
                    }
                }
                if($total['code'] == 'shipping'){
                    if($total['value'] > 0) {
                        $values['Cart']['Shipping'] = array(
                            'withouttax' => $this->currency->format($total['value'], $order_info['currency_code'],
                                    $order_info['currency_value'], false) * 100,
                            'taxrate' => $total['tax_rate']
                        );
                        $orderTotal += $this->currency->format($total['value'], $order_info['currency_code'],
                                $order_info['currency_value'], false) * 100;
                        $taxTotal += ($this->currency->format($total['value'], $order_info['currency_code'],
                                    $order_info['currency_value'], false) * 100) * ($total['tax_rate'] / 100);
                    }
                }
                if($total['code'] == 'billmate_fee'){
                    if($total['value'] > 0) {
                        $values['Cart']['Handling'] = array(
                            'withouttax' => $this->currency->format($total['value'], $order_info['currency_code'],
                                    $order_info['currency_value'], false) * 100,
                            'taxrate' => $total['tax_rate']
                        );
                        $orderTotal += $this->currency->format($total['value'], $order_info['currency_code'],
                                $order_info['currency_value'], false) * 100;
                        $taxTotal += ($this->currency->format($total['value'], $order_info['currency_code'],
                                    $order_info['currency_value'], false) * 100) * ($total['tax_rate'] / 100);
                    }
                }


                if($total['code'] != 'myoc_price_rounding' )
                {
                    if (isset($prepareDiscount[$total['tax_rate']]))
                        $prepareDiscount[$total['tax_rate']] += $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                    else
                        $prepareDiscount[$total['tax_rate']] = $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                    $subtotal += $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                }
            }
        }

        if(isset($this->session->data['advanced_coupon'])){
            $coupon = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'advanced_coupon' AND order_id = ".$this->session->data['order_id']);
            $total = $coupon->row;

            $this->load->model('checkout/advanced_coupon');
            $codes = array_unique($this->session->data['advanced_coupon']);
            foreach ($codes as $code) {
                # code...
                $coupons_info[] = $this->model_checkout_advanced_coupon->getAdvancedCoupon($code);
            }

            if(isset($coupons_info)){
                foreach($coupons_info as $coupon_info){
                    if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F' || $coupon_info['type'] == 'FP') && $coupon_info['shipping'] == 1)
                    {
                        $shipping = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'shipping' AND order_id = ".$this->session->data['order_id']);
                        $shipping = $shipping->row;
                        $taxes = array();
                        $total = 0;
                        $total_data = array();
                        $shippingtax = 0;
                        if ($this->config->get($shipping['code'].'_status'))
                        {
                            $this->load->model('total/'.$shipping['code']);

                            if(version_compare(VERSION,'2.2','>=')) {
                                $totalArr = array('total_data' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);
                                $this->{'model_total_' . $result['code']}->getTotal($totalArr);
                            }
                            else
                                $this->{'model_total_'.$shipping['code']}->getTotal($total_data, $total, $taxes);

                            if(isset($totalArr))
                                extract($totalArr);
                            
                            foreach ($taxes as $key => $value)
                            {
                                $shippingtax += $value;
                            }
                            $shippingtax = $shippingtax / $shipping['value'];

                        }
                        if($total['value'] < $shipping['value'])
                        {

                            foreach ($prepareProductDiscount as $tax => $value)
                            {

                                $discountValue = $total['value'] + $shipping['value'];
                                $percent       = $value / $productTotal;

                                $discountIncl = $percent * ($discountValue);

                                $discountExcl = $discountIncl / (1 + $tax / 100);
                                $discountToArticle = $this->currency->format($discountIncl, $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                                //$discountToArticle = $this->currency->convert($discountIncl,$this->config->get('config_currency'),$this->session->data['currency']);
                                if($discountToArticle != 0) {
                                    $values['Articles'][] = array(
                                        'quantity' => 1,
                                        'artnr' => '',
                                        'title' => $total['title'] .' '.$coupon_info['name'].' ' . $tax . $this->language->get('% tax'),
                                        'aprice' => $discountToArticle,
                                        'taxrate' => $tax,
                                        'discount' => 0.0,
                                        'withouttax' => $discountToArticle

                                    );
                                    $orderTotal += $discountToArticle;
                                    $taxTotal += $discountToArticle * ($tax/100);
                                }

                            }
                        }
                        $freeshipTotal = $this->currency->format(-$shipping['value'] * 100, $order_info['currency_code'], $order_info['currency_value'], false);
                        //$freeshipTotal = $this->currency->convert(-$shipping['value'] * 100,$this->config->get('config_currency'),$this->session->data['currency']);

                        $values['Articles'][] = array(
                            'quantity'   => 1,
                            'artnr'    => '',
                            'title'    => $total['title'].' Free Shipping',
                            'aprice'    => $freeshipTotal,
                            'taxrate'      => $shippingtax * 100,
                            'discount' => 0.0,
                            'withouttax'    => $freeshipTotal
                        );
                        $orderTotal += $freeshipTotal;
                        $taxTotal += $freeshipTotal * $shippingtax;

                    } else if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F' || $coupon_info['type'] == 'FP') && $coupon_info['shipping'] == 0){


                        foreach ($prepareProductDiscount as $tax => $value)
                        {

                            $percent      = $value / $productTotal;
                            $discount     = $percent * ($total['value']);
                            $discountToArticle = $this->currency->format($discount, $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                            //$discountToArticle = $this->currency->convert($discount,$this->config->get('config_currency'),$this->session->data['currency']);

                            $values['Articles'][] = array(
                                'quantity'   => 1,
                                'artnr'    => '',
                                'title'    => $total['title'].' '.$coupon_info['name'].' ' .$tax.$this->language->get('tax_discount'),
                                'aprice'    => (int)$discountToArticle,
                                'taxrate'      => $tax,
                                'discount' => 0.0,
                                'withouttax'    => $discountToArticle
                            );
                            $orderTotal += $discountToArticle;
                            $taxTotal += $discountToArticle * ($tax/100);
                        }
                    }
                }
            }

        }

        if(isset($this->session->data['coupon'])){
            $coupon = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'coupon' AND order_id = ".$this->session->data['order_id']);
            $coupon_total = $coupon->row;
            if(version_compare(VERSION,'2.1.0','>=')){
                if (version_compare(VERSION,'2.3.0','>=')) {
                    $this->load->model('extension/total/coupon');
                    $coupon_info = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);
                } else {
                    $this->load->model('total/coupon');
                    $coupon_info = $this->model_total_coupon->getCoupon($this->session->data['coupon']);
                }
            } else {
                $this->load->model('checkout/coupon');
                $coupon_info = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
            }
            if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F') && $coupon_info['shipping'] == 1)
            {
                $shipping = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'shipping' AND order_id = ".$this->session->data['order_id']);
                $shipping = $shipping->row;
                $taxes = array();
                $total = 0;
                $total_data = array();
                $shippingtax = 0;
                if ($this->config->get($shipping['code'].'_status'))
                {
                    if (version_compare(VERSION,'2.3.0','>=')) {
                        $this->load->model('extension/total/'.$shipping['code']);
                    } else {
                        $this->load->model('total/'.$shipping['code']);
                    }

                    if(version_compare(VERSION,'2.2','>=')){
                        $totalArr = array('total_data' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);
                        if (version_compare(VERSION,'2.3.0','>=')) {
                            $this->{'model_extension_total_' . $result['code']}->getTotal($totalArr);
                        } else {
                            $this->{'model_total_' . $result['code']}->getTotal($totalArr);
                        }
                    }
                    else
                        $this->{'model_total_'.$shipping['code']}->getTotal($total_data, $total, $taxes);

                    if (isset($totalArr) AND is_array($totalArr))
                        extract($totalArr);
                    foreach ($taxes as $key => $value)
                    {
                        $shippingtax += $value;
                    }
                    $shippingtax = $shippingtax / $shipping['value'];

                }

                // If code above return 0, use shipping taxrate from $values
                if ($shippingtax < 1 AND isset($values['Cart']['Shipping']['taxrate'])) {
                    $shippingtax = $values['Cart']['Shipping']['taxrate'];
                    if ($shippingtax > 0) {
                        $shippingtax = $shippingtax / 100;
                    }
                }

                if($coupon_total['value'] < $shipping['value'])
                {
                    /* Free shipping have additional discount */
                    foreach ($prepareProductDiscount as $tax => $value)
                    {

                        $discountValue = $coupon_total['value'] + $shipping['value'];
                        $percent       = $value / $productTotal;

                        $discountIncl = $percent * ($discountValue);

                        $discountExcl = $discountIncl / (1 + $tax / 100);
                        $discountToArticle = $this->currency->format($discountIncl, $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                        //$discountToArticle = $this->currency->convert($discountIncl,$this->config->get('config_currency'),$this->session->data['currency']);
                        if($discountToArticle != 0) {
                            $discountToArticle = round($discountToArticle);
                            $values['Articles'][] = array(
                                'quantity' => 1,
                                'artnr' => '',
                                'title' => $coupon_total['title'] .' '.$coupon_info['name'].' ' . $tax . $this->language->get('tax_discount'),
                                'aprice' => (0 -abs($discountToArticle)),
                                'taxrate' => $tax,
                                'discount' => 0.0,
                                'withouttax' => (0 -abs($discountToArticle))

                            );
                            $orderTotal += $discountToArticle;
                            $taxTotal += $discountToArticle * ($tax / 100);
                        }

                    }
                }
                $freeshipTotal =  $this->currency->format(-$shipping['value'] * 100, $order_info['currency_code'], $order_info['currency_value'], false);
                //$freeshipTotal = $this->currency->convert(-$shipping['value'] * 100,$this->config->get('config_currency'),$this->session->data['currency']);

                $freeshipTotal = round($freeshipTotal);
                $values['Articles'][] = array(
                    'quantity'   => 1,
                    'artnr'    => '',
                    'title'    => $coupon_total['title'].' Free Shipping',
                    'aprice'    => (0 -abs($freeshipTotal)),
                    'taxrate'      => $shippingtax * 100,
                    'discount' => 0,
                    'withouttax'    => (0 -abs($freeshipTotal))

                );
                $orderTotal += $freeshipTotal;
                $taxTotal += $freeshipTotal * $shippingtax;

            } else if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F') && $coupon_info['shipping'] == 0){


                foreach ($prepareProductDiscount as $tax => $value)
                {

                    $percent      = $value / $productTotal;
                    $discount     = $percent * ($coupon_total['value']);
                    $discountToArticle = $this->currency->format($discount, $order_info['currency_code'],$order_info['currency_value'], false) * 100;
                    //$discountToArticle = $this->currency->convert($discount,$this->config->get('config_currency'),$this->session->data['currency']);

                    $discountToArticle = round($discountToArticle);
                    $values['Articles'][] = array(
                        'quantity'   => 1,
                        'artnr'    => '',
                        'title'    => $coupon_total['title'].' '.$coupon_info['name'].' ' .$tax.$this->language->get('tax_discount'),
                        'aprice'    => (0 - abs($discountToArticle)),
                        'taxrate'      => $tax,
                        'discount' => 0.0,
                        'withouttax'    => (0 - abs($discountToArticle))

                    );
                    $orderTotal += $discountToArticle;
                    $taxTotal += $discountToArticle * ($tax/100);

                }
            }

        }  // End discount isset
        $total = $this->currency->format($order_info['total'],$order_info['currency_code'],$order_info['currency_value'],false);
        $round = round($total*100) - round($orderTotal + $taxTotal);
        if(abs($myocRounding) > abs($round)){
            $round = $myocRounding;
        }
        $values['Cart']['Total'] = array(
            'withouttax' => round($orderTotal),
            'tax' => round($taxTotal),
            'rounding' => round($round),
            'withtax' => round($orderTotal + $taxTotal + $round)
        );



        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_code` = 'billmate_bankpay',  `payment_method` = '" . $this->db->escape(strip_tags($this->language->get('text_title'))) . "' WHERE `order_id` = " . (int)$this->session->data['order_id']);

        $result1 = $k->addPayment($values);
        if(isset($result1['code'])){
            $response['success'] = false;
            $response['message'] = $result1['message'];
        } else {
            $this->session->data['order_created'] = $result1['orderid'];
            $this->session->data['bankorder_api_called'] = false;
            $response['success'] = true;
            $response['url'] = $result1['url'];
        }
        $this->response->setOutput(my_json_encode($response));
	}
}
?>
