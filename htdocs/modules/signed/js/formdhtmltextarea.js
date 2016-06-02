/* $Id: formdhtmltextarea.js 7381 2011-08-29 05:36:17Z beckmi $ */

function signedCodeUrl(id, enterUrlPhrase, enterWebsitePhrase)
{
    if (enterUrlPhrase == null) {
        enterUrlPhrase = "Enter the URL of the link you want to add:";
    }
    var text = prompt(enterUrlPhrase, "");
    var domobj = signedGetElementById(id);
    if ( text != null && text != "" ) {
        var selection = signedGetSelect(id);
        if (selection.length > 0){
            var text2 = selection;
        }else {
            var text2 = prompt(enterWebsitePhrase, "");
        }
        if ( text2 != null ) {
            if ( text2 == "" ) {
                var result = "[url=" + text + "]" + text + "[/url]";
            } else {
                var pos = text2.indexOf(unescape('%00'));
                if(0 < pos){
                    text2 = text2.substr(0,pos);
                }
                var result = "[url=" + text + "]" + text2 + "[/url]";
            }
            signedInsertText(domobj, result);
        }
    }
    domobj.focus();
}

function signedCodeImg(id, enterImgUrlPhrase, enterImgPosPhrase, imgPosRorLPhrase, errorImgPosPhrase, enterImgWidthPhrase)
{
    if (enterImgUrlPhrase == null) {
        enterImgUrlPhrase = "Enter the URL of the image you want to add:";
    }
    var selection = signedGetSelect(id);
    if (selection.length > 0) {
        var text = selection;
    } else {
        var text = prompt(enterImgUrlPhrase, "");
    }
    var domobj = signedGetElementById(id);
    if ( text != null && text != "" ) {
        if (enterImgPosPhrase == null) {
            enterImgPosPhrase = "Now, enter the position of the image.";
        }
        if (imgPosRorLPhrase == null) {
            imgPosRorLPhrase = "'R' or 'r' for right, 'L' or 'l' for left, or leave it blank.";
        }
        if (errorImgPosPhrase == null) {
            errorImgPosPhrase = "ERROR! Enter the position of the image:";
        }
        var text2 = prompt(enterImgPosPhrase + "\n" + imgPosRorLPhrase, "");
        while ( ( text2 != "" ) && ( text2 != "r" ) && ( text2 != "R" ) && ( text2 != "l" ) && ( text2 != "L" ) && ( text2 != null ) ) {
            text2 = prompt(errorImgPosPhrase + "\n" + imgPosRorLPhrase,"");
        }
        if ( text2 == "l" || text2 == "L" ) {
            text2 = " align=left";
        } else if ( text2 == "r" || text2 == "R" ) {
            text2 = " align=right";
        } else {
            text2 = "";
        }

        var text3 = prompt(enterImgWidthPhrase, "300");
        if ( text3.length>0 ) {
            text3 = " width="+text3;
        }else {
            text3 = "";
        }

        var result = "[img" + text2 + text3 + "]" + text + "[/img]";
        signedInsertText(domobj, result);
    }
    domobj.focus();
}

function signedCodeEmail(id, enterEmailPhrase)
{
    if (enterEmailPhrase == null) {
        enterEmailPhrase = "Enter the email address you want to add:";
    }
    var selection = signedGetSelect(id);
    if (selection.length > 0) {
        var text = selection;
    }else {
        var text = prompt(enterEmailPhrase, "");
    }
    var domobj = signedGetElementById(id);
    if ( text != null && text != "" ) {
        var result = "[email]" + text + "[/email]";
        signedInsertText(domobj, result);
    }
    domobj.focus();
}

