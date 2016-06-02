/* $Id: signed.js 8289 2011-11-15 01:19:44Z beckmi $ */
function signed$()
{
    var elements = new Array();

    for (var i = 0; i < arguments.length; i++) {
        var element = arguments[i];
        if (typeof element == 'string') {
            element = document.getElementById(element);
        }

        if (arguments.length == 1) {
            return element;
        }

        elements.push(element);
    }

    return elements;
}


function signedGetElementById(id)
{
    return signed$(id);
}

function signedSetElementProp(name, prop, val)
{
    var elt = signedGetElementById(name);
    if (elt) {
        elt[prop] = val;
    }
}

function signedSetElementStyle(name, prop, val)
{
    var elt = signedGetElementById(name);
    if (elt && elt.style) {
        elt.style[prop] = val;
    }
}

function signedGetFormElement(fname, ctlname)
{
    var frm = document.forms[fname];
    return frm ? frm.elements[ctlname] : null;
}

function signedCheckAll(form, switchId)
{
    var eltForm = signed$(form);
    var eltSwitch = signed$(switchId);
    // You MUST NOT specify names, it's just kept for BC with the old lame crappy code
    if (!eltForm && document.forms[form]) {
        eltForm = document.forms[form];
    }
    if (!eltSwitch && eltForm.elements[switchId]) {
        eltSwitch = eltForm.elements[switchId];
    }

    var i;
    for (i = 0; i != eltForm.elements.length; i++) {
        if (eltForm.elements[i] != eltSwitch && eltForm.elements[i].type == 'checkbox') {
            eltForm.elements[i].checked = eltSwitch.checked;
        }
    }
}


function signedCheckGroup(form, switchId, groupName)
{
    var eltForm = signed$(form);
    var eltSwitch = signed$(switchId);
    // You MUST NOT specify names, it's just kept for BC with the old lame crappy code
    if (!eltForm && document.forms[form]) {
        eltForm = document.forms[form];
    }
    if (!eltSwitch && eltForm.elements[switchId]) {
        eltSwitch = eltForm.elements[switchId];
    }

    var i;
    for (i = 0; i != eltForm.elements.length; i++) {
        var e = eltForm.elements[i];
        if ((e.type == 'checkbox') && ( e.name == groupName )) {
            e.checked = eltSwitch.checked;
            e.click();
            e.click();  // Click to activate subgroups twice so we don't reverse effect
        }
    }
}

function signedCheckAllElements(elementIds, switchId)
{
    var switch_cbox = signedGetElementById(switchId);
    for (var i = 0; i < elementIds.length; i++) {
        var e = signedGetElementById(elementIds[i]);
        if ((e.name != switch_cbox.name) && (e.type == 'checkbox')) {
            e.checked = switch_cbox.checked;
        }
    }
}

function signedSavePosition(id)
{
    var textareaDom = signedGetElementById(id);
    if (textareaDom.createTextRange) {
        textareaDom.caretPos = document.selection.createRange().duplicate();
    }
}
function signedInsertText(domobj, text)
{
    if (domobj.selectionEnd) {
        //firefox
        var start = domobj.selectionStart;
        var end = domobj.selectionEnd;
        domobj.value = domobj.value.substr(0, start) + text + domobj.value.substr(end, domobj.value.length);
        domobj.focus();
        var pos = start + text.length;
        domobj.setSelectionRange(pos, pos);
        domobj.blur();
    } else if (domobj.createTextRange && domobj.caretPos) {
        //IE
        var caretPos = domobj.caretPos;
        caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
    } else if (domobj.getSelection && domobj.caretPos) {
        var caretPos = domobj.caretPos;
        caretPos.text = caretPos.text.charat(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
    } else {
        domobj.value = domobj.value + text;
    }
}

function signedCodeSmilie(id, smilieCode)
{
    var revisedMessage;
    var textareaDom = signedGetElementById(id);
    signedInsertText(textareaDom, smilieCode);
    textareaDom.focus();
    return;
}
function showImgSelected(imgId, selectId, imgDir, extra, signedUrl)
{
    if (signedUrl == null) {
        signedUrl = "./";
    }
    imgDom = signedGetElementById(imgId);
    selectDom = signedGetElementById(selectId);
    if (selectDom.options[selectDom.selectedIndex].value != "") {
        imgDom.src = signedUrl + "/" + imgDir + "/" + selectDom.options[selectDom.selectedIndex].value + extra;
    } else {
        imgDom.src = signedUrl + "/images/blank.gif";
    }
}

function signedExternalLinks()
{
    if (!document.getElementsByTagName) {
        return;
    }
    var anchors = document.getElementsByTagName("a");
    for (var i = 0; i < anchors.length; i++) {
        var anchor = anchors[i];
        if (anchor.getAttribute("href")) {
            // Check rel value with extra rels, like "external noflow". No test for performance yet
            var $pattern = new RegExp("external", "i");
            if ($pattern.test(anchor.getAttribute("rel"))) {
                /*anchor.onclick = function() {
                 window.open(this.href);
                 return false;
                 }*/
                anchor.target = "_blank";
            }
        }
    }
}

function signedOnloadEvent(func)
{
    if (window.onload) {
        signedAddEvent(window, 'load', window.onload);
    }
    signedAddEvent(window, 'load', func);
}

function signedAddEvent(obj, evType, fn)
{
    if (obj.addEventListener) {
        obj.addEventListener(evType, fn, true);
        return true;
    } else {
        if (obj.attachEvent) {
            var r = obj.attachEvent("on" + evType, fn);
            return r;
        } else {
            return false;
        }
    }
}

signedOnloadEvent(signedExternalLinks);