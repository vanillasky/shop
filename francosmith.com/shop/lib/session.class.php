<?php
/*
	Session Ŭ����
*/
class session {

	var $m_no;
	var $m_id;
	var $level;
	var $groupsno;
	var $dc;

	function session() {
		if($_SESSION['sess']['m_no']) {
			$this->m_no=$_SESSION['sess']['m_no'];
			$this->m_id=$_SESSION['sess']['m_id'];
			$this->level=$_SESSION['sess']['level'];
			$this->groupsno=$_SESSION['sess']['groupsno'];
			$this->dc=$_SESSION['sess']['dc'];
		}
		else {
			$this->m_no=false;
			$this->m_id='';
			$this->level='';
			$this->groupsno='';
			$this->dc='';
		}
	}

	/*
		�α���

		return ��
		���������� �α��� �� ��� = true
		���̵� ��й�ȣ�� �Է����Ŀ� ��߳� ��� = NOT_VALID
		���̵� ��й�ȣ ���� �ʴ� ��� = NOT_FOUND
		������ ���ε��� �ʴ� ��� = NOT_ACCESS
	*/
	function login($id,$password) {
		// �Է� ���� üũ
		$validation_check = array(
			'id'=>array('require'=>true,'pattern'=>'/^[\xa1-\xfea-zA-Z0-9_-]{4,20}$/'),
			'password'=>array('require'=>true,'pattern'=>'/^[\x21-\x7E]{4,}$/'),
		);
		$chk_result = array_value_cheking($validation_check,array('id'=>$id,'password'=>$password));
		if(count($chk_result)) {
			return 'NOT_VALID';
		}

		// ���̵�,��й�ȣ ��ȸ
		$db = Core::loader('db');

		//�޸�ȸ�� üũ - ��й�ȣ Ȯ�� ��� ����� ���� ���� ���
		$dormantRestore = false;
		$dormant = Core::loader('dormant');
		$dormantRestore = $dormant->checkDormantLogin($id, $password);

		$query = $db->_query_print('
			select
				m.m_no,
				m.m_id,
				m.name,
				m.nickname,
				m.email,
				m.status,
				m.level,
				m.password_moddt,
				g.dc,
				g.sno gsno
			from
				gd_member as m
				left join gd_member_grp as g on m.level=g.level
			where
				m.m_id = [s] and
				m.password in (password([s]),old_password([s]),[s])
		',$id,$password,$password,md5($password));

		$result = $db->_select($query);
		$result = $result[0];

		if(!$result['m_no']) { // ��ġ�ϴ� ��� ���� ���� ���
			return 'NOT_FOUND';
		}

		if($result['status']==1)  { // �α����� ������ ���
			// ������� �ڵ��α��� ��Ű�� �ִ� ��� ����ó��
			if ($result['level'] >= 80 && function_exists('accessCookieMemberInfo')) {
				// �ڵ��α��� ��Ű�� �ִ� ���
				if (accessCookieMemberInfo()) {
					// �ڵ��α��� ��Ű�� �ı����� �α��� ���� ó��
					expireCookieMemberInfo();
					return false;
				}
			}

			// �α��� ������ ���� �ֱ� �α��γ�¥&�޸�ȸ���ȳ����� ����
			$query = $db->_query_print('
				update gd_member set
					last_login = now(),
					cnt_login = (cnt_login+1),
					last_login_ip = [s],
					dormant_mailSendDate = "",
					dormant_smsSendCheck = ""
				where
					m_no = [s]
			',$_SERVER['REMOTE_ADDR'],$result['m_no']);
			$db->query($query);

			// �������� ����
			$_SESSION['sess']=array(
				'm_no'=>$result['m_no'],
				'm_id'=>$result['m_id'],
				'level'=>$result['level'],
				'groupsno'=>$result['gsno'],
				'dc'=>($result['dc'] ? $result['dc'].'%' : ''),
			);
			$_SESSION['member']=array(
				'name'=>$result['name'],
				'email'=>$result['email'],
				'nickname'=>$result['nickname'],
				'password_moddt'=>$result['password_moddt'],
			);
			$this->session();

			//�޸�ȸ�� �α��� ������
			if($dormantRestore === true){
				echo '<script>alert("ȸ������ ������ �޸�ȸ������ �Ϲ� ȸ������ ���� �Ǿ����ϴ�.\n���������� ���񽺸� �̿��� �� �ֽ��ϴ�.");</script>';
			}

			return true;
		}
		else {
			return 'NOT_ACCESS';
		}

	}

	/*
		���� ����

	 *	ȸ���� �α��� �� ���¿���
	 *	�����ڰ� �α��� �� ȸ�� ��� ���� �Ǵ� ȸ�� ���� ������
	 *	���� ���
	 *	ȸ���� ������ �̵� �Ǵ� ���ΰ�ħ �� �� ����� ���� ����
	*/
	function sessionUpdate(){
		if($_SESSION['sess']['m_no']){ // ȸ�� �α��� �Ǿ� �ִ� ����
			$db = Core::loader('db');

			$query = $db->_query_print('
				select
					m.m_no,
					m.m_id,
					m.name,
					m.nickname,
					m.email,
					m.status,
					m.level,
					m.password_moddt,
					g.dc,
					g.sno gsno
				from
					gd_member as m
					left join gd_member_grp as g on m.level=g.level
				where
					m.m_no = [s]
			',$_SESSION['sess']['m_no']);

			$result = $db->_select($query);
			$result = $result[0];

			if(!$result['m_no']) { // ȸ���� �α��� �Ǿ� �ִ� ���¿��� �����ڰ� ���� ���� ���
				$this->logout();
				$this->session();
			}

			if($result['status']==1)  { // ȸ���� �����ϴ� ������ ���
				// �������� ����
				$_SESSION['sess']['m_no'] = $result['m_no'];
				$_SESSION['sess']['m_id'] = $result['m_id'];
				$_SESSION['sess']['level'] = $result['level'];
				$_SESSION['sess']['groupsno'] = $result['gsno'];
				$_SESSION['sess']['dc'] = ($result['dc'] ? $result['dc'].'%' : '');

				$_SESSION['member']['name'] = $result['name'];
				$_SESSION['member']['email'] = $result['email'];
				$_SESSION['member']['nickname'] = $result['nickname'];
				$_SESSION['member']['password_moddt'] = $result['password_moddt'];
				$this->session();
			}
			else { // ȸ���� �α��� �Ǿ� �ִ� ���¿��� �����ڰ� �̽��� ���� ���� ���
				$this->logout();
				$this->session();
			}
		} else { // �α��� �ȵǾ� �ִ� ����
		}
	}

	function socialLogin(SocialMember $socialMember)
	{
		$db = Core::loader('db');
		$m_no = $socialMember->getMemberNo();

		//�޸�ȸ�� üũ - ��й�ȣ Ȯ�� ��� ����� ���� ���� ���
		$dormantRestore = false;
		$dormant = Core::loader('dormant');
		$dormantRestore = $dormant->checkDormantSocialLogin($m_no);

		// ȸ������ ��ȸ
		$query = $db->_query_print('
			select
				m.m_no,
				m.m_id,
				m.name,
				m.nickname,
				m.email,
				m.status,
				m.level,
				m.password_moddt,
				g.dc,
				g.sno gsno
			from
				gd_member as m
				left join gd_member_grp as g on m.level=g.level
			where
				m.m_no = [i]
		',$m_no);

		$result = $db->_select($query);
		$result = $result[0];

		if(!$result['m_no']) { // ��ġ�ϴ� ��� ���� ���� ���
			return 'NOT_FOUND';
		}

		if($result['status']==1)  { // �α����� ������ ���
			// �α��� ������ ���� �ֱ� �α��γ�¥ ����
			$query = $db->_query_print('
				update gd_member set
					last_login = now(),
					cnt_login = (cnt_login+1),
					last_login_ip = [s],
					dormant_mailSendDate = "",
					dormant_smsSendCheck = ""
				where
					m_no = [s]
			',$_SERVER['REMOTE_ADDR'],$result['m_no']);
			$db->query($query);

			// �������� ����
			$_SESSION['sess']=array(
				'm_no'=>$result['m_no'],
				'm_id'=>$result['m_id'],
				'level'=>$result['level'],
				'groupsno'=>$result['gsno'],
				'dc'=>($result['dc'] ? $result['dc'].'%' : ''),
			);
			$_SESSION['member']=array(
				'name'=>$result['name'],
				'email'=>$result['email'],
				'nickname'=>$result['nickname'],
				'password_moddt'=>$result['password_moddt'],
			);
			$this->session();

			//�޸�ȸ�� �α��� ������
			if($dormantRestore === true){
				echo '<script>alert("ȸ������ ������ �޸�ȸ������ �Ϲ� ȸ������ ���� �Ǿ����ϴ�.\n���������� ���񽺸� �̿��� �� �ֽ��ϴ�.");</script>';
			}

			return true;
		}
		else {
			return 'NOT_ACCESS';
		}
	}

	/*
		�α׾ƿ�
	*/
	function logout() {
		session_unset();
		session_destroy();
		setCookie('Xtime','',0,'/');
		setcookie('gd_cart','',time() - 3600,'/');
		setcookie('gd_cart_direct','',time() - 3600,'/');
		if (SocialMemberService::getPersistentData('social_code')) {
			$this->socialLogout(SocialMemberService::getPersistentData('social_code'));
		}
	}

	function socialLogout($socialCode)
	{
		include dirname(__FILE__).'/SocialMember/SocialMemberServiceLoader.php';
		$socialMember = SocialMemberService::getMember($socialCode);
		$socialMember->logout();
	}

}


?>
