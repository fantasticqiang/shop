<?php
class ControllerPaymentQt extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/qtpay');

		return $this->load->view('payment/qt', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['qt'] == 'qt') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('qt_order_status_id'));
		}
	}
}