function signedCodeQuote(id, enterQuotePhrase)
{
    if (enterQuotePhrase == null) {
        enterQuotePhrase = "Enter the text that you want to be quoted:";
    }
    var selection = signedGetSelect(id);
    if (selection.length > 0) {
        var text = selection;
    } else {
        var text = prompt(enterQuotePhrase, "");
    }
    var domobj = signedGetElementById(id);
    if ( text != null && text != "" ) {
        var pos = text.indexOf(unescape('%00'));
        if (0 < pos) {
            text = text.substr(0,pos);
        }
        var result = "[quote]" + text + "[/quote]";
        signedInsertText(domobj, result);
    }
    domobj.focus();
}

function signedCodeCode(id, enterCodePhrase)
{
    if (enterCodePhrase == null) {
        enterCodePhrase = "Enter the codes that you want to add.";
    }
    var selection = signedGetSelect(id);
    if (selection.length > 0) {
        var text = selection;
    } else {
        var text = prompt(enterCodePhrase, "");
    }
    var domobj = signedGetElementById(id);
    if ( text != null && text != "" ) {
        var result = "[code]" + text + "[/code]";
        signedInsertText(domobj, result);
    }
    domobj.focus();
}

function signedCodeText(id, hiddentext, enterTextboxPhrase)
{
    var textareaDom = signedGetElementById(id);
    var textDom = signedGetElementById(id + "Addtext");
    var fontDom = signedGetElementById(id + "Font");
    var colorDom = signedGetElementById(id + "Color");
    var sizeDom = signedGetElementById(id + "Size");
    var signedHiddenTextDomStyle = signedGetElementById(hiddentext).style;
    var selection = signedGetSelect(id);
    if (selection.length > 0) {
        var textDomValue = selection;
    } else {
        var textDomValue = textDom.value;
    }
    var fontDomValue = fontDom.options[fontDom.options.selectedIndex].value;
    var colorDomValue = colorDom.options[colorDom.options.selectedIndex].value;
    var sizeDomValue = sizeDom.options[sizeDom.options.selectedIndex].value;
    if ( textDomValue == "" ) {
        if (enterTextboxPhrase == null) {
            enterTextboxPhrase = "Please input text into the textbox.";
        }
        alert(enterTextboxPhrase);
        textDom.focus();
    } else {
        if ( fontDomValue != "FONT") {
            textDomValue = "[font=" + fontDomValue + "]" + textDomValue + "[/font]";
            fontDom.options[0].selected = true;
        }
        if ( colorDomValue != "COLOR") {
            textDomValue = "[color=" + colorDomValue + "]" + textDomValue + "[/color]";
            colorDom.options[0].selected = true;
        }
        if ( sizeDomValue != "SIZE") {
            textDomValue = "[size=" + sizeDomValue + "]" + textDomValue + "[/size]";
            sizeDom.options[0].selected = true;
        }
        if (signedHiddenTextDomStyle.fontWeight == "bold" || signedHiddenTextDomStyle.fontWeight == "700") {
            textDomValue = "[b]" + textDomValue + "[/b]";
            signedHiddenTextDomStyle.fontWeight = "normal";
        }
        if (signedHiddenTextDomStyle.fontStyle == "italic") {
            textDomValue = "[i]" + textDomValue + "[/i]";
            signedHiddenTextDomStyle.fontStyle = "normal";
        }
        if (signedHiddenTextDomStyle.textDecoration == "underline") {
            textDomValue = "[u]" + textDomValue + "[/u]";
            signedHiddenTextDomStyle.textDecoration = "none";
        }
        if (signedHiddenTextDomStyle.textDecoration == "line-through") {
            textDomValue = "[d]" + textDomValue + "[/d]";
            signedHiddenTextDomStyle.textDecoration = "none";
        }
        if (signedHiddenTextDomStyle.textalign == "center") {
            textDomValue = "[center]" + textDomValue + "[/center]";
            signedHiddenTextDomStyle.textalign = "none";
        }
        if (signedHiddenTextDomStyle.textalign == "left") {
            textDomValue = "[left]" + textDomValue + "[/left]";
            signedHiddenTextDomStyle.textalign = "none";
        }
        if (signedHiddenTextDomStyle.textalign == "right") {
            textDomValue = "[right]" + textDomValue + "[/right]";
            signedHiddenTextDomStyle.textalign = "none";
        }

        signedInsertText(textareaDom, textDomValue);
        textDom.value = "";
        signedHiddenTextDomStyle.color = "#000000";
        signedHiddenTextDomStyle.fontFamily = "";
        signedHiddenTextDomStyle.fontSize = "12px";
        signedHiddenTextDomStyle.visibility = "hidden";
        textareaDom.focus();
    }
}

