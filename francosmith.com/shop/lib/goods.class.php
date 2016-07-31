<?php
class Goods
{
	var $db;
	function Goods($db=0){
		$this->db = Core::loader('db');
	}
	function get_opt_goodsno($optsno){
		list($goodsno) = $this->db->fetch("select a.goodsno from ".GD_GOODS." a,".GD_GOODS_OPTION." b where a.goodsno=b.goodsno and b.sno='$optsno' and go_is_deleted <> '1' and go_is_display = '1' limit 1");
		return $goodsno;
	}
	function update_date($goodsno){
		$this->db->query("update ".GD_GOODS." set updatedt=now() where goodsno='$goodsno'");
	}

	/* ��ǰ�� ����� ���������� �ҷ��� �����ÿ� ���������� ������*/
	function get_goods_price($goodsno){
		$query = "SELECT price FROM gd_goods_option WHERE goodsno='$goodsno' and link and go_is_deleted <> '1' and go_is_display = '1'";
		list($price)=$this->db->fetch($query);
		return $price;
	}

	/* ��ǰ �Ϸù�ȣ�� ��ǰ������ �����´�. */
	function get_goods($goodsno){
		return $this->db->_select("select * from gd_goods where goodsno='$goodsno'");
	}

	// ��ǰ�� ������ ī�װ����� �����´�
	function get_goods_category($goodsno)
	{
		$db = Core::loader('db');
		if(is_array($goodsno)){
			$query = "select category,char_length(category) clen from gd_goods_link where goodsno in (".implode(',',$goodsno).")";
		}else{
			$query = "select category,char_length(category) clen from gd_goods_link where goodsno='$goodsno'";
		}
		$result = $db->_select($query);
		foreach($result as $tmp){
			for($i=3;$i<=$tmp['clen'];$i+=3){
				$categorys[] = substr($tmp['category'],0,$i);
			}
		}
		return $categorys;
	}
}
?>
