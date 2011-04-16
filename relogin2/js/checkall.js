/*
 * Írta: Takács Ákos (Rimelek)
 * http://rimelek.hu
 */
function checkAll(chbx,id)
{
	var checked = chbx.checked;
	var container = document.getElementById(id); 
	if (!container) return;

	var inputs = container.getElementsByTagName('input');
	for (var i in inputs)
	{
		if (inputs[i].type != 'checkbox') return;
		inputs[i].checked = checked;
	}
}

