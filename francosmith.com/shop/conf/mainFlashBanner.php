<? 

header ("Cache-Control: no-cache, must-revalidate"); 
header ("Pragma: no-cache");

include "../lib/library.php";
include "../conf/config.php";

$loccd = $_GET[idx];
$url = "/".$cfg[rootDir]."/data/skin/".$cfg[tplSkin]."/img/banner/";

$query = "
select * from
	".GD_BANNER."
where
	tplSkin = '$cfg[tplSkin]'
	and loccd = '$loccd'
order by sort
";

$res = $db->query($query);

?>

//�̵����ǵ�: 1~5 /�Ҽ��� �Ұ���
&goods_speed=2&

//����ũ��: 1~5 /�Ҽ��� �Ұ���
&goods_quake=1&

//�̵��ð�����: �Ҽ��� ����
&move_time=3&

<? 
$idx = 0;
while ($data=$db->fetch($res)){ $idx++;
	if ($data[linkaddr]=="nolink") $data[linkaddr] = "";
?>
&imagepath_<?=$idx?>=<?=$url.$data[img]?>&<?="\n"?>
&excURL_<?=$idx?>=<?=urlencode($data[linkaddr])?>&<?="\n"?>
<? } ?>