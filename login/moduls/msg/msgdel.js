var delmind = document.getElementById("delall");
if (document.form1)
{
	var hossz = document.form1.elements.length-1;
}
function challit()
{

	if (!document.form1) return;
    if(delmind.checked == true)
    {
        for(i=0;i<hossz;i++){
        document.form1.elements[i].checked = true;
        }
    }else{
        for(i=0;i<hossz;i++){
        document.form1.elements[i].checked = false;
        }
    }
}
if (delmind)
{
	delmind.onclick = challit;
}