<?php /* Template_ 2.2.7 2016/04/10 14:52:02 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_qna.htm 000004652 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<div class="page_title_div">
	<div class="page_title">1:1 ���ǰԽ���</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">����������</a> &gt; <span class='bold'>1:1���ǰԽ���</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>

<div class="indiv"><!-- Start indiv -->

	<table class="mypage-board-table">
	<tr class="mypage-board-title">
		<th width=8%>��ȣ</th>
		<th width=15%>��������</th>
		<th>����</th>
		<th width=12%>�ۼ���</th>
		<th width=12%>�ۼ���</th>
	</tr>
	</table>

<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
	<div>
<?php if($TPL_V1["sno"]==$TPL_V1["parent"]){?>
		<table class="mypage-board-content" onclick="view_content(this, event)">
		<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
			<td width=8% align="center"><?php echo $TPL_V1["idx"]?></td>
			<td width=15% align="center">[<?php echo $TPL_V1["itemcd"]?>]</td>
			<td align="left"><?php echo $TPL_V1["subject"]?> [<?php echo $TPL_V1["replecnt"]?>]</td>
			<td width=12% align="center"><?php echo $TPL_V1["m_id"]?></td>
			<td width=12% align="center"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
		</tr>
		</table>

<?php }elseif($TPL_V1["sno"]!=$TPL_V1["parent"]){?>
		<table class="mypage-board-content" onclick="view_content(this, event)">
		<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
			<td width=8% align="center"><?php echo $TPL_V1["idx"]?></td>
			<td width=15% align="center">[�亯]</td>
			<td align="left"><span class="ans"><img src="/shop/data/skin/freemart/img/icon/board-re.png">&nbsp;<?php echo $TPL_V1["subject"]?></span></td>
			<td width=12% align="center"><?php echo $TPL_V1["m_id"]?></td>
			<td width=12% align="center"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
		</tr>
		</table>
<?php }?>

		<div class="mypage-txt-wrapper">
			<div class="mypage-txt">
<?php if($TPL_V1["ordno"]!='0'){?>
				<div class="order-no">[ �ֹ���ȣ <?php echo $TPL_V1["ordno"]?> ���� ]</div>
<?php }?>
				<div class="description"><?php echo $TPL_V1["contents"]?></div>
				
				<div style="text-align:right;padding-bottom:5px;">
<?php if($TPL_V1["authreply"]=='Y'){?>
					<button class="button-small button-dark" onclick="popup_register( 'reply_qna', '<?php echo $TPL_V1["sno"]?>' );">�亯</button>
<?php }?>
<?php if($TPL_V1["authmodify"]=='Y'){?>
					<button class="button-small button-dark" onclick="popup_register( 'mod_qna', '<?php echo $TPL_V1["sno"]?>' );">����</button>
<?php }?>
<?php if($TPL_V1["authdelete"]=='Y'){?>
				    <button class="button-small button-dark" onclick="popup_register( 'del_qna', '<?php echo $TPL_V1["sno"]?>' );">����</button>
<?php }?>
			    </div>
			</div>
		</div>
	</div>
<?php }}?>

<div style="float:right;padding:10px 5px">
<button class="button-big button-dark" onclick="popup_register( 'add_qna' )">�۾���</button>
</div>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>


<script language="javascript">

function popup_register( mode, sno )
{
	if ( mode == 'del_qna' ) var win = window.open("../mypage/mypage_qna_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
	else var win = window.open("../mypage/mypage_qna_register.php?mode=" + mode + "&sno=" + sno,"qna_register","width=600,height=500");
	win.focus();
}

var preContent;

function view_content(obj)
{
	var div = obj.parentNode;

	for (var i=1, m=div.childNodes.length;i<m;i++) {
		if (div.childNodes[i].nodeType != 1) continue;	// text node.
		else if (obj == div.childNodes[ i ]) continue;

		obj = div.childNodes[ i ];
		break;
	}

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}
</script>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>