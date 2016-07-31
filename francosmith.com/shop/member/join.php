<?
include "../_header.php";
include "../conf/fieldset.php";

$config = Core::loader('config');
$hpauth = Core::loader('Hpauth');
$shopConfig = $config->load('config');
$hpauthRequestData = $hpauth->getAuthRequestData();

### ȸ����������
if( $sess ){
	msg("������ �α��� ���Դϴ�.",$code=-1 );
}

unset($fld['per']['resno']);

$mode = "joinMember";
$checked[sex][m] = $checked[calendar][s] = "checked";
if ($_POST[resno][1] && $_POST[resno][1][0]%2==0) $checked[sex][w] = "checked";
foreach ($checked[reqField] as $k => $v) $required[$k] = 'required fld_esssential';

if ($_POST[resno][0]){
	$_POST[birth_year] = 1900 + substr($_POST[resno][0],0,2) + floor((substr($_POST[resno][1],0,1)-1)/2) * 1000;
	$_POST[birth][0] = substr($_POST[resno][0],2,2);
	$_POST[birth][1] = substr($_POST[resno][0],4,2);
}

// �������ۼ����� ��14�� �̸� ȸ������ ������
if (strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF']) && isset($_POST['type'])) {
	$mUnder14 = Core::loader('memberUnder14Join');
	$under14Code = $mUnder14->joinWrite();
	if ( $under14Code == 'rejectJoin' ) { // ��14�� �̸� ȸ������ �ź�
		msg('�� 14�� �̸��� ��� ȸ�������� ������� �ʽ��ϴ�.', -1);
	}
	$tpl->assign('under14Code', $under14Code);
	$tpl->assign('under14Status', $mUnder14->under14Status);
	$customHeader .= '<script src="../lib/js/member.under14.js" type="text/javascript"></script>';
	$tpl->assign('customHeader', $customHeader);
}
else if ($socialMemberService->isEnabled() && $_GET['MODE'] === 'social_member_join') {
	$mUnder14 = Core::loader('memberUnder14Join');
	$under14Code = $mUnder14->joinWrite();
	$tpl->assign('under14Code', $under14Code);
	$tpl->assign('under14Status', $mUnder14->under14Status);
	$customHeader .= '<script src="../lib/js/member.under14.js" type="text/javascript"></script>';
	$tpl->assign('customHeader', $customHeader);
}

if ($socialMemberService->isEnabled()) {
	$_MODE = $_GET['MODE'];
	$_SOCIAL_CODE = $_GET['SOCIAL_CODE'];
	$socialMember = SocialMemberService::getMember($_SOCIAL_CODE);
	if (!isset($_MODE)) {
		$enabledSocialMemberServiceList = $socialMemberService->getEnabledServiceList();
		if (in_array(SocialMemberService::FACEBOOK, $enabledSocialMemberServiceList)) {
			$facebookMember = SocialMemberService::getMember(SocialMemberService::FACEBOOK);
			$tpl->assign('FacebookLoginURL', $facebookMember->getLoginURL());
		}
		$tplfile = 'member/join_type.htm';
	}
	else if ($_MODE === 'social_member_join') {
		$name = $socialMember->getName();
		$email = $socialMember->getEmail();
		if (strlen($email) > 0) {
			$emailID = array_shift(explode('@', $email));
			list($memberID) = $db->fetch('SELECT m_id FROM '.GD_MEMBER.' WHERE email="'.$email.'"');
			if (strlen($memberID) < 1) {
				//�޸���� ��ȸ
				$dormant = Core::loader('dormant');
				list($memberID) = $dormant->checkDormantEmail($email, 'm_id');
			}

			if (strlen($memberID) > 0) {
				msg($email.'\r\n�ٸ� �������� ������� �̸��� �Դϴ�.\r\n�̹� �����Ͻ� ��� �ٸ� �α��� ������ ���� �α��� �� �ֽñ� �ٶ��ϴ�.', './login.php');
			}
			else {
				$tpl->assign('email', $email);
				$tpl->assign('email_id', $emailID);
				$tplfile = 'member/social_member_join.htm';
			}
		}
		else {
			$tplfile = 'member/social_member_join.htm';
		}

		// ������� ��������
		$birthday = $socialMember->getBirthday();
		if (strlen($birthday) == 8) {
			$birth_year = substr($birthday, 0, 4);
			$birth[0] = substr($birthday,4,2);
			$birth[1] = substr($birthday,6,2);
			$tpl->assign('birth_year', $birth_year);
			$tpl->assign('birth', $birth);
		}

		$tpl->assign('name', $name);
		$tpl->assign('MODE', $_MODE);
		$tpl->assign('SOCIAL_CODE', $_SOCIAL_CODE);
	}
	else if (strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF']) && isset($_POST['type'])) {
		$tplfile = 'member/join.htm';
	}
	else {
		$tplfile = 'member/agreement.htm';
	}
}
else {
	if (strpos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF']) && isset($_POST['type'])) {
		$tplfile = 'member/join.htm';
	}
	else {
		$tplfile = 'member/agreement.htm';
	}
}

