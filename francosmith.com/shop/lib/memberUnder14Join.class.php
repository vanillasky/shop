<?
/**
 * ��14�� �̸� ȸ������ ���̺귯��
 * @author pr
 */
class memberUnder14Join
{
	var $useConstraint = false; // (bool) ȸ��������������
	var $under14Status; // ��14�� �̸� ���� ����
	var $ipinYn = 'n'; // ������(�� �ѱ��ſ�����) ��뿩��
	var $niceIpinYn = 'n'; // ������(��) ��뿩��
	var $hpAuthYn = 'n'; // �޴�������Ȯ�� (Mcerti / �帲��ť��Ƽ) ��뿩��
	var $selfCert = false; // (bool) �������� ��뿩��

	function memberUnder14Join()
	{
		// ȸ��������å
		$cfgfile = dirname(__FILE__).'/../conf/fieldset.php';
		if(file_exists($cfgfile)) @include $cfgfile;

		// ȸ��������������
		if ($joinset['under14status'] == 1 || $joinset['under14status'] == 2) {
			$this->useConstraint = true;
		}
		else {
			$this->useConstraint = false;
		}

		// ��14�� �̸� ���� ����
		$this->under14Status = $joinset['under14status']; // 1 : ������ ���� �� ����, 0 : ���ξ��� ����, 2 : ���ԺҰ�

		// �������� > ������
		$this->ipinYn = (empty($ipin['id']) ? 'n' : empty($ipin['useyn']) ? 'n': $ipin['useyn']); // ������(�� �ѱ��ſ�����) ��뿩��
		$this->niceIpinYn = (empty($ipin['code']) ? 'n' : empty($ipin['nice_useyn'])? 'n': $ipin['nice_useyn']); // ������(��) ��뿩��

		// �������� > �޴�������Ȯ�� (Mcerti / �帲��ť��Ƽ) ��뿩��
		if (file_exists(dirname(__FILE__).'/../lib/Hpauth.class.php') === true) {
			$hpauth = Core::loader('Hpauth');
			$hpauthRequestData = $hpauth->getAuthRequestData();
			$this->hpAuthYn = (empty($hpauthRequestData['cpid']) ? 'n' : empty($hpauthRequestData['useyn'])? 'n': $hpauthRequestData['useyn']);
		}

		// �������� ��뿩��
		if ($this->ipinYn == 'y' || $this->niceIpinYn == 'y' || $this->hpAuthYn == 'y') {
			$this->selfCert = true;
		}
		else {
			$this->selfCert = false;
		}
	}

	/**
	 * ������������ ��14�� �̸� ȸ������ ������
	 *
	 * @param string $birthday �������
	 * @return string (�������ڵ� - rejectJoin : ���԰ź�, adminStatus : ������ ���� �� ����, pass : ��� ����)
	 *
	 * Ex) ��14�� �̸� ������� : ���ó�¥(YYYYMMDD) - 14�� + 1��; (���ó�¥(20140625) => '20000626')
	 * Ex) ��14�� �̻� ������� : ���ó�¥(YYYYMMDD) - 14��; (���ó�¥(20140625) => '20000625')
	 *
	 */
	function joinSelfCert($birthday)
	{
		// ���� �ʱ�ȭ
		unset($_SESSION['under14']);

		// 1. ȸ��������������
		if ($this->useConstraint !== true) return 'pass';

		// 2. ��14�� �̸� ��
		if ($this->calMinus1Age($birthday) < 14) { // �̸�
			if ($this->under14Status == '1') { // ������ ���� �� ����
				return 'adminStatus';
			}
			else if ($this->under14Status == '2') { // ���ԺҰ�
				return 'rejectJoin';
			}
		}
		else { // �̻�
			$_SESSION['under14'] = 1;
			return 'pass';
		}
	}

	/**
	 * �������ۼ����� ��14�� �̸� ȸ������ ������
	 *
	 * @return string (�������ڵ� - rejectJoin : ���԰ź�, adminStatus : ������ ���� �� ����, pass : ��� ����, needBirthCheck : ������� üũ �ʿ�)
	 *
	 */
	function joinWrite()
	{
		// 1. ȸ��������������
		if ($this->useConstraint !== true) return 'pass';

		// 2. �������� üũ
		if ($this->selfCert === true) { // ���
			// 2-1. ��14�� �̸� ��
			if ($_SESSION['under14'] != 1) { // �̸�
				if ($this->under14Status == '1') { // ������ ���� �� ����
					return 'adminStatus';
				}
				else if ($this->under14Status == '2') { // ���ԺҰ�
					return 'rejectJoin';
				}
			}
			else { // �̻�
				return 'pass';
			}
		}
		else { // �̻��
			return 'needBirthCheck'; // ������� üũ �ʿ�
		}
	}

	/**
	 * ȸ�����忡�� ��14�� �̸� ȸ������ ������
	 *
	 * @param string $birthday �������
	 * @return string (�������ڵ� - rejectJoin : ���԰ź�, adminStatus : ������ ���� �� ����, pass : ��� ����, over14 : ��14�� �̻�)
	 *
	 * Ex) ��14�� �̸� ������� : ���ó�¥(YYYYMMDD) - 14�� + 1��; (���ó�¥(20140625) => '20000626')
	 * Ex) ��14�� �̻� ������� : ���ó�¥(YYYYMMDD) - 14��; (���ó�¥(20140625) => '20000625')
	 *
	 */
	function joinIndb($birthday='')
	{
		// 1. ȸ��������������
		if ($this->useConstraint !== true) return 'pass';

		// 2. �������� üũ
		if ($this->selfCert === true) { // ���
			// 2-1. ��14�� �̸� ��
			if ($_SESSION['under14'] != 1) { // �̸�
				if ($this->under14Status == '1') { // ������ ���� �� ����
					return 'adminStatus';
				}
				else if ($this->under14Status == '2') { // ���ԺҰ�
					return 'rejectJoin';
				}
			}
			else { // �̻�
				return 'over14';
			}
		}
		else { // �̻��
			if ($birthday == '' || $birthday == '00000000') { // ������� �� ����
				if ($this->under14Status == '1') { // ������ ���� �� ����
					return 'undecidableAdminStatus';
				}
				else if ($this->under14Status == '2') { // ���ԺҰ�
					return 'undecidableRejectJoin';
				}
			}
			else { // ������� �� ����
				// ��14�� �̸� ��
				if ($this->calMinus1Age($birthday) < 14) { // �̸�
					if ($this->under14Status == '1') { // ������ ���� �� ����
						return 'adminStatus';
					}
					else if ($this->under14Status == '2') { // ���ԺҰ�
						return 'rejectJoin';
					}
				}
				else { // �̻�
					return 'over14';
				}
			}
		}
	}

	/**
	 * �� ���� ���
	 *
	 * @param string $birthday �������
	 * @return int �� ����
	 *
	 */

	function calMinus1Age($birthday)
	{
		$birthday = str_replace('-', '', trim($birthday));

		$now_year = date('Y', time());
		$now_month = date('m', time());
		$now_day = date('d', time());

		$birth_year = substr($birthday, 0, 4);
		$birth_month = substr($birthday, 4, 2);
		$birth_day = substr($birthday, 6, 2);

		if ($birth_month < $now_month) {
			$age = $now_year - $birth_year;
		}
		else if ($birth_month == $now_month) {
			if ($birth_day <= $now_day)
				$age= $now_year - $birth_year;
			else
				$age= $now_year - $birth_year - 1;
		}
		else {
			$age= $now_year - $birth_year - 1;
		}

		return $age;
	}
}
?>