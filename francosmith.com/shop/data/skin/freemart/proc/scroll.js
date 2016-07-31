/*{*** ��ũ�ѹ�� ��ũ��Ʈ | proc/scroll.js ***}*/
var bodyHeight = scrollobjHeight = 0;

if ( document.all ) window.attachEvent("onload", initSlide); // IE ���
else window.addEventListener("load", initSlide, false); // FF(������) ���

function initSlide()
{
	var scroll = document.getElementById('scroll');
	var scrollTop = get_objectTop(document.getElementById('pos_scroll'));
	scroll.style.top = document.body.scrollTop + scrollTop;
	bodyHeight = document.body.scrollHeight;
	scrollobjHeight = scroll.clientHeight;
	movingSlide();
}

function movingSlide()
{
	var yMenuFrom, yMenuTo, yOffset, timeoutNextCheck;
	var scroll = document.getElementById('scroll');
	var scrollTop = get_objectTop(document.getElementById('pos_scroll'));

	yMenuFrom  = parseInt (scroll.style.top, 10);
	yMenuTo    = document.body.scrollTop + 10;
	if(yMenuTo<scrollTop) yMenuTo = scrollTop;
	timeoutNextCheck = 500;
	if (yMenuFrom != yMenuTo) {
		yOffset = Math.ceil(Math.abs(yMenuTo - yMenuFrom) / 10);
		if (yMenuTo < yMenuFrom) yOffset = -yOffset;
		scroll.style.top = parseInt (scroll.style.top, 10) + yOffset;
		timeoutNextCheck = 100;
	}
	if (scroll.style.pixelTop > bodyHeight - scrollobjHeight) scroll.style.top = bodyHeight - scrollobjHeight;

	setTimeout ("movingSlide()", timeoutNextCheck);
}

function gdscroll(gap)
{
	var gdscroll = document.getElementById('gdscroll');
	gdscroll.scrollTop += gap;
}