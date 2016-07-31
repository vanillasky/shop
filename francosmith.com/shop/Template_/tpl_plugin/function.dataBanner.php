<?php

/* Return Banner Data Function */

function dataBanner( $loccd, $limit=0, $random='' ){

	global $db,$cfg,$isTodayShopPage;

	$load = false;

	if ($isTodayShopPage === true) {
		$root_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin_today/' . $cfg['tplSkinToday'] . '/img/banner/'; # ��Ʈ���
		$link_path = $cfg['rootDir'] . '/data/skin_today/' . $cfg['tplSkinToday'] . '/img/banner/'; # ��ũ���

		$banner = array();

		$query = "select img, linkaddr, target from ".GD_BANNER." where tplSkin = '$cfg[tplSkinToday]' and loccd='$loccd'";
		$r = $db->query($query,1);

		if (mysql_num_rows($r) > 0) $load = true;
	}

	// �Ϲݸ� ��ʸ� �ҷ����� ����
	if ($load === false) {

		$root_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkin'] . '/img/banner/'; # ��Ʈ���
		$link_path = $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkin'] . '/img/banner/'; # ��ũ���

		$banner = array();

		$query = "select img, linkaddr, target from ".GD_BANNER." where tplSkin = '$cfg[tplSkin]' and loccd='$loccd'";
	}

	if ( $random == 'Y' ) $query .= " order by Rand()";
	else $query .= " order by sort asc, regdt desc";

	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);

	while ( $data = $db->fetch( $res, 1 ) ){

		$img = $data[img];
		$data[img] = $link_path . $data[img];

		if ( $data[linkaddr] == '' ) $data[linkaddr] = 'nolink';

		if ( preg_match( "'\.swf$'is", $data[img] ) ){ // �÷��� �����ΰ��

			# �÷����� ���̵��� ����
			$randNo		= mt_rand(1,10000);
			$IDNameFlash = "godo" . $randNo;

			$swf = new swfheader(false) ;
			$swf->loadswf($root_path.$img) ;

			$data[tag] = '<script>embed("' . $data[img] . '?pageNum=' . $IDNameFlash.'",' . $swf->width . ',' . $swf->height . ')</script>';
		}
		else{ // �̹��� ȭ���ΰ��

			$data[tag] = '<img src="'.$data[img].'" align="absmiddle">';

			if ( $data[linkaddr] != '' && $data[linkaddr] != 'nolink' ){
				$data[tag] = '<a href="' . $data[linkaddr] . '" target="' . $data[target] . '">' . $data[tag] . '</a>';
			}
		}

		$banner[] = $data;
	}

	return $banner;
}
?>