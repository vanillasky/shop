<?

include "../conf/config.php";
include "../_header.php"; chkMember();
include "../conf/fieldset.php";
$hpauth = Core::loader('Hpauth');
$hpauthRequestData = $hpauth->getAuthRequestData();

if ($socialMemberService->isEnabled() && $_SESSION['sess']['confirm_password']) {
	$socialMemberServiceList = $socialMemberService->getEnabledServiceList();
	$socialMember = SocialMemberService::getMember();
	$facebookMember = $socialMemberService->getMember(SocialMemberService::FACEBOOK);

	if ($socialMember) {
		$tpl->assign('SocialProfileImage', $socialMember->getProfileImageURL());
	}
	$tpl->assign('FacebookSocialMemberEnabled', in_array(SocialMemberService::FACEBOOK, $socialMemberServiceList));
	$tpl->assign('FacebookSocialMemberConnected', $facebookMember->isConnected() && $facebookMember->getMemberNo() == $sess['m_no']);
	$tpl->assign('FacebookSocialMemberConnectURL', $facebookMember->getConnectURL());

	$tpl->assign('memberSocialStatus', true);
	$tpl->define('memberSocialStatus', 'proc/member_social_status.htm');
}

$mode = "modMember";
$data = $db->fetch("select MB.*, SC.category from ".GD_MEMBER." AS MB LEFT JOIN ".GD_TODAYSHOP_SUBSCRIBE." AS SC ON MB.m_id = SC.m_id where MB.m_id='$sess[m_id]'");

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$data = validation::xssCleanArray($data, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}

$checked[sex][$data[sex]] = "checked";
$checked[marriyn][$data[marriyn]] = "checked";
$checked[calendar][$data[calendar]] = "checked";
$checked[private2][$data[private2]] = "checked";
$checked[private3][$data[private3]] = "checked";
if ($data[mailling]=="y") $checked[mailling] = "checked";
if ($data[sms]=="y") $checked[sms] = "checked";

$selected[job][$data[job]] = "selected";

$data[phone]	= explode("-",$data[phone]);
$data[mobile]	= explode("-",$data[mobile]);
$data[fax]		= explode("-",$data[fax]);
$data[zipcode]	= explode("-",$data[zipcode]);
$data[birth]	= array(
				substr($data[birth],0,2),
				substr($data[birth],2),
				);
$data[marridate]= array(
				substr($data[marridate],0,4),
				substr($data[marridate],4,2),
				substr($data[marridate],6,2),
				);

$data['linked_naverCheckout']	= (preg_match("/\|naverCheckout\|/", $data['outlink'])) ? "y" : "n";

foreach ($checked[reqField] as $k => $v) $required[$k] = "required";

// �����̼�
$_ts_interest = $todayShop->interest();
if ($_ts_interest['use'] == 'y' && $_ts_interest['member'] == 1) {
	$checked['useField']['interest'] = 'checked';
	$selected['interest'][$data['category']] = "selected";
	$tpl->assign('ts_category_all', $ts_category_all);	// ���� _header.php ���� �ҷ��ɴϴ�.
}

$tpl->assign($data);

// �Ҽȷα����� ��� �Ҽ�ȸ������ ������
if ($socialMemberService->isEnabled() && SocialMemberService::getPersistentData('social_code') && !$_SESSION['sess']['confirm_password']) {
	$socialMember = $socialMemberService->getMember(SocialMemberService::getPersistentData('social_code'));
	$tpl->assign('SocialCode', SocialMemberService::getPersistentData('social_code'));
	$tpl->assign('SocialConfirmMemberURL', $socialMember->getConfirmMemberURL('confirm_password'));
	$tpl->define('tpl', 'member/confirm_social_member.htm');
}
// ��Ų ��ġ�� �� ��쿡�� �н����� ���� ������ ��ħ
else if(!$_SESSION['sess']['confirm_password'] && is_file($tpl->template_dir.'/member/confirm_password.htm')) {
	$tpl->define(array(
		'frmMember' => 'member/confirm_password.htm'
	));
}
else {
	$tpl->define(array(
		'frmMember'	=> 'member/_form.htm',
	));
}

// �������� ����
if($_SESSION['sess']['endConfirm'] == "y") {
	unset($_SESSION['sess']['confirm_password']);
	unset($_SESSION['sess']['endConfirm']);
}

//�߰������׸�
$consentData = $consentRequired = array();
$result = $db->query("SELECT *,GC.sno as sno FROM ".GD_CONSENT." AS GC LEFT JOIN ".GD_MEMBER_CONSENT." AS GMC ON GC.sno = GMC.consent_sno AND GMC.m_no = '".$sess['m_no']."' WHERE GC.useyn = 'y' AND (GMC.m_no IS NULL OR GC.requiredyn='n') ORDER BY GC.sno");
while ($datas = $db->fetch($result)){
	$datas['requiredyn_text'] = $datas['requiredyn'] == 'y' ? '�ʼ�' : '����';
	$datas['termsContent'] = htmlspecialchars_decode(@parseCode(htmlspecialchars($datas['termsContent'])));
	$consentData[] = $datas;
}
$tpl->assign('consentData', $consentData);

// ȸ�����Խ� �޴��� ����Ȯ�λ���ϰ� ȸ������������ �޴�������Ȯ�λ���ϸ� �޴����������θ� �޴�����ȣ ����
$mobileReadonly='';
if($hpauthRequestData['useyn'] == 'y' && $hpauthRequestData['moduseyn'] == 'y') $mobileReadonly = 'readonly';
$tpl->assign('hpauthDreamyn', $hpauthRequestData['useyn']);
$tpl->assign('hpauthDreammoduseyn', $hpauthRequestData['moduseyn']);
$tpl->assign('mobileReadonly', $mobileReadonly);
$tpl->assign('hpauthDreamcpid', $hpauthRequestData['cpid']);

### ���Ẹ�ȼ��� ȸ��ó��url
$tpl->assign('memActionUrl',$sitelink->link('member/indb.php','ssl'));

$tpl->print_('tpl');

?>