<?php
header("Content-type:text/html; charset=GB2312");
include '../xy_config.php'; 

$billno = $_GET['billno'];
$amount = $_GET['amount'];
$mydate = $_GET['date'];
$succ = $_GET['succ'];
$msg = $_GET['msg'];
$attach = $_GET['attach'];
$ipsbillno = $_GET['ipsbillno'];
$retEncodeType = $_GET['retencodetype'];
$currency_type = $_GET['Currency_type'];
$signature = $_GET['signature'];

$content = 'billno'.$billno.'currencytype'.$currency_type.'amount'.$amount.'date'.$mydate.'succ'.$succ.'ipsbillno'.$ipsbillno.'retencodetype'.$retEncodeType;
$cert = '27157522057972279532409806796985919088172972270434925771835182616295228145053762526196924715298935508411688961817266641027050284';
$signature_1ocal = md5($content . $cert);

if ($signature_1ocal == $signature){
$conn = mysql_connect($dbhost,$conf['db']['user'],$conf['db']['password']);
if (!$conn)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db($dbname,$conn);
mysql_query("SET NAMES UTF8");
@session_start();//����session�Ự

$chaxun = mysql_query("SELECT state FROM xy_order WHERE order_number = '".$billno."'");
$chaxun2 = mysql_query("select actionIP from xy_member_recharge where rechargeid= '".$billno."'");
$actionIP = mysql_result($chaxun2,0);
$chaxun3 = mysql_query("select id from xy_member_recharge where rechargeid= '".$billno."'");
$id = mysql_result($chaxun3,0);
$chaxun4 = mysql_query("select uid from xy_member_recharge where rechargeid= '".$billno."'");
$uid = mysql_result($chaxun4,0);
$chaxun5 = mysql_query("select coin from xy_members where uid= '".$uid."'");
$coin = mysql_result($chaxun5,0);
$chaxun6 = mysql_query("select value from xy_params where name='czzs'");
$czzs = mysql_result($chaxun6,0);
if($czzs){
	$amount=$amount*(1+number_format($czzs/100,2,'.',''));
}
$inserts = "insert into xy_coin_log (uid,type,playedId,coin,userCoin,fcoin,liqType,actionUID,actionTime,actionIP,info,extfield0,extfield1) values ('".$uid."',0,0,'".$amount."','".$coin."'+'".$amount."',0,1,0,UNIX_TIMESTAMP(),'".$actionIP."','��Ѷ��ֵ�Զ�����','".$id."','".$uid."')";
$update1 = "UPDATE xy_order SET state = 2 WHERE order_number = '".$billno."'";
$update2 = "UPDATE xy_members SET coin = coin+'".$amount."' WHERE username = '".$attach."'";
$update3 = "update xy_member_recharge set state=2,rechargeTime=UNIX_TIMESTAMP(),rechargeAmount='".$amount."',coin='".$coin."',info='��Ѷ��ֵ�Զ�����' where rechargeid='".$billno."'";

$jiancha = mysql_result($chaxun,0);

if($jiancha==0){
	if ($succ == 'Y'){
                if(mysql_query($update1,$conn)){
                mysql_query($update2,$conn);
                mysql_query($update3,$conn);
                mysql_query($inserts,$conn);
                echo "���ѳɹ���ֵ�������µ�½ƽ̨����鿴,лл!";
				}else{
					echo "����Ͷ�ݳ���";
				}
	}else{
	   echo "����ʧ��";
    }
}else{
    echo "���ѳ�ֵ�����𷴸�ˢ��,лл!";
}
}else{
	echo 'ǩ������ȷ��';
	exit;
}
mysql_close($conn);
?>
