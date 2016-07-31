<?
/**
 * ��Ƽ �˾� ��� ������
 * @author cjb3333 , artherot @ godosoft development team.
 */

include_once '../_header.php';

// ��Ƽ �˾� Class
$multipopup	= Core::loader('MultiPopup');
$data		= $multipopup->getPopupData($_GET['code']);
$popupData	= gd_json_decode(stripslashes($data['value']));

// �̹��� ���� �� ����
$displaySet	= explode('_',$popupData['displaySet']);
$row		= $displaySet[0];	// ���� ����
$col		= $displaySet[1];	// ���� ����
$imgTotNum	= $row * $col;		// �̹��� �� ����

$dir		= dirname(__FILE__).'/../data/multipopup/';	// �˾� �̹��� �ý��� ���
$path		= $cfg['rootDir'].'/data/multipopup/';		// �˾� �̹��� ���
$imgWidth	= 0;
$imgHeight	= 0;
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<script src="<?php echo $cfg['rootDir'];?>/lib/js/jquery-1.10.2.min.js"></script>
<script src="<?php echo $cfg['rootDir'];?>/lib/js/jquery.banner.js"></script>
<script src="<?php echo $cfg['rootDir'];?>/data/skin/<?php echo $cfg['tplSkin'];?>/common.js"></script>
<link rel="styleSheet" href="<?php echo $cfg['rootDir'];?>/data/skin/<?php echo $cfg['tplSkin'];?>/style.css" />
<style>
img	{border:none; vertical-align:top;}
</style>
</head>
<body bgcolor="white">
<div style="padding:<?php echo $popupData['outlinePadding'];?>px; text-align:center;">

<table id="popupImgTable" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td align="center" valign="center" width="<?php echo $popupData['mainImgSizew'];?>" height="<?php echo $popupData['mainImgSizeh'];?>">
		<div class="clsBannerScreen">
<?php
	$imgCount	= 1;

	// ���� �̹��� ���
	foreach ($popupData['mainBannerImg'] as $bannerimg)
	{
		// �̹��� ������
		$imgWidth	= $popupData['mainImgSizew'];
		$imgHeight	= $popupData['mainImgSizeh'];

		// �̹��� �ױ� ����
		$aTagSting	= '<div>';
		$imgTag		= '<img src="'.$multipopup->getImgSrc($bannerimg,$path).'" alert="�˾��̹���_'.$imgCount.'" />';

		// ��ũ�� �ִ� ���
		if (empty($popupData['linkUrl'][$imgCount]) === false) {
			$aTagSting	.= '<a '.$multipopup->getLink($popupData['linkTarget'][$imgCount],$popupData['linkUrl'][$imgCount]).'>'.$imgTag.'</a>';
		}
		// ��ũ�� ���°��
		else {
			$aTagSting	.= $imgTag;
		}
		$aTagSting	.= '</div>'.chr(10);

		echo $aTagSting;

		$imgCount++;
	}
?>
		</div>
	</td>
</tr>
<tr><td height="<?php echo $popupData['outlinePadding'];?>"></td></tr>
<tr>
	<td class="clsBannerButton">
		<table cellpadding="0" cellspacing="0" border="0">
<?php
	$imgCount	= 1;

	for ($i=1; $i <= $col; $i++)
	{
		echo "<tr>";

		for ($j=1; $j <= $row; $j++)
		{
			// ���� �̹����� ���� ó�� (�糡�� ���� ���� ����, ��� �̹������� ��� ���� ó��)
			if ($j == 1){
				$alignStr	= 'left';
			} else if($j == $row) {
				$alignStr	= 'right';
			} else {
				$alignStr	= 'center';
			}

			// td �� ũ��
			$tdWidth	= ceil($imgWidth/$row);

			// �̹��� �ױ� ����
			$imgTag		= '<img src="'.$multipopup->getImgSrc($popupData['mouseOutImg'][$imgCount],$path).'"';
			if (empty($popupData['mouseOnImg'][$imgCount]) === false) {
				$imgTag		.= ' oversrc="'.$multipopup->getImgSrc($popupData['mouseOnImg'][$imgCount],$path).'" outsrc="'.$multipopup->getImgSrc($popupData['mouseOutImg'][$imgCount],$path).'"';
			}
			$imgTag		.= ' alert="�����̹���_'.$imgCount.'" />';

			// ��ũ�� �ִ� ���
			if (empty($popupData['linkUrl'][$imgCount]) === false) {
				$aTagSting	= '<a '.$multipopup->getLink($popupData['linkTarget'][$imgCount],$popupData['linkUrl'][$imgCount]).'>'.$imgTag.'</a>';
			}
			// ��ũ�� ���°��
			else {
				$aTagSting	= $imgTag;
			}

			echo '<td width="'.$tdWidth.'" align="'.$alignStr.'">'.$aTagSting.'</td>'.chr(10);
			$imgCount++;
		}
		echo '</tr>';
	}
?>
			</table>
		</td>
	</tr>
	<?php if($popupData['popup_invisible'] == 'Y' ){ ?>
	<tr>
		<td style="background-color:#<?php echo $popupData['invisible_bgcolor'];?>;text-align:right;"><font style='font-size:12px;color:#<?php echo $popupData['invisible_fontcolor'];?>'><b>���� �Ϸ� ������ ����</b></font><input type="checkbox" onClick="controlCookie( 'mlpCookie_<?php echo $_GET['code'];?>', this )"></td>
	</tr>
	<?php } ?>

</table>
</div>

<script>
$(function() {
	$("#popupImgTable").jQBanner({nWidth:<?php echo $imgWidth;?>,nHeight:<?php echo $imgHeight;?>,nCount:<?php echo $imgTotNum;?>,isActType:"<?php echo $popupData['isActType'];?>",nOrderNo:1,isStartAct:"N",isStartDelay:"Y",nDelay:<?php echo $popupData['nDelay'];?>,isBtnType:"img"});
});

/**
 * �˾������� ���� ��ũ �̵�
 * @param string url
 */
function goLink(url)
{
<?php if ($popupData['popup_type'] != ''){ ?>
	parent.document.location.href	= url;
<?php } else { ?>
	opener.location.href			= url;
	self.close();
<?php } ?>
}

/**
 * ���� �Ϸ� ������ ���� ��Ű ó��
 * @param string name
 * @param object elemnt
 */
<?php
	if ($popupData['popup_type'] != '') $parent = "parent.";
	else $parent = "";
?>
function controlCookie( name, elemnt ){

	if ( elemnt.checked ){

	    var today = new Date()
	    var expire_date = new Date(today.getTime() + 60*60*6*1000);

		setCookie( name=name, value='true', expires=expire_date, path='/' );

		if (<?php echo $parent;?>_ID(name) == null) setTimeout( "self.close()" );
		else setTimeout( "<?php echo $parent;?>_ID('" + name + "').style.display='none'" );
	}
	else clearCookie( name );

	return
}
</script>
</body>
</html>