function signedGetSelect(id)
{
    if (window.getSelection) {
        ele = document.getElementById(id);
        var selection = ele.value.substring(
            ele.selectionStart, ele.selectionEnd
        );
    } else if (document.getSelection) {
        var selection = document.getSelection();
    } else if (document.selection) {
        var selection = document.selection.createRange().text;
    } else {
        var selection = null;
    }
    return selection;
}


function signedSetElementAttribute(key, val, id, eid)
{
    var text = signedGetSelect(id);
    if (text.length <= 0) {
        setVisible("signedHiddenText");
        eval("setElement" + key.substr(0,1).toUpperCase() + key.substr(1,key.length) + "(eid, val)");
        return;
    }
    var domobj = signedGetElementById(id);
    signedInsertText(domobj, "[" + key + "=" + val + "]" + text + "[/" + key + "]");
    domobj.focus();
}

function makeBold(id)
{
    var eleStyle = signedGetElementById(id).style;
    if (eleStyle.fontWeight != "bold" && eleStyle.fontWeight != "700") {
        eleStyle.fontWeight = "bold";
    } else {
        eleStyle.fontWeight = "normal";
    }
}

function makeItalic(id)
{
    var eleStyle = signedGetElementById(id).style;
    if (eleStyle.fontStyle != "italic") {
        eleStyle.fontStyle = "italic";
    } else {
        eleStyle.fontStyle = "normal";
    }
}

function makeUnderline(id)
{
    var eleStyle = signedGetElementById(id).style;
    if (eleStyle.textDecoration != "underline") {
        eleStyle.textDecoration = "underline";
    } else {
        eleStyle.textDecoration = "none";
    }
}

function makeLineThrough(id)
{
    var eleStyle = signedGetElementById(id).style;
    if (eleStyle.textDecoration != "line-through") {
        eleStyle.textDecoration = "line-through";
    } else {
        eleStyle.textDecoration = "none";
    }
}

function signedMakeStyle(id, eid, val, func)
{
    var text = signedGetSelect(id);
    if (text.length <= 0 && func.length > 0 && eid.length > 0) {
        setVisible(eid);
        eval(func + "(eid)");
        return;
    }
    var domobj = signedGetElementById(id);
    signedInsertText(domobj, "[" + val + "]" + text + "[/" + val + "]");
    domobj.focus();
}

function signedMakeBold(eid, id)
{
    signedMakeStyle(id, eid, "b", "makeBold");
}

function signedMakeItalic(eid, id)
{
    signedMakeStyle(id, eid, "i", "makeItalic");
}

function signedMakeUnderline(eid, id)
{
    signedMakeStyle(id, eid, "u", "makeUnderline");
}

function signedMakeLineThrough(eid, id)
{
    signedMakeStyle(id, eid, "d", "makeLineThrough");
}

function signedMakeCenter(eid, id)
{
    signedMakeStyle(id, eid, "center", "");
}

function signedMakeLeft(eid, id)
{
    signedMakeStyle(id, eid, "left", "");
}

function signedMakeRight(eid, id)
{
    signedMakeStyle(id, eid, "right", "");
}

