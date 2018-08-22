<?php

include dirname(__DIR__).'\checkout\qtutil\util.php';

class ControllerPaymentQt extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/qtpay');

		return $this->load->view('payment/qt.tpl', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['qte'] == 'qt') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('qt_order_status_id'));
		}
	}

    /**
     * 工具方法
     */


    /**
     * 修改订单的状态
     */
	public function notify() {
        $myfile = fopen("d://testfile.txt", "w");
        $result=file_get_contents('php://input', 'r');
        $tmp = explode("|", $result);
        $resp_xml = base64_decode($tmp[0]);
        fwrite($myfile, $resp_xml);
        $resp_sign = $tmp[1];
        if(verity(MD5($resp_xml,true),$resp_sign)){//验签
            echo '<br/>响应结果<br/><textarea cols="120" rows="20">'.$resp_xml.'</textarea>';
            fwrite($myfile, '<br/>响应结果<br/><textarea cols="120" rows="20">'.$resp_xml.'</textarea>');
        } else {
            fwrite($myfile, 'fail');
        }
        fclose($myfile);
    }
}
