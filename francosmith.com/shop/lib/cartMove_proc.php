<?
header('Content-Type: text/html; charset=euc-kr');

include "../lib/library.php";
include "../lib/json.class.php";
include "../lib/cart.class.php";

### �����Ҵ�
$orderitem_mode = "cart";
$mode = ($_GET[mode]) ? $_GET[mode] : $_GET[mode];


if( $_GET['mode'] != "Cart_view" ){

	if( $_GET[goodsno] ){

		//�ɼ�����
		$Query = "
			select
				a.img_s,
				a.opttype,
				a.runout,
				a.usestock,
				b.opt1,
				b.opt2,
				b.stock,
				a.strprice,
				a.addoptnm
			from
				".GD_GOODS." a , ".GD_GOODS_OPTION." b
			where
				a.goodsno = b.goodsno and go_is_deleted <> '1' and go_is_display = '1' and
				a.open = '1' and
				a.goodsno = '$_GET[goodsno]'
			order by b.sno asc
		";
		$row = $db->fetch($Query);
		$opt = array();

		//�ɼ��� �������!!
		if( $row['opt1'] || $row['opt2'] ){
			if( $row['opttype'] == "double" ){
				$opt['0'] = $row['opt1'];
				$opt['1'] = $row['opt2'];
			}else $opt['0'] = $row['opt1'].'|'.$row['opt2'];
		}

		//addopt �ʼ�üũ
		unset($addidx,$tmp);
		if($row['addoptnm']){
			if(preg_match('/\^o/',$row['addoptnm'])){
				$tmp = explode('|',$row['addoptnm']);
				foreach($tmp as $k => $v) if(preg_match('/\^o/',$v) && $addidx == null) $addidx = $k;
			}
		}

		if(isset($addidx)){
			$query = "select sno,opt,addprice from ".GD_GOODS_ADD." where goodsno='".$_GET['goodsno']."' and step='".$addidx."' order by sno limit 1";
			$add_tmp = $db->fetch($query);
			$add_tmp['optnm'] = str_replace('^o','',$tmp[$addidx]);
			$addopt[] = $add_tmp['sno']."^".$add_tmp['optnm']."^".$add_tmp['opt']."^".$add_tmp['addprice'];
		}
	}

		$cart = new Cart;

		//�ش� ��ǰ�� īƮ�� �ִ��� üũ!! �������!! ������ �߰�
		if( $_GET['mode'] == "addItem" ){
			$cart_Cnt = count($cart->item);
			$_inCartyn = 'n';
			$ea = '';
			for( $c=0 ; $c < $cart_Cnt; $c++ ){
				if( $cart->item[$c]['goodsno'] == $_GET['goodsno'] ){
					//�ɼ��� �������!!
					if($cart->item[$c]['opt']){
						//�ɼ��� �����ϰ��
						if(is_array($cart->item[$c]['opt'])){
							if( $cart->item[$c]['opt']['0'] == $row['opt1'] && $cart->item[$c]['opt']['1'] == $row['opt2'] ){
								$_inCartyn = "y";
								$ea[$c] = $cart->item[$c]['ea'] + 1;
							}
						}else{ //�ɼ��� �̱��ϰ��
							if( $cart->item[$c]['opt'] == $opt ){
								$_inCartyn = "y";
								$ea[$c] = $cart->item[$c]['ea'] + 1;
							}
						}
					}else{ //�ɼ��� �������!!
						$_inCartyn = "y";
						$ea[$c] = $cart->item[$c]['ea'] + 1;
					}
				}else{
					$ea[$c] = $cart->item[$c]['ea'];
				}
			}

			if( $_inCartyn == "n" ) $mode = "addItem";
			else $mode = "modItem";
		}

		//����
		if( $mode == "addItem" )	$ea = '1';

		if( $row['stock'] < $ea && $row['usestock'] != ''){
			$mode = 'Not_ea1';
		}else if( $row['runout'] == '1'){
			$mode = 'Not_ea2';
		}else if( $row['strprice'] != ''){
			$mode = 'Not_ea3';
		}else{
			//��ũ�� īƮ ���,����,������
			switch ($mode){
				case "addItem":
					$cart->addCart($_GET[goodsno],$opt,array_notnull($addopt),array(), $ea,$goodsCoupon);
					break;
				case "modItem": $cart->modCart($ea); break;
				case "delItem": $cart->delCart($_GET[idx]); break;
				case "empty": $cart->emptyCart(); break;
			}
			$cart->calcu();
		}

		$_data = array_merge( array( 'Remode'=>$mode ), (array)$cart );
		$_info = array_merge( array( 'ReMGT'=>$Move_GoodsType ), $_data );
		unset($_info['db']);	// db ��ü�� �����ݴϴ�.
		$json = new Services_JSON();
		$output = $json->encode($_info);
		echo $output;
}

if( $_GET['mode'] == "Cart_view" ){ //ī�尪�� �θ����!!
	$cart = new Cart;
	$cart->calcu();

	$_info = array_merge( array( 'Remode'=>$mode ), (array)$cart );
	unset($_info['db']);	// db ��ü�� �����ݴϴ�.
	$json = new Services_JSON();
	$output = $json->encode($_info);
	echo $output;
}
?>