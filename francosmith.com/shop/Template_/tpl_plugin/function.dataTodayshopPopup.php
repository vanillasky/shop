<?php

/* Return Popup Data Function */

function dataTodayshopPopup(){

	global $db,$cfg,$tpl;

	@include dirname(__FILE__) . "/../../conf/design_skinToday_" . $cfg['tplSkinToday'] . ".php";

	$popup = array();

	$tmp = array_keys( $design_skinToday );
	$keys = array_ereg( "'^popup/[^/]*$'si", $tmp );

	foreach ( $keys as $filenm ){

		$file = $design_skinToday[$filenm];

		# �˾�â �̻���� ���
		if ( $file['popup_use'] != 'Y' ) continue;

		# �˾�â �Ⱓ �� ���� ����
		if ( $file['popup_sdt'] && $file['popup_sdt'] != "00000000" && $file['popup_edt'] && $file['popup_edt'] != "00000000" ){
			if( $file['popup_sdt'] > date("Ymd") || $file['popup_edt'] < date("Ymd") ){
				continue;
			}else{
				# �ð� ������ �Ǿ��ִ� ��� üũ
				if ( $file['popup_stime'] && $file['popup_stime'] != "0000" && $file['popup_edt'] && $file['popup_edt'] != "0000" ){
					# �����ΰ��
					if( $file['popup_dt2tm'] == "Y" ){
						if( ($file['popup_sdt'].$file['popup_stime']) > date("YmdHi") || ($file['popup_edt'].$file['popup_etime']) < date("YmdHi") ) continue;
					}else{
						if( $file['popup_stime'] > date("Hi") || $file['popup_etime'] < date("Hi") ) continue;
					}
				}
			}
		}

		$filenm = '../../skin_today/'.$cfg['tplSkinToday'].'/'.$filenm;

		$popup[] = array( 'file'=>$filenm, 'code'=>preg_replace( array( "'^popup/'si","'.htm$'si","'[^0-9a-zA-Z]+'si" ), "", $filenm ), 'name'=>$file[text], 'width'=>$file['popup_sizew'], 'height'=>$file['popup_sizeh'], 'top'=>$file['popup_spotw'], 'left'=>$file['popup_spoth'], 'type'=>$file['popup_type'], 'popContents'=>'' );
	}

	return $popup;
}
?>