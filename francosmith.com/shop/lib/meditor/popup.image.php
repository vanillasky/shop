<?
include dirname(__FILE__) . "/../library.php";

list( $disk_errno, $disk_msg ) = disk();

if ( $disk_errno == '001' ) $disk_img = "http://www.godo.co.kr/userinterface/img/disk_guide_add_text.gif";
else if ( $disk_errno == '002' ) $disk_img = "http://www.godo.co.kr/userinterface/img/disk_guide_date_text.gif";
?>
<title>�̹��� ����ϱ�</title>
<style>
body {background:buttonface}
body,td,input {font:9pt ����}
</style>
<script language="javascript"><!--
function webftp(){
	var hrefStr = '../../admin/design/popup.webftp.php';
	var win = window.open( hrefStr, 'webftp', "loaction=no, directories=no, Width=900, Height=800, left=50, top=50, scrollbars=1" );
	win.focus();
}

function imghost(){
	var hrefStr = 'http://image.godo.co.kr/login/imghost_login.php';
	var win = window.open( hrefStr, 'imghost', "loaction=no, directories=no, Width=980, Height=700, left=50, top=50, scrollbars=1" );
	win.focus();
}
--></script>

<form method=post target=ifrm action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="InsertImage">
<input type=hidden name=idx value="<?=$_GET[idx]?>">

<table width=100%>
<tr>
	<td style="font:bold 20px ����;padding:10 0 0 5; letter-spacing:-1">�̹��� ����ϱ�</td>
</tr>
<tr><td height=10></td></tr>
<tr>
	<td>
	<table width=100%><tr><td nowrap>- �̹��� ���ε��ϱ�</td><td width=100%><hr></td></tr></table>
	</td>
</tr>
<tr><td style="padding-left:14px"><font color="#555555">�̹��������� �뷮�� <?=ini_get('upload_max_filesize')?>B������ ����� �� �ֽ��ϴ�.</font></td></tr>
<tr>
	<td>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td style="padding:0 10 0 10"><input type=file name=mini_file style="width:100%"></td>
	</tr>
	</table>

	<? if ( !empty( $disk_errno ) ){ ?>
	<script>var call_file_disabled = true;</script>
	<div style="margin-top:10px;" align=center><img src="<?=$disk_img?>"></div>
	<? } ?>

	<div style="margin-top:20px; display:none;" id="imgurlSpace">
		<table width=100%><tr><td nowrap style="padding-bottom:10">- �Ǵ�, �̹��� �ּҳֱ�</td><td width=100%><hr></td></tr></table>

		<table width=100% cellpadding=0 cellspacing=0>
		<tr>
			<td style="padding:0 10 0 10"><input type="input" name="mini_url" value="http://" style="width:100%;"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5 10 0 0">
			<input type="button" value="�� WebFTP ����" style="width:120px" onclick="webftp()">
			<input type="button" value="�� �̹���ȣ���� ����" style="width:130px" onclick="imghost()">
			</td>
		</tr>
		</table>
	</div>

	</td>
</tr>
<tr>
	<td>
	<table width=100% style="margin-top:20px;"><tr><td nowrap>- �̹���������</td><td width=100%><hr></td></tr></table>
	</td>
</tr>
<tr><td style="padding-left:14"><font color=555555>���� ������ �״�� ���̰� �ʹٸ� �Է¾��ص� �˴ϴ�.<!--�̹����� ũ�⸦ �ٸ��� ���� ��쿡�� �Է��ϼ���.--></td></tr>
<tr>
	<td style="padding:0 10px">

	<table width=100%>
	<tr>
		<td>���� �ȼ� : <input type=text name=imgWidth size=10></td>
		<td>���� �ȼ� : <input type=text name=imgHeight size=10></td>
	</tr>
	</table>

	</td>
</tr>
<tr><td><hr></td></tr>
<tr>
	<td align=center style="padding:5px">

	<input type=submit value="Ȯ��" style="width:100px">
	<input type=button value="���" style="width:100px" onclick="window.close()">

	</td>
</tr>
</table>

</form>
<iframe name=ifrm style="display:none"></iframe>

<script language="javascript"><!--

if (opener.document.location.href.toString().match(/\/admin\//)){
	document.getElementById('imgurlSpace').style.display = 'block';
}
--></script>
<SCRIPT LANGUAGE="JavaScript" SRC="../../admin/proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>