if ($_POST['rncheck'] == 'ipin' || $_POST['rncheck'] == 'hpauthDream') {
	if ($_POST['sex'] == "M") 	$checked[sex][m] = "checked";
	else 						$checked[sex][w] = "checked";

	if (strlen($_POST['birthday']) == 8) {
		$_POST[birth_year] = substr($_POST['birthday'], 0, 4);
		$_POST[birth][0] = substr($_POST['birthday'],4,2);
		$_POST[birth][1] = substr($_POST['birthday'],6,2);
	}

	$_POST['name'] = $_POST['nice_nm'];

	if (strlen($_POST['mobile']) == 11) { //�޴����ڸ��� 11�ڸ��̸�
		$mobile[0] = substr($_POST['mobile'],0,3);
		$mobile[1] = substr($_POST['mobile'],3,4);
		$mobile[2] = substr($_POST['mobile'],7,4);
	} else if (strlen($_POST['mobile']) == 10) { //�޴����ڸ��� 10�ڸ��̸�
		$mobile[0] = substr($_POST['mobile'],0,3);
		$mobile[1] = substr($_POST['mobile'],3,3);
		$mobile[2] = substr($_POST['mobile'],6,4);
	}
	$_POST['mobile'] = $mobile;

}

### ���̹� üũ�ƿ�(ȸ������)
@include "../conf/naverCheckout.cfg.php";
if($checkoutCfg['useYn']=='y' && $checkoutCfg['ncMemberYn']=='y'):
	require "../lib/naverCheckout.class.php";
	$NaverCheckout = Core::loader('NaverCheckout');
	$ncResData = $NaverCheckout->get_oneclickJoin($_POST);

	if ($ncResData['mode'] == 'agreement') { // �̿���
		$tpl->assign('naverCheckout_oneclickStep',$ncResData['stepHtml']);
		$realname[useyn] = $ipin[useyn] = ''; // �Ǹ�Ȯ��/������ �̻��ó��
	} else if ($ncResData['mode'] == 'form') { // �������ۼ�
		$tpl->assign('naverCheckout_oneclickStep',$ncResData['stepHtml']);
		$tpl->assign($ncResData['data']);
		if (substr($ncResData['data']['resno'][1],0,1)%2 == 1) $checked[sex][m] = "checked";
		else $checked[sex][w] = "checked";
	}
endif;

$ipinyn = (empty($ipin['id']) ? 'n' : empty($ipin['useyn']) ? 'n': $ipin['useyn']);
$niceipinyn = (empty($ipin['code']) ? 'n' : empty($ipin['nice_useyn'])? 'n': $ipin['nice_useyn']);
$ipinStatus = ($ipinyn == 'y' || $niceipinyn == 'y') ? 'y' : 'n';

if($tplfile == 'member/agreement.htm' || $tplfile == 'member/social_member_join.htm'){
	//�̿���
	$termsAgreement = getTermsGuideContents('terms', 'termsAgreement', 'Y');
	$tpl->assign('termsAgreement', $termsAgreement);

	//�������� ��޹�ħ ȸ������
	$termsPolicyCollection2 = getTermsGuideContents('terms', 'termsPolicyCollection2');
	$tpl->assign('termsPolicyCollection2', $termsPolicyCollection2);

	//�������� ��3�� ��������
	if($cfg['private2YN'] === 'Y'){
		$termsThirdPerson = getTermsGuideContents('terms', 'termsThirdPerson');
		$tpl->assign('termsThirdPerson', $termsThirdPerson);
	}

	//�������� ��޾��� ��Ź����
	if($cfg['private3YN'] === 'Y'){
		$termsEntrust = getTermsGuideContents('terms', 'termsEntrust');
		$tpl->assign('termsEntrust', $termsEntrust);
	}

	//�߰������׸�
	$consentData = $consentRequired = array();
	$result = $db->query("SELECT * FROM ".GD_CONSENT." WHERE useyn = 'y' ORDER BY sno ASC");
	while ($data = $db->fetch($result)){
		$data['requiredyn_text'] = $data['requiredyn'] == 'y' ? '�ʼ�' : '����';
		$data['termsContent'] = htmlspecialchars_decode(@parseCode(htmlspecialchars($data['termsContent'])));
		$consentData[] = $data;
	}
}

// ȸ�����Խ� �޴��� ����Ȯ�λ���ϰ� ȸ�� ���Խ� �޴��� ��ȣ ������ �Ұ����ϸ� �޴�����ȣ ���� �Ұ�
$mobileReadonly='';
if($hpauthRequestData['useyn']=='y' && $hpauthRequestData['modyn']=='n' && $_POST['rncheck']=='hpauthDream') $mobileReadonly='readonly';

$tpl->assign($_POST);
$tpl->assign('realnameyn', (empty($realname[id]) ? 'n' : empty($realname[useyn])? 'n': $realname[useyn]));
$tpl->assign('ipinyn', $ipinyn);
$tpl->assign('niceipinyn', $niceipinyn);
$tpl->assign('ipinStatus', $ipinStatus);
$tpl->assign('hpauthDreamyn', $hpauthRequestData['useyn']);
$tpl->assign('hpauthDreammodyn', $hpauthRequestData['modyn']);
$tpl->assign('mobileReadonly', $mobileReadonly);
$tpl->assign('hpauthDreamcpid', $hpauthRequestData['cpid']);
$tpl->assign('shopName', $cfg['shopName']);
$tpl->define(array(
			'frmMember'		=> 'member/_form.htm',
			'tpl'			=> $tplfile,
			));
$tpl->define('member_join_auth', 'proc/member_join_auth.htm');

### ���Ẹ�ȼ��� ȸ��ó��url
$tpl->assign('memActionUrl',$sitelink->link('member/indb.php','ssl'));

// �߰������׸�
$tpl->assign('consentData', $consentData);

$tpl->print_('tpl');

?>