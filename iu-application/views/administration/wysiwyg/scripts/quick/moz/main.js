var onloadOverrided = false;

function onload_new()
  { 
  onload_original();  
  setMozEdit();
  }
  
function onload_original()
  {
  }  

function setMozEdit(idIframe) 
  { 
    if ((idIframe != null) && (idIframe!="")) {
        try {document.getElementById(idIframe).contentDocument.designMode="on";} catch(e) {}
    } else {
        for (var i=0; i<oUtil.arrIframe.length; i++)
        {
        try {document.getElementById(oUtil.arrIframe[i]).contentDocument.designMode="on";} catch(e) {alert(e)}
        }
    }
  } 
  
function getHTMLBody(idIframe)
	{
    var oEditor=document.getElementById(idIframe).contentWindow;
    sHTML=oEditor.document.body.innerHTML;
    sHTML=String(sHTML).replace(/ contentEditable=true/g,"");
    sHTML = String(sHTML).replace(/\<PARAM NAME=\"Play\" VALUE=\"0\">/ig,"<PARAM NAME=\"Play\" VALUE=\"-1\">");
    return sHTML;
	}

/*Insert custon HTML function*/
function insertHTML(idIframe, sHTML)
  {
  var oEditor=document.getElementById(idIframe).contentWindow;
  var oSel=oEditor.getSelection(); 
  var range = oSel.getRangeAt(0);
    
  var docFrag = range.createContextualFragment(sHTML);
  range.collapse(true);
  var lastNode = docFrag.childNodes[docFrag.childNodes.length-1];
  range.insertNode(docFrag);
  try { oEditor.document.designMode="on"; } catch (e) {}
  if (lastNode.nodeType==Node.TEXT_NODE) 
    {
    range = oEditor.document.createRange();
    range.setStart(lastNode, lastNode.nodeValue.length);
    range.setEnd(lastNode, lastNode.nodeValue.length);
    oSel = oEditor.getSelection();
    oSel.removeAllRanges();
    oSel.addRange(range);
    }
  }

function doCmd(idIframe,sCmd,sOption)
	{
    var oEditor=document.getElementById(idIframe).contentWindow;
    oEditor.document.execCommand(sCmd,false,sOption);
    }
    
function toggleViewSource(chk, idIframe) {
    if (chk.checked) {
        //view souce
        viewSource(idIframe);
    } else {
        //wysiwyg mode
        applySource(idIframe);
    }
}

function viewSource(idIframe) {
    var oEditor=document.getElementById(idIframe).contentWindow;
    
    var sHTML="";
    sHTML = oEditor.document.body.innerHTML;
    sHTML = sHTML.replace(/>\s+</gi, "><"); //replace space between tag
    sHTML = sHTML.replace(/\r/gi, ""); //replace space between tag
    sHTML = sHTML.replace(/(<br>)\s+/gi, "$1"); //replace space between BR and text

    var docBody = oEditor.document.body;
    docBody.innerHTML = "";
    docBody.appendChild(oEditor.document.createTextNode(sHTML));
}

function applySource(idIframe) {
    var oEditor=document.getElementById(idIframe).contentWindow;
    
    var range = oEditor.document.body.ownerDocument.createRange();
    range.selectNodeContents(oEditor.document.body);
    oEditor.document.body.innerHTML = range.toString();
}
    