// very rough calculation on text length
function signedCheckLength(id, maxlength, currentLengthPhrase, maxLengthPhrase)
{
    var mb_len_extra = 2;
    var domobj = signedGetElementById(id);
    var len = domobj.value.length;

    if (len > 50 * 1024) {
        len_current = " > 50K";
    } else if(len > 30 * 1024) {
        len_current = " 30-50K";
    } else if(len > 10 * 1024) {
        len_current = " 10-30K";
    } else if(len > 5 * 1024) {
        len_current = " 5-10K";
    } else if(len > 1 * 1024) {
        len_current = " 1-5K";
    } else {
        len_current = len;
        for (var n = 0; n < len; n++) {
            if (domobj.value.charAt(n) > '~') {
                len_current += mb_len_extra;
            }
        }

        len_current = len_current + " bytes";
    }

    var string = currentLengthPhrase.replace(/\%s/, len_current);
    if (maxlength > 0) string += ' [' + maxLengthPhrase + maxlength + ']';
    alert(string);
}


function signedValidate(subjectId, textareaId, submitId, plzCompletePhrase, msgTooLongPhrase, allowedCharPhrase, currCharPhrase)
{
    var maxchars = 65535;
    var subjectDom = signedGetElementById(subjectId);
    var textareaDom = signedGetElementById(textareaId);
    var submitDom = signedGetElementById(submitId);
    if (textareaDom.value == "" || subjectDom.value == "") {
        if (plzCompletePhrase == null) {
            plzCompletePhrase = "Please complete the subject and message fields.";
        }
        alert(plzCompletePhrase);
        return false;
    }
    if (maxchars != 0) {
        if (textareaDom.value.length > maxchars) {
            if (msgTooLongPhrase == null) {
                msgTooLongPhrase = "Your message is too long.";
            }
            if (allowedCharPhrase == null) {
                allowedCharPhrase = "Allowed max chars length: ";
            }
            if (currCharPhrase == null) {
                currCharPhrase = "Current chars length: ";
            }
            alert(msgTooLongPhrase + "\n\n" + allowedCharPhrase + maxchars + "\n" + currCharPhrase + textareaDom.value.length + "");
            textareaDom.focus();
            return false;
        } else {
            submitDom.disabled = true;
            return true;
        }
    } else {
        submitDom.disabled = true;
        return true;
    }
}

// AJAX code for preview
var form_area_id;
var http_request = false;
function makeRequest(area_id, url, arg, method)
{
    http_request = false;
    form_area_id = area_id;

    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();
        if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
        }
    } else if (window.ActiveXObject) { // IE
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }

    if (!http_request) {
        alert('Giving up :( Cannot create an XMLHTTP instance');
        return false;
    }
    if (!method || method != 'POST') {
        method = 'GET';
    } else {
        method = 'POST';

    }

    http_request.onreadystatechange = alertContents;
    http_request.open(method, url, true);
    if (method == 'POST') {
        //Send the proper header information along with the request
        http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http_request.setRequestHeader("Content-length", arg.length);
        http_request.setRequestHeader("Connection", "close");
    }
    http_request.send(arg);
}

function alertContents()
{
    if (http_request.readyState == 4) {
        if (http_request.status == 200) {
            document.getElementById(form_area_id + '_hidden_data').innerHTML = http_request.responseText;
        } else {
            alert(" Server Not Responding ... Please Try later ");
        }
    }
}


function form_checkserver(area_id)
{
    if(!xdh_triggered[area_id]) {
        alert(" Ohh.. Server Not Responding ... Please Try later ");
        signedGetElementById(area_id + '_preview_button').disabled = false;
        document.getElementById(area_id + '_hidden_data').innerHTML = "";
        xdh_triggered[area_id] = 1;
    }
}

function form_instantPreview(signedUrl, area_id, imgurl, doHtml, token)
{
    var imgUrl = signedUrl + '/images/form';
    var data = escape(signedGetElementById(area_id).value);

    var url_request = signedUrl + "/include/formdhtmltextarea_preview.php";//?text=" + data;
    var args =  "text=" + data;

    if (doHtml) {
        args += '&html=' + doHtml;
        //url_request += '&html=' + doHtml;
    }
    args += '&token=' + token;
    makeRequest(area_id, url_request, args, 'POST');  // - Made ajax Hidden
}