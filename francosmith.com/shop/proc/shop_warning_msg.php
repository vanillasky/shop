<?php
	

		### ���� ȯ���ڵ�
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (!is_file($file)) msg("���� ���������� ����ϼ���",1);
		$file = file($file);
		$godo = decode($file[1],1);

		### � ���ذ�
		if ( preg_match( "/^rental_mxfree/i", $godo['ecCode'] ) ){ // ������
			$g_warn_day			= 5;		# ���� ��� �Ⱓ
			$g_shopstop_day		= 30;		# �����ȭ�� ���� ������
			$g_drop_day			= 60;		# �ַ�� ���� ������
		}
		else if ( preg_match( "/^rental_mx/i", $godo['ecCode'] ) ){ // �Ӵ���
			if ( $godo['freeType'] == "y" ){
				$g_warn_day			= 5;		# ���� ��� �Ⱓ
				$g_shopstop_day		= 1;		# �����ȭ�� ���� ������
				$g_drop_day			= 1;		# �ַ�� ���� ������
			}else{
				$g_warn_day			= 5;		# ���� ��� �Ⱓ
				$g_shopstop_day		= 30;		# �����ȭ�� ���� ������
				$g_drop_day			= 60;		# �ַ�� ���� ������
			}
		}


		### � üũ
		if ( preg_match( "/^rental_mxfree/i", $godo['ecCode'] ) ){ // ������
			$nowDay = betweenDate(date('Ymd'),$godo['edate']);

			if ( $godo['edate'] != 0 && $nowDay <= $g_warn_day && $nowDay > 0 ){ # ���Ό��ȸ��
				$err_msg = "���� ���θ��� �̿��ϴ� ���� ��� " . $g_drop_day . "�Ͽ� �� ���� ������ ȭ�鿡 �����ؾ� �ϸ� \\n������ ���ӽð� �� " . $g_shopstop_day . "���� ����ϸ� ȸ��� ���� �뺸 ���� �ش� ���θ��� �����ȭ�� ������ ������ �� �ֽ��ϴ�";
			}
			else if ( $godo['edate'] != 0 && $nowDay <= 0 ){ # ����ȸ��

				if ( $nowDay <= ( 0 - ( $g_drop_day - $g_shopstop_day ) ) ){ # �ַ�� ���� ����
					msg("������ ���ӽð� �� " . $g_drop_day . "�� �̻� ����Ͽ� ���θ� �̿��� �Ұ����մϴ�.",-1);
				}
				else { # �����ȭ�� ���� ����
					$err_msg = "������ ���ӽð� �� " . ( abs( $nowDay ) + $g_shopstop_day ) . "��° �����ϼ̽��ϴ�. \\n�����ȭ�� ������ �����Ǿ����ϴ�.";
				}
			}

			$ret = @readurl("http://www.godo.co.kr/userinterface/_godoConn/host_eFree_connect.php?sno=".$godo['sno']);
		}else if(preg_match( "/^rental_mx/i", $godo['ecCode'])){ // �Ӵ���
			$nowDay = betweenDate(date('Ymd'),$godo['edate']);

			if ( $nowDay <= $g_warn_day && $nowDay > 0 ){ # ���Ό��ȸ��
				$err_msg = "���� ���Ⱓ�� {$nowDay}�� ���ҽ��ϴ�.";
			}
			else if ( $nowDay <= 0 ){ // ������ ����

				if ( $nowDay <= ( 0 - $g_drop_day ) ){ # �ַ�� ���� ����
					msg("���� ���� �� {$g_drop_day}�� �̻� ����Ͽ� ���θ� �̿��� �Ұ����մϴ�.",-1);
				}
				else if ( $nowDay <= ( 0 - $g_shopstop_day ) ){ # �����ȭ�� ���� ����
					$err_msg = "���� ���� �� " . abs( $nowDay ) . "�� �������ϴ�.\\n������ �� " . $g_shopstop_day . "���� ����Ͽ� �����ȭ�� ������ ���ѵǾ����ϴ�.\\n���̰��� �����ϼż� ���񽺱Ⱓ�� ������ �ֽñ� �ٶ��ϴ�.";
				}
				else {
					$err_msg = "���� ���� �� " . abs( $nowDay ) . "�� �������ϴ�.\\n������ �� " . $g_shopstop_day . "���� ������ �����ȭ�� ������ ���ѵ˴ϴ�.\\n���̰��� �����ϼż� ���񽺱Ⱓ�� ������ �ֽñ� �ٶ��ϴ�.";
				}
			}
		}
	
?>