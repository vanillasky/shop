function iptNullCheck(ipt_obj, ipt_class) {
	if(!ipt_obj.value) ipt_obj.className = ipt_class;
	else ipt_obj.className = "";
}

function setBorder(obj, colorCode) {
	obj.style.borderColor = colorCode;
}

function checkForm(f) {
	if(!f.shopId.value) {
		alert("���̵� �Է��� �ּ���!");
		f.shopId.focus();
		return false;
	}

	if(!f.shopPassword.value) {
		alert("��й�ȣ�� �Է��� �ּ���!");
		f.shopPassword.focus();
		return false;
	}

	if(!f.MallUserSSN1.value || f.MallUserSSN1.value.length != 6) {
		alert("�ֹε�Ϲ�ȣ ���ڸ��� ��Ȯ�� �Է����ּ���. (6��)");
		f.MallUserSSN1.focus();
		return false;
	}
	if(!f.MallUserSSN2.value || f.MallUserSSN2.value.length != 7) {
		alert("�ֹε�Ϲ�ȣ ���ڸ��� ��Ȯ�� �Է����ּ���. (7��)");
		f.MallUserSSN2.focus();
		return false;
	}

	return true;
}

function checkAgreeForm(f) {
	if(!f.agree.checked) {
		alert("���̹� üũ�ƿ� ���θ� ȸ�� ���� ���񽺸�\n�̿��Ͻ÷��� �����ĺ� ���� ������ �����ϼž� �մϴ�.");
		f.agree.focus();
		return false;
	}

	if(!f.agree2.checked) {
		alert("���̹� üũ�ƿ� ���θ� ȸ�� ���� ���񽺸�\n�̿��Ͻ÷��� �������� ������ �����ϼž� �մϴ�.");
		f.agree2.focus();
		return false;
	}

	return true;
}