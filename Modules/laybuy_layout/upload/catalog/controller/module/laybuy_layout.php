<?php
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015-2016 Arastta Association. All rights reserved. (arastta.org)
 * @credits        See CREDITS.txt for credits and other copyright notices.
 * @license        GNU General Public License version 3; see LICENSE.txt
 */

class ControllerModuleLaybuyLayout extends Controller
{
    public function index()
    {
        $this->load->model('module/laybuy_layout');

        $status = $this->config->get('laybuy_layout_status');

        if (($status && $this->config->get('laybuy_status')) && $this->customer->isLogged() && isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];

            if ($this->model_module_laybuy_layout->isLayBuyOrder($order_id)) {
                $this->load->language('module/laybuy_layout');

                $data = $this->language->all();

                $transaction_info = $this->model_module_laybuy_layout->getTransactionByOrderId($order_id);

                $data['transaction'] = array(
                    'laybuy_ref_no'      => $transaction_info['laybuy_ref_no'],
                    'paypal_profile_id'  => $transaction_info['paypal_profile_id'],
                    'status'             => $this->model_module_laybuy_layout->getStatusLabel($transaction_info['status']),
                    'amount'             => $this->currency->format($transaction_info['amount'], $transaction_info['currency']),
                    'downpayment'        => $transaction_info['downpayment'],
                    'months'             => $transaction_info['months'],
                    'downpayment_amount' => $this->currency->format($transaction_info['downpayment_amount'], $transaction_info['currency']),
                    'payment_amounts'    => $this->currency->format($transaction_info['payment_amounts'], $transaction_info['currency']),
                    'first_payment_due'  => date($this->language->get('date_format_short'), strtotime($transaction_info['first_payment_due'])),
                    'last_payment_due'   => date($this->language->get('date_format_short'), strtotime($transaction_info['last_payment_due'])),
                    'report'             => json_decode($transaction_info['report'], true)
                );

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/laybuy_layout.tpl')) {
                    return $this->load->view($this->config->get('config_template') . '/template/module/laybuy_layout.tpl', $data);
                } else {
                    return $this->load->view('default/template/module/laybuy_layout.tpl', $data);
                }
            }
        }

        return null;
    }
}
