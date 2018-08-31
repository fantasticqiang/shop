<?php

/**
 * gateway 下单地址，正式地址，转正式的时候需要切换
 */
global $gateway_url;
#$gateway_url="https://www.qtongpay.com/pay/pay.htm";
$gateway_url="http://123.56.119.177:8081/pay/pay.htm";

/**
 * 证书密码
 */
global $merchant_cert_password;
$merchant_cert_password="11111111";


//商户号
global $merchant_no;
$merchant_no="1000000";

//异步通知地址
global $notifyUrl;
$notifyUrl="http://localhost/shop/index.php?route=payment/qt/notify";

/**
 * 签名  生成签名串  基于sha1withRSA
 * @param string $data 签名前的字符串
 * @return string 签名串
 */
function sign($data) {
	$certs = array();
	openssl_pkcs12_read(file_get_contents(__DIR__."/merchant_cert.pfx"),
		$certs,$GLOBALS['merchant_cert_password']); //其中password为你的证书密码
	if(!$certs) return ;
	$signature = '';  
	openssl_sign($data, $signature, $certs['pkey']);
	return base64_encode($signature);
}
/**
 * 验证签名： 
 * @param data：原文 
 * @param signature：签名 
 * @return bool 返回：签名结果，true为验签成功，false为验签失败 
 */  
function verify($data,$signature)
{  
	$pubKey = file_get_contents(__DIR__."/server_cert.cer");
	$res = openssl_get_publickey($pubKey);  
	$result = (bool)openssl_verify($data, base64_decode($signature), $res);  
	openssl_free_key($res);
	return $result;  
}
