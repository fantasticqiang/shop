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
		if ($this->session->data['payment_method']['code'] == 'qt') {
			$this->load->model('checkout/order');

//			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('qt_order_status_id'));
		}
	}

    /**
     * 工具方法
     */


    /**
     * 修改订单的状态
     */
	public function notify() {
	    //注意，不同系统的目录结构不同，需要稍作修改
        $myfile = fopen("d://testfile.txt", "w");
        $result=file_get_contents('php://input', 'r');
        $tmp = explode("|", $result);
        $resp_xml = base64_decode($tmp[0]);
        fwrite($myfile, $resp_xml);
        $resp_sign = $tmp[1];

        //修改订单装态
        $this->load->model('checkout/order');
        //获取订单号,具体看request参数,在做修改
        $orderXmlObj = simplexml_load_string($resp_xml);
        $orderXmlArr = json_decode(json_encode($orderXmlObj),true);
        $order_id = $orderXmlArr['@attributes']['merchantOrderId'];

        if(verity(MD5($resp_xml,true),$resp_sign)){//验签
            echo '<br/>响应结果<br/><textarea cols="120" rows="20">'.$resp_xml.'</textarea>';
            fwrite($myfile, '<br/>响应结果<br/><textarea cols="120" rows="20">'.$resp_xml.'</textarea>');

            //测试数据，执行ok，可以修改状态，可以添加历史订单
            //$this->model_checkout_order->addOrderHistory('18', $this->config->get('qt_order_status_id'));

            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('qt_order_status_id'));
            $this->response->redirect($this->url->link('checkout/success'));
        } else {
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('qt_failed_status_id'));
            $this->response->redirect($this->url->link('checkout/failure'));
            fwrite($myfile, 'fail');
        }
        fclose($myfile);
    }
}
