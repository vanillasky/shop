<?php
/*
 * Head�±� �κ��� �ý��ۿ��� �����ϱ� ���� prefilter
 */

function systemHeadTagStart_replace($matches) {
	static $first=true;
	if($first) {
		$first=false;
		return "<head>\n{systemHeadTagStart}";
	}
}

function systemHeadTagEnd_replace($matches) {
	static $first=true;
	if($first) {
		$first=false;
		return "{systemHeadTagEnd}\n</head>";
	}
}


function systemHeadTag($source, $tpl) {
	if(preg_match('/_header\.htm/',$tpl->tpl_path)
		&& !preg_match('/goods_qna_list/',$tpl->tpl_path)
		&& !preg_match('/goods_review_list/',$tpl->tpl_path)) {
		$source = preg_replace_callback('/\<heaD\>/i','systemHeadTagStart_replace',$source);
		$source = preg_replace_callback('/\<\/head\>/i','systemHeadTagEnd_replace',$source);
	}
	return $source;
}


?>