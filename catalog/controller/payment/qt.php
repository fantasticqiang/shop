<?php

include dirname(__DIR__)."/checkout/qtutil/util.php";

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
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 2);
		}
	}

    /**
     * 修改订单的状态
     */
	public function notify() {
	    //注意，不同系统的目录结构不同，需要稍作修改
        $result=file_get_contents('php://input', 'r');
        $this->log->write("接收到钱通发来异步通知：".$result);
        $tmp = explode("|", $result);
        $resp_xml = base64_decode($tmp[0]);
        $this->log->write("通知原文：".$resp_xml);
        $resp_sign = $tmp[1];
        $this->log->write("签名：".$resp_sign);

        //获取订单号,具体看request参数,在做修改
        $orderXmlObj = simplexml_load_string($resp_xml);
        $orderXmlArr = json_decode(json_encode($orderXmlObj),true);
        $order_id = $orderXmlArr['@attributes']['merchantOrderId'];
        $this->log->write("商户订单号：".$order_id);

        if(verify(MD5($resp_xml,true),$resp_sign)){//验签
            $this->log->write("响应结果：".$resp_xml);
            //修改订单装态
            $this->load->model('checkout/order');
            //测试数据，执行ok，可以修改状态，可以添加历史订单

            $this->model_checkout_order->addOrderHistory($order_id, 5,'',true);
            $this->log->write("修改订单状态：addOrderHistory($order_id, 5,'',true)");
            $this->response->redirect($this->url->link('checkout/success'));
        } else {
            $this->log->write("验签错误：$order_id");
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($order_id, 2,'',true);
            $this->response->redirect($this->url->link('checkout/failure'));
        }
    }
}
