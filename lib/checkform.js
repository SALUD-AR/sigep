function checkform() {
	nocheckform="remito|factura";
	ret=false;
	r=parent.frame2.document.forms.length;
	for (var s=0; s<r; s++) {
		if (nocheckform.indexOf(parent.frame2.document.forms[s].name,0) != -1 && parent.frame2.document.forms[s].name != "")
			ret=testDefaultValues(parent.frame2.document.forms[s]);
	}
	return ret;
}

function testDefaultValues(what) {
    var result = false;
    var output = '';
    for (var i=0, j=what.elements.length; i<j; i++) {
        myType = what.elements[i].type;
        if (myType == 'checkbox' || myType == 'radio') {
            if (what.elements[i].checked != what.elements[i].defaultChecked) {
                output += what.elements[i].name + ' es check' + what.elements[i].defaultChecked + '\n';
                result = true
            }
        }
        if (myType == 'hidden' || myType == 'password' || myType == 'text' || myType == 'textarea') {
            if (what.elements[i].value != what.elements[i].defaultValue) {
                output += what.elements[i].name + ' es igual "' + what.elements[i].defaultValue + '"' + '\n';
                result = true
            }
        }
        if (myType == 'select-one' || myType == 'select-multiple') {
            for (var k=1, l=what.elements[i].options.length; k<l; k++) {
                if (what.elements[i].options[k].selected != what.elements[i].options[k].defaultSelected) {
                    output += what.elements[i].name + ' option ' + k + ' is still selected' + '\n';
                    result = true
                }
            }
        }
    }
	//if (output)
		//alert (output);
    return result;
}
