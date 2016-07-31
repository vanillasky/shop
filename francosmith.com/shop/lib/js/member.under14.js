/**
 * ��14�� �̸� ȸ������ ���ɿ��� ������Ϸ� üũ
 *
 * @param string birthDay �������
 * @param string under14Status (1 : ������ ���� �� ����, 2 : ���ԺҰ�)
 * @param string under14Code ȸ������ �������ڵ�
 * @return bool
 *
 */
function chkUnder14(birthDay, under14Status, under14Code)
{
	birthDay = birthDay.replace('-', '');
	var digit = /^[0-9]+$/;
	if (birthDay == '' || birthDay == '00000000' || digit.test(birthDay) === false || birthDay.length != 8) { // ������� �� ����
		if (under14Status == '1') { // ������ ���� �� ����
			if (confirm('��14�� �̸��� Ȯ���� �� �����Ƿ� ������ ���� �� ������ �� �ֽ��ϴ�.\n��� �����Ͻðڽ��ϱ�?') === false) {
				return false;
			}
		}
		else if (under14Status == '2') { // ���ԺҰ�
			alert('��14�� �̸��� Ȯ���� �� �����Ƿ� ȸ�������� ������� �ʽ��ϴ�. �����ڿ��� ������ �ּ���.');
			return false;
		}
	}
	else { // ������� �� ����
		// ��14�� �̸� ��
		if (calMinus1Age(birthDay) < 14) { // �̸�
			if (under14Status == '1') { // ������ ���� �� ����
				if (confirm('��14�� �̸� ȸ���� ��� ������ ���� �� ������ �Ϸ�˴ϴ�.\n��� �����Ͻðڽ��ϱ�?') === false) {
					return false;
				}
				else {
					return true;
				}
			}
			else if (under14Status == '2') { // ���ԺҰ�
				alert('�� 14�� �̸��� ��� ȸ�������� ������� �ʽ��ϴ�.');
				return false;
			}
		}
		else { // �̻�
			return true;
		}
	}
}

/**
 * �� ���� ���
 *
 * @param string birthDay �������
 * @return int �� ����
 *
 */
function calMinus1Age(birthDay)
{
	birthDay = birthDay.replace('-', '');

	var now_date = new Date();
	now_year = parseInt(now_date.getFullYear());
	now_month = parseInt(now_date.getMonth() + 1);
	now_day = parseInt(now_date.getDate());

	birth_year = parseInt(birthDay.substring(0, 4));
	birth_month = parseInt(birthDay.substring(4, 6));
	birth_day = parseInt(birthDay.substring(6, 8));

	if (birth_month < now_month) {
		age = now_year - birth_year;
	}
	else if (birth_month == now_month) {
		if (birth_day <= now_day)
			age= now_year - birth_year;
		else
			age= now_year - birth_year - 1;
	}
	else {
		age= now_year - birth_year - 1;
	}

	return age;
}