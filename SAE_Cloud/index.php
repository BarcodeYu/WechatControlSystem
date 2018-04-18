<?php
 
//������־
function echo_server_log($log){
        file_put_contents("log.txt", $log, FILE_APPEND);
}
 
//����TOKEN
define ( "TOKEN", "barcode" );
 
//��֤΢�Ź���ƽ̨ǩ��
function checkSignature() {
        $signature = $_GET ['signature'];
        $nonce = $_GET ['nonce'];
        $timestamp = $_GET ['timestamp'];
        $tmpArr = array (TOKEN,$timestamp,$nonce );
        sort ( $tmpArr );
         
        $tmpStr = implode ( $tmpArr );
        $tmpStr = sha1 ( $tmpStr );
        if ($tmpStr == $signature) {
                return true;
        }else{
                return false;
        }
}
if(false == checkSignature()) {
        exit(0);
}
 
//����ʱ��֤�ӿ�
$echostr = $_GET ['echostr'];
if($echostr) {
    header('content-type:text');
        echo $echostr;
        exit(0);
}
 
//��ȡPOST����
function getPostData() {
        $data = $GLOBALS['HTTP_RAW_POST_DATA'];
        return        $data;
}
$PostData = getPostData();
 
//���
if(!$PostData){
        echo_server_log("wrong input! PostData is NULL");
        echo "wrong input!";
        exit(0);
}
 
//װ��XML
$xmlObj = simplexml_load_string($PostData, 'SimpleXMLElement', LIBXML_NOCDATA);
 
//���
if(!$xmlObj) {
        echo_server_log("wrong input! xmlObj is NULL\n");
        echo "wrong input!";
        exit(0);
}
 
//׼��XML
$fromUserName = $xmlObj->FromUserName;
$toUserName = $xmlObj->ToUserName;
$msgType = $xmlObj->MsgType;
 
 
if($msgType == 'voice') {//�ж��Ƿ�Ϊ����
        $content = $xmlObj->Recognition;
}elseif($msgType == 'text'){
        $content = $xmlObj->Content;
}else{
        $retMsg = 'ֻ֧���ı���������Ϣ';
}
 
if (strstr($content, "�¶�")) {
        $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
        mysql_select_db("app_barcode", $con);//�޸����ݿ���
 
        $result = mysql_query("SELECT * FROM sensor");
        while($arr = mysql_fetch_array($result)){
          if ($arr['ID'] == 1) {
                  $tempr = $arr['data'];
          }
        }
        mysql_close($con);
 		
    if($tempr<=10)
    {
 	   $retMsg = "�¶Ȼ�ȡ�ɹ�"."\n"."���ķ��������Ϊ".$tempr."��"."\n"."�¶Ƚϵ���ע�Ᵽů~";
    }
    else if($tempr>=30)
    {
    	$retMsg = "�¶Ȼ�ȡ�ɹ�"."\n"."���ķ��������Ϊ".$tempr."��"."\n"."�¶Ƚϸߣ��յ���������~";
    }
   else if($tempr>10&$tempr<30)
    {
    	$retMsg = "�¶Ȼ�ȡ�ɹ�"."\n"."���ķ��������Ϊ".$tempr."��"."\n"."�¶��ʺϣ�~";
    }
		 
}

else if (strstr($content, "����")) {
        $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
 
 
        $dati = date("h:i:sa");
        mysql_select_db("app_barcode", $con);//�޸����ݿ���
 
        $sql ="UPDATE switch SET timestamp='$dati',state = '1'
        WHERE ID = '1'";//�޸Ŀ���״ֵ̬
 
        if(!mysql_query($sql,$con)){
            die('Error: ' . mysql_error());
        }else{
                mysql_close($con);
                $retMsg = "���Ƴɹ�";
        }
}
else if (strstr($content, "�ص�")) {
        $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
 
 
        $dati = date("h:i:sa");
        mysql_select_db("app_barcode", $con);//�޸����ݿ���
 
        $sql ="UPDATE switch SET timestamp='$dati',state = '0'
        WHERE ID = '1'";//�޸Ŀ���״ֵ̬
 
        if(!mysql_query($sql,$con)){
            die('Error: ' . mysql_error());
        }else{
                mysql_close($con);
                $retMsg = "�صƳɹ�";
        }        
}
else{
        $retMsg = "��ʱ��֧�ָ�����";
}
 
//װ��XML
$retTmp = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";
$resultStr = sprintf($retTmp, $fromUserName, $toUserName, time(), $retMsg);
 
//������΢�ŷ�����
echo $resultStr;
?>