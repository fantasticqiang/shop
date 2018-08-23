<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>online pay</title>
</head>

<body>
<?php
include 'qtutil/util.php';
global $gateway_url2;
$gateway_url2 = $gateway_url;

class ControllerCheckoutQtpay extends Controller {
	
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			// Add to activity log
			$this->load->model('account/activity');

			if ($this->customer->isLogged()) {
				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name'        => $this->customer->getFirstName(),
					'order_id'    => $this->session->data['order_id']
				);

				$this->model_account_activity->addActivity('order_account', $activity_data);
			} else {
				$activity_data = array(
					'name'     => $this->session->data['guest']['firstname'],
					'order_id' => $this->session->data['order_id']
				);

				$this->model_account_activity->addActivity('order_guest', $activity_data);
			}
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
			// 获取订单数据
			$this->load->model('checkout/order');
			//$this->load->model('payment/alipay_direct');

			$order_id = $activity_data['order_id'];
			$order_info = $this->model_checkout_order->getOrder($order_id);
			//$order_product_info = $this->model_payment_alipay_direct->getOrderProduct($order_id);
			$merchantId = "1000000";//商户号
			$merchantOrderId = $activity_data['order_id'];//订单号
			$price = intval($order_info['total']*100);//金额
			$notifyUrl = "http://39.104.104.16:8080/ff.jsp";
            $payCallBack = "http://192.168.1.90/index.php?route=payment/qt/notify"; //支付完成后回调本网站处理订单
			//必填r，订单时间
            //测试的时候把这个替换：merchantFrontEndUrl="https://127.0.0.1:8443/pay-interface/order_request.jsp"
            //成：merchantFrontEndUrl="'.$payCallBack.'"
            //为了跳过外网的支付流程，生产测试时，在替换过了来，就可以
			$orderTime = date("YmdHis");
			$str=  '<?xml version="1.0" encoding="utf-8" standalone="no"?>
				<message accountType="0" application="SubmitOrder" bankId="" bizType="" credentialNo="" credentialType="" guaranteeAmt="0" 
				merchantFrontEndUrl="https://127.0.0.1:8443/pay-interface/order_request.jsp"
				merchantId="'.$merchantId.'" merchantOrderAmt="' .$price. '" merchantOrderDesc="环球地理" merchantOrderId="'.$merchantOrderId.'" 
				merchantPayNotifyUrl="'.$notifyUrl.'" msgExt="" orderTime="'.$orderTime.'" payMode="0" 
				payerId="" rptType="1" salerId="" userMobileNo="13333333333" userName="" userType="1" version="1.0.1"/>';
			
			
			/*****生成请求内容**开始*****/
			$strMD5 =  MD5($str,true);	
			$strsign =  sign($strMD5);
			$base64_src=base64_encode($str);
			$msg = $base64_src."|".$strsign;
			
			/*****生成请求内容**结束*****/
			
			$def_url =  '<div style="text-align:center">';
			$def_url .= '<body onLoad="document.ipspay.submit();">网银订单确认';
			$def_url .= '<form name="ipspay" action="'.$GLOBALS['gateway_url2'].'" method="post">';
			$def_url .=	'<input name="msg" type="hidden" value="'.$msg.'" /><input type="submit" value="提交"/>';
			$def_url .=	'</form></div>';
			echo $def_url;

		}
	}
}
?>

<!--
<form action="<?php echo $gateway_url ?>" method="post" id="form_qt">
<div style="text-align:center;">
	<table style="margin:0 auto;">
		<tr>
			<td>网银订单确认</td>	
		</tr>
		<tr>
			<td>
				<input type="hidden" name="msg" value="<?php echo $msg_out ?>"/>
			</td>
			<td>
				<input type="submit" value="确定"/>
			</td>
		</tr>
	</table>
</div>
</form>
<script>
	//document.getElementById('form_qt').submit();
</script>
</body>
</html>
-->