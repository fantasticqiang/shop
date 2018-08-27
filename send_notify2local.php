<?php
/**
 * Created by PhpStorm.
 * User: lq
 * Date: 2018/8/23
 * Time: 11:13
 */
include 'catalog/controller/checkout/qtutil/util.php';

$str=  '<?xml version="1.0" encoding="utf-8" standalone="no"?>
<message 
	application="NotifyOrder" 
	version="1.0.1"
	merchantId="1000001" 
	merchantOrderId="1000011651">
	<deductList>
		<item
			payOrderId="pxQoal02Q3rKTOI"
			payAmt="1000"
			payStatus="01"
			payDesc="付款成功" 
			payTime="20151225110046"/>
	</deductList>
	<refundList/>
</message>';


/*****生成请求内容**开始*****/
$strMD5 =  MD5($str,true);
$strsign =  sign($strMD5);
$base64_src=base64_encode($str);
$msg = $base64_src."|".$strsign;
echo '要发送的密文：'.$msg;

//要发送的数据
$curlPost = $msg;
//要发送的地址
$postUrl = 'http://localhost/shop/index.php?route=payment/qt/notify';

$ch = curl_init($postUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);//$data JSON类型字符串
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close ( $ch );