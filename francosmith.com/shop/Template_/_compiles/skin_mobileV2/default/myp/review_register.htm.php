<?php /* Template_ 2.2.7 2014/01/20 20:32:50 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/review_register.htm 000005683 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<?php  $TPL_VAR["page_title"] = "��ǰ�� ����";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#nreviewregister {background:#FFFFFF; padding:12px;}
section#nreviewregister table{border:none; border-top:solid 1px #dbdbdb;width:100%;}
section#nreviewregister table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#nreviewregister table th{text-align:center; background:#f5f5f5; width:70px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}
section#nreviewregister table .img{padding:5px; width:60px;}
section#nreviewregister table .img img{border:solid 1px #d9d9d9;}
section#nreviewregister table td input[type=text], input[type=password], select{width:95%;height:21px;}
section#nreviewregister table td textarea{width:95%;height:116px;}
section#nreviewregister .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#nreviewregister .btn_center .btn_save{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum;}
section#nreviewregister .btn_center .btn_prev{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum;}
section#nreviewregister .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
section#nreviewregister .goods-price{color:#f03c3c; font-size:12px;}
section#nreviewregister .attach{float:left;}
section#nreviewregister .camera_btn{width:80px; height:27px; line-height:27px; font-size:12px; color:#FFFFFF; font-weight:normal;text-align:center; background:url("/shop/data/skin_mobileV2/default/common/img/info/btn_blue01_off.png") no-repeat;}
section#nreviewregister .camera_btn :active{background:url("/shop/data/skin_mobileV2/default/common/img/detailp/btn_wishlist_on.png") no-repeat; float:left;}
</style>

<form method=post action="indb.php" enctype="multipart/form-data" onSubmit="return chkForm(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=goodsno value="<?php echo $GLOBALS["goodsno"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">
<input type=hidden name=referer value="<?php echo $GLOBALS["referer"]?>">

<section id="nreviewregister"  class="content">
	<table>
	<tr>
		<th class="img"><?php echo goodsimgMobile($GLOBALS["goods"]["img_s"], 50)?></th>
		<td>
			<div class="goods-nm">
				<?php echo $GLOBALS["goods"]["goodsnm"]?>

			</div>
			<div class="goods-price">
				<?php echo number_format($GLOBALS["goods"]["price"])?>��
			</div>
		</td>
	</tr>
	</table>
	<div style="height:12px;"></div>
	<table>
	<tr>
		<th>�ۼ���</th>
		<td>
			<input type="text" name="name" required label="�ۼ���" value="<?php echo $GLOBALS["data"]["name"]?>" />
		</td>
	</tr>
<?php if(!$GLOBALS["sess"]&&empty($GLOBALS["data"]['m_no'])){?>
	<tr>
		<th>��й�ȣ</th>
		<td>
			<input type=password name=password style="width:100" required label="��й�ȣ">
		</td>
	</tr>
<?php }?>
	<tr>
		<th>����</th>
		<td>
			<input type="text" name="subject" required label="����" value="<?php echo $GLOBALS["data"]["subject"]?>" />
		</td>
	</tr>
	<tr>
		<th>����</th>
		<td>
			<textarea name="contents" required label="����"  ><?php echo $GLOBALS["data"]["contents"]?></textarea>
		</td>
	</tr>

	<tr>
		<th>����</th>
		<td>
			<table width=100% cellpadding=0 cellspacing=0 border=0>
			<col align=center>
			<tr>
				<td colspan="2"><div style="font-size:10px;">
				* ������ �ִ� <?php echo $GLOBALS["reviewFileNum"]?>������ ���ε尡 �����˴ϴ�.<br>
<?php if($GLOBALS["cfg"]["reviewLimitPixel"]){?>* ������ ���� ����� <?php echo number_format($GLOBALS["cfg"]["reviewLimitPixel"])?>px���� Ŭ ��� �ڵ� �������� �˴ϴ�.<br><?php }?>
<?php if($GLOBALS["cfg"]["reviewFileSize"]){?>* ������ ��� �ִ� <?php echo $GLOBALS["cfg"]["reviewFileSize"]?>KB�� ���� �� �����ϴ�.<br><?php }?>
				</div></td>
			</tr>
			<?php echo $GLOBALS["data"]["fileupload"]?>

			</table>
		</td>
	</tr>

	<tr>
		<th>��</th>
		<td>
			<select name="point">
				<option value="5" <?php echo $GLOBALS["data"]["point"]['5']?>>�ڡڡڡڡ�</option>
				<option value="4" <?php echo $GLOBALS["data"]["point"]['4']?>>�ڡڡڡڡ�</option>
				<option value="3" <?php echo $GLOBALS["data"]["point"]['3']?>>�ڡڡڡ١�</option>
				<option value="2" <?php echo $GLOBALS["data"]["point"]['2']?>>�ڡڡ١١�</option>
				<option value="1" <?php echo $GLOBALS["data"]["point"]['1']?>>�ڡ١١١�</option>
			</select>
		</td>
	</tr>
<?php if($GLOBALS["cfg"]["reviewSpamBoard"]& 2){?>
	<tr>
		<th>�ڵ����<br />����</th>
		<td class=cell_L><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></td>
	</tr>
<?php }?>
	</table>



	<div class="m_review">
		<div class="btn_center">
			<button type="submit" id="save-btn" class="btn_save">Ȯ ��</button>
			<button type="button" id="prev-btn" class="btn_prev"  onclick="history.back();">�� ��</button>
		</div>
	</div>

</section>
</form>

<?php $this->print_("footer",$TPL_SCP,1);?>