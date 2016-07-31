<?
include "../_header.php";
include "../lib/cart.class.php";
setcookie('gd_isDirect','',time() - 3600,'/');

require "../lib/load.class.php";

$cart = new Cart;
$cart->setAjaxMode(true);

$_POST = iconv_recursive('utf-8','euc-kr',$_POST);

if(!$cart->chkMaxCount()){		//��������üũ
	$code="chkMaxCount";
	$msg=$cart->msg['maxCount'];
}
else if($chkGoods=rtnOpenYn($_POST[goodsno])) {	//�������� üũ
	$code="chkOpenYn";
	$msg=$chkGoods[0]."��ǰ�� ���� �������� ��ǰ�� �ƴմϴ�";
}
else{
	if(is_array($_POST[goodsno])){	//���ڿ��� �Ѿ�� ��Ʈ��ǰ
		for($i=0;$i<sizeof($_POST[goodsno]);$i++){
			if($_POST[goodsno][$i]){
				$cart->addCart($_POST[goodsno][$i],array_notnull($_POST[opt][$i]),$_POST[addopt][$i],$_POST[addopt_inputable][$i],$_POST[ea][$i],$_POST[goodsCoupon][$i]);
			}
		}
	}else{
		if ($_POST[multi_ea]) {
			$_keys = array_keys($_POST[multi_ea]);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST[multi_opt][ $_keys[$i] ];
				$_ea = $_POST[multi_ea][ $_keys[$i] ];
				$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];
				$cart->addCart($_POST[goodsno],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST[goodsCoupon]);
			}
		}
		else {
			$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],$_POST[_addopt_inputable],$_POST[ea],$_POST[goodsCoupon]);
		}
	}
	$code="success";

	// ace ī���� ��ǰ�߰�
	$Acecounter = new Acecounter();
	if ($Acecounter->open_state()) {
		if ($_POST['multi_ea']) {
			$aceGoodsno = $aceEa = array();
			$_keys = array_keys($_POST['multi_ea']);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				array_push($aceGoodsno, $_POST['goodsno']);
				array_push($aceEa, $_POST['multi_ea'][ $_keys[$i] ]);
			}
		} else {
			$aceGoodsno = (array)$_POST['goodsno'];
			$aceEa = (array)$_POST['ea'];
		}

		if ($Acecounter->goods_cart_add($cart->item, $aceGoodsno, $aceEa) === true) {
			$aceScript = $Acecounter->scripts;
			$aceScript = strip_tags($Acecounter->scripts);
		}
	}
}

if($cart->getErrorMsg()){
	$code='chkMaxCount';
	$msg=$cart->getErrorMsg();
}

header('Content-Type: application/xml;charset=euc-kr');
echo "<?xml version=\"1.0\" encoding=\"euc-kr\" ?>";
echo "<result>";
echo "<code>".$code."</code>";
echo "<msg><![CDATA[".$msg."]]></msg>";
echo "<aceScript><![CDATA[".$aceScript."]]></aceScript>";
echo "</result>";
?>
