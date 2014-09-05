function getHTMLBody(idIframe)
	{
	var oEditor=eval(idIframe);

	sHTML=oEditor.document.body.innerHTML;
	sHTML=String(sHTML).replace(/\<PARAM NAME=\"Play\" VALUE=\"0\">/ig,"<PARAM NAME=\"Play\" VALUE=\"-1\">");
	return sHTML;
	}

/*Insert custom HTML function*/
function insertHTML(wndIframe, sHTML)
  {
  var oEditor=wndIframe;
  var oSel=oEditor.document.selection.createRange();
  
  var arrA = String(sHTML).match(/<A[^>]*>/ig);
  if(arrA)
    for(var i=0;i<arrA.length;i++)
      {
      sTmp = arrA[i].replace(/href=/,"href_iwe=");
      sHTML=String(sHTML).replace(arrA[i],sTmp);
      }

  var arrB = String(sHTML).match(/<IMG[^>]*>/ig);
  if(arrB)
    for(var i=0;i<arrB.length;i++)
      {
      sTmp = arrB[i].replace(/src=/,"src_iwe=");
      sHTML=String(sHTML).replace(arrB[i],sTmp);
      }

  if(oSel.parentElement)oSel.pasteHTML(sHTML);
  else oSel.item(0).outerHTML=sHTML;

  for(var i=0;i<oEditor.document.all.length;i++)
    {
    if(oEditor.document.all[i].getAttribute("href_iwe"))
      {
      oEditor.document.all[i].href=oEditor.document.all[i].getAttribute("href_iwe");
      oEditor.document.all[i].removeAttribute("href_iwe",0);
      }
    if(oEditor.document.all[i].getAttribute("src_iwe"))
      {
      oEditor.document.all[i].src=oEditor.document.all[i].getAttribute("src_iwe");
      oEditor.document.all[i].removeAttribute("src_iwe",0);
      }
    }
  }

function doCmd(idIframe,sCmd,sOption)
	{
	var oEditor=eval(idIframe);
	var oSel=oEditor.document.selection.createRange();
	var sType=oEditor.document.selection.type;
	var oTarget=(sType=="None"?oEditor.document:oSel);
	oTarget.execCommand(sCmd,false,sOption);
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
    var oEditor=eval(idIframe);
    var sHTML=oEditor.document.body.innerHTML;

    var docBody = oEditor.document.body;
    docBody.innerHTML = "";
    docBody.innerText = sHTML;
}


function applySource(idIframe) {
    var oEditor=eval(idIframe);
    
    var s = oEditor.document.documentElement.outerHTML;
    var arrTmp = s.split("<BODY")
    var beforeBody = arrTmp[0] + "<BODY" + arrTmp[1].substring(0, arrTmp[1].indexOf(">")+1);
    var afterBody = s.substr(s.indexOf("</BODY>"));
    var body = oEditor.document.body.innerText
    //alert(beforeBody + oEditor.document.body.innerText + afterBody);
    
    var oDoc = oEditor.document.open("text/html", "replace");
    oDoc.write(beforeBody + body + afterBody);
    oDoc.close();
    oEditor.document.body.contentEditable=true
    oEditor.document.execCommand("2D-Position", true, true);
    oEditor.document.execCommand("MultipleSelection", true, true);
    oEditor.document.execCommand("LiveResize", true, true);
    oEditor.document.body.onmouseup=function() { oUtil.oEditor = oEditor } ;
}	