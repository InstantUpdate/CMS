function doCmd(idIframe,sCmd,sOption)
	{
	var oEditor=eval(idIframe);
	var oSel=oEditor.document.selection.createRange();
	var sType=oEditor.document.selection.type;
	var oTarget=(sType=="None"?oEditor.document:oSel);
	oTarget.execCommand(sCmd,false,sOption);
	}

function getHTMLBody(idIframe)
	{
	var oEditor=eval(idIframe);

	sHTML=oEditor.document.body.innerHTML;
	sHTML=String(sHTML).replace(/\<PARAM NAME=\"Play\" VALUE=\"0\">/ig,"<PARAM NAME=\"Play\" VALUE=\"-1\">");
	return sHTML;
	}

function getXHTMLBody(idIframe)
	{
	var oEditor=eval(idIframe);
	cleanDeprecated(idIframe);
	return recur(oEditor.document.body,"");
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

/************************************
	CLEAN DEPRECATED TAGS; Used in loadHTML, getHTMLBody, getXHTMLBody 
*************************************/
function cleanDeprecated(idIframe)
	{
	var oEditor=eval(idIframe);

	var elements;

	//elements=oEditor.document.body.getElementsByTagName("STRONG");
	//cleanTags(idIframe,elements,"bold");
	//elements=oEditor.document.body.getElementsByTagName("B");
	//cleanTags(idIframe,elements,"bold");

	//elements=oEditor.document.body.getElementsByTagName("I");
	//cleanTags(idIframe,elements,"italic");
	//elements=oEditor.document.body.getElementsByTagName("EM");
	//cleanTags(idIframe,elements,"italic");
	
	elements=oEditor.document.body.getElementsByTagName("STRIKE");
	cleanTags(idIframe,elements,"line-through");
	elements=oEditor.document.body.getElementsByTagName("S");
	cleanTags(idIframe,elements,"line-through");
	
	elements=oEditor.document.body.getElementsByTagName("U");
	cleanTags(idIframe,elements,"underline");

	replaceTags(idIframe,"DIR","DIV");
	replaceTags(idIframe,"MENU","DIV");	
	replaceTags(idIframe,"CENTER","DIV");
	replaceTags(idIframe,"XMP","PRE");
	replaceTags(idIframe,"BASEFONT","SPAN");//will be removed by cleanEmptySpan()
	
	elements=oEditor.document.body.getElementsByTagName("APPLET");
	var count=elements.length;
	while(count>0) 
		{
		f=elements[0];
		f.removeNode(false);   
		count--;
		}
	
	cleanFonts(idIframe);
	cleanEmptySpan(idIframe);

	return true;
	}
function cleanEmptySpan(idIframe)//WARNING: blm bisa remove span yg bertumpuk dgn style sama,dst.
	{
	var bReturn=false;
	var oEditor=eval(idIframe);
	var allSpans=oEditor.document.getElementsByTagName("SPAN");
	if(allSpans.length==0)return false;

	var emptySpans=[];
	var reg = /<\s*SPAN\s*>/gi;
	for(var i=0;i<allSpans.length;i++)
		{
		if(allSpans[i].outerHTML.search(reg)==0)
			emptySpans[emptySpans.length]=allSpans[i];
		}
	var theSpan,theParent;
	for(var i=0;i<emptySpans.length;i++)
		{
		theSpan=emptySpans[i];
		theSpan.removeNode(false);
		bReturn=true;
		}
	return bReturn;
	}
function cleanFonts(idIframe)
	{
	var oEditor=eval(idIframe);
	var allFonts=oEditor.document.body.getElementsByTagName("FONT");
	if(allFonts.length==0)return false;

	var f;
	while(allFonts.length>0)
		{
		f=allFonts[0];
		if(f.hasChildNodes && f.childNodes.length==1 && f.childNodes[0].nodeType==1 && f.childNodes[0].nodeName=="SPAN") 
			{
			//if font containts only span child node
			copyAttribute(f.childNodes[0],f);
			f.removeNode(false);
			}
		else
			if(f.parentElement.nodeName=="SPAN" && f.parentElement.childNodes.length==1)
				{
				//font is the only child node of span.
				copyAttribute(f.parentElement,f);
				f.removeNode(false);
				}
			else
				{
				var newSpan=oEditor.document.createElement("SPAN");
				copyAttribute(newSpan,f);
				newSpan.innerHTML=f.innerHTML;
				f.replaceNode(newSpan);
				}
		}
	return true;
	}
function cleanTags(idIframe,elements,sVal)//WARNING: Dgn asumsi underline & linethrough tidak bertumpuk
	{
	var oEditor=eval(idIframe);
	var f;
	while(elements.length>0)
		{
		f=elements[0];
		if(f.hasChildNodes && f.childNodes.length==1 && f.childNodes[0].nodeType==1 && f.childNodes[0].nodeName=="SPAN") 
			{//if font containts only span child node
			if(sVal=="bold")f.childNodes[0].style.fontWeight="bold";
			if(sVal=="italic")f.childNodes[0].style.fontStyle="italic";
			if(sVal=="line-through")f.childNodes[0].style.textDecoration="line-through";
			if(sVal=="underline")f.childNodes[0].style.textDecoration="underline";	
			f.removeNode(false);
			}
		else
			if(f.parentElement.nodeName=="SPAN" && f.parentElement.childNodes.length==1)
				{//font is the only child node of span.
				if(sVal=="bold")f.parentElement.style.fontWeight="bold";
				if(sVal=="italic")f.parentElement.style.fontStyle="italic";
				if(sVal=="line-through")f.parentElement.style.textDecoration="line-through";
				if(sVal=="underline")f.parentElement.style.textDecoration="underline";	
				f.removeNode(false);
				}
			else
				{
				var newSpan=oEditor.document.createElement("SPAN");
				if(sVal=="bold")newSpan.style.fontWeight="bold";
				if(sVal=="italic")newSpan.style.fontStyle="italic";
				if(sVal=="line-through")newSpan.style.textDecoration="line-through";
				if(sVal=="underline")newSpan.style.textDecoration="underline";
				newSpan.innerHTML=f.innerHTML;
				f.replaceNode(newSpan);
				}
		}
	}
function replaceTags(idIframe,sFrom,sTo)
	{
	var oEditor=eval(idIframe);
	var elements=oEditor.document.getElementsByTagName(sFrom);

	var newSpan;
	var count=elements.length;
	while(count > 0) 
		{
		f=elements[0];
		newSpan=oEditor.document.createElement(sTo);
		newSpan.innerHTML=f.innerHTML;
		f.replaceNode(newSpan);          
		count--;
		}
	}
function copyAttribute(newSpan,f)
    {
    if((f.face!=null)&&(f.face!=""))newSpan.style.fontFamily=f.face;
    if((f.size!=null)&&(f.size!=""))
        {
        var nSize="";
        if(f.size==1)nSize="8pt";
        else if(f.size==2)nSize="10pt";
        else if(f.size==3)nSize="12pt";
        else if(f.size==4)nSize="14pt";
        else if(f.size==5)nSize="18pt";
        else if(f.size==6)nSize="24pt";
        else if(f.size>=7)nSize="36pt";
        else if(f.size<=-2||f.size=="0")nSize="8pt";
        else if(f.size=="-1")nSize="10pt";
        else if(f.size==0)nSize="12pt";
        else if(f.size=="+1")nSize="14pt";
        else if(f.size=="+2")nSize="18pt";
        else if(f.size=="+3")nSize="24pt";
        else if(f.size=="+4"||f.size=="+5"||f.size=="+6")nSize="36pt";
        else nSize="";
        if(nSize!="")newSpan.style.fontSize=nSize;
        }
    if((f.style.backgroundColor!=null)&&(f.style.backgroundColor!=""))newSpan.style.backgroundColor=f.style.backgroundColor;
    if((f.color!=null)&&(f.color!=""))newSpan.style.color=f.color;
    }	
function GetElement(oElement,sMatchTag)
	{
	while (oElement!=null&&oElement.tagName!=sMatchTag)
		{
		if(oElement.tagName=="BODY")return null;
		oElement=oElement.parentElement;
		}
	return oElement;
	}

/************************************
	HTML to XHTML
*************************************/
function lineBreak1(tag) //[0]<TAG>[1]text[2]</TAG>
	{
	arrReturn = ["\n","",""];
	if(	tag=="A"||tag=="B"||tag=="CITE"||tag=="CODE"||tag=="EM"||
		tag=="FONT"||tag=="I"||tag=="SMALL"||tag=="STRIKE"||tag=="BIG"||
		tag=="STRONG"||tag=="SUB"||tag=="SUP"||tag=="U"||tag=="SAMP"||
		tag=="S"||tag=="VAR"||tag=="BASEFONT"||tag=="KBD"||tag=="TT")
		arrReturn=["","",""];

	if(	tag=="TEXTAREA"||tag=="TABLE"||tag=="THEAD"||tag=="TBODY"||
		tag=="TR"||tag=="OL"||tag=="UL"||tag=="DIR"||tag=="MENU"||
		tag=="FORM"||tag=="SELECT"||tag=="MAP"||tag=="DL"||tag=="HEAD"||
		tag=="BODY"||tag=="HTML")
		arrReturn=["\n","","\n"];

	if(	tag=="STYLE"||tag=="SCRIPT")
		arrReturn=["\n","",""];

	if(tag=="BR"||tag=="HR")
		arrReturn=["","\n",""];

	return arrReturn;
	}
function fixAttr(s)
	{
	s = String(s).replace(/&/g, "&amp;");
	s = String(s).replace(/</g, "&lt;");
	s = String(s).replace(/"/g, "&quot;");
	return s;
	}
function fixVal(s)
	{
	s = String(s).replace(/&/g, "&amp;");
	s = String(s).replace(/</g, "&lt;");
	var x = escape(s);
	x = unescape(x.replace(/\%A0/gi, "-*REPL*-"));
	s = x.replace(/-\*REPL\*-/gi, "&nbsp;");
	return s;
	}
function recur(oEl,sTab)
	{
	var sHTML="";
	for(var i=0;i<oEl.childNodes.length;i++)
		{
		var oNode=oEl.childNodes(i);
		if(oNode.nodeType==1)//tag
			{			
			var sTagName = oNode.nodeName;

            var sCloseTag = oNode.outerHTML;
            if (sCloseTag.indexOf("<?xml:namespace") > -1) sCloseTag=sCloseTag.substr(sCloseTag.indexOf(">")+1);
            sCloseTag = sCloseTag.substring(1, sCloseTag.indexOf(">"));
            if (sCloseTag.indexOf(" ")>-1) sCloseTag=sCloseTag.substring(0, sCloseTag.indexOf(" "));

			var bDoNotProcess=false;
			if(sTagName.substring(0,1)=="/")
				{
				bDoNotProcess=true;//do not process
				}
			else
				{
				/*** tabs ***/
				var sT= sTab;
				
				sHTML+= lineBreak1(sTagName)[0];
				if(lineBreak1(sTagName)[0] !="") sHTML+= sT;//If new line, use base Tabs
				/************/
				}

			if(bDoNotProcess)
				{
				;//do not process
				}
			else if(sTagName=="OBJECT" || sTagName=="EMBED")
				{
				s=oNode.outerHTML;

				s=s.replace(/\"[^\"]*\"/ig,function(x){
						x=x.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&apos;").replace(/\s+/ig,"#_#");
						return x});
				s=s.replace(/<([^ >]*)/ig,function(x){return x.toLowerCase()});		
				s=s.replace(/ ([^=]+)=([^"' >]+)/ig," $1=\"$2\"");//new
				s=s.replace(/ ([^=]+)=/ig,function(x){return x.toLowerCase()});
				s=s.replace(/#_#/ig," ");

				s=s.replace(/<param([^>]*)>/ig,"\n<param$1 />").replace(/\/ \/>$/ig," \/>");//no closing tag

				if(sTagName=="EMBED")
					if(oNode.innerHTML=="")
						s=s.replace(/>$/ig," \/>").replace(/\/ \/>$/ig,"\/>");//no closing tag

				s=s.replace(/<param name=\"Play\" value=\"0\" \/>/,"<param name=\"Play\" value=\"-1\" \/>");

				sHTML+=s;
				}
			else if(sTagName=="TITLE")
				{
				sHTML+="<title>"+oNode.innerHTML+"</title>";
				}
			else
				{
				if(sTagName=="AREA")
					{
					var sCoords=oNode.coords;
					var sShape=oNode.shape;
					}

				var oNode2=oNode.cloneNode();
				if (oNode.checked) oNode2.checked=oNode.checked;
				s=oNode2.outerHTML.replace(/<\/[^>]*>/,"");
				s = s.replace(/(\r\n)+/,"");
				if(sTagName=="STYLE")
					{
					var arrTmp=s.match(/<[^>]*>/ig);
					s=arrTmp[0];
					}

				s=s.replace(/\"[^\"]*\"/ig,function(x){
						//x=x.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&apos;").replace(/\s+/ig,"#_#");
						//x=x.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\s+/ig,"#_#");
						x=x.replace(/&/g, "&amp;").replace(/&amp;amp;/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\s+/ig,"#_#");
						return x});
						//info ttg: .replace(/&amp;amp;/g, "&amp;")
						//ini karena '&' di (hanya) '&amp;' selalu di-replace lagi dgn &amp;.
						//tapi kalau &lt; , &gt; tdk (no problem) => default behaviour

				s=s.replace(/<([^ >]*)/ig,function(x){return x.toLowerCase()});		
				s=s.replace(/ ([^=]+)=([^" >]+)/ig," $1=\"$2\"");
				s=s.replace(/ ([^=]+)=/ig,function(x){return x.toLowerCase()});
				s=s.replace(/#_#/ig," ");

				//single attribute
				s=s.replace(/[<hr]?(noshade)/ig,"noshade=\"noshade\"");
				s=s.replace(/[<input]?(checked)/ig,"checked=\"checked\"");
				s=s.replace(/[<select]?(multiple)/ig,"multiple=\"multiple\"");
				s=s.replace(/[<option]?(selected)/ig,"selected=\"true\"");
				s=s.replace(/[<input]?(readonly)/ig,"readonly=\"readonly\"");
				s=s.replace(/[<input]?(disabled)/ig,"disabled=\"disabled\"");
				s=s.replace(/[<td]?(nowrap )/ig,"nowrap=\"nowrap\" ");
				s=s.replace(/[<td]?(nowrap\>)/ig,"nowrap=\"nowrap\"\>");

				s=s.replace(/ contenteditable=\"true\"/ig,"");

				if(sTagName=="AREA")
					{
					s=s.replace(/ coords=\"0,0,0,0\"/ig," coords=\""+sCoords+"\"");
					s=s.replace(/ shape=\"RECT\"/ig," shape=\""+sShape+"\"");
					}

				var bClosingTag=true;
				if(sTagName=="IMG"||sTagName=="BR"||
					sTagName=="AREA"||sTagName=="HR"||
					sTagName=="INPUT"||sTagName=="BASE"||
					sTagName=="LINK")//no closing tag
					{
					s=s.replace(/>$/ig," \/>").replace(/\/ \/>$/ig,"\/>");//no closing tag
					bClosingTag=false;	
					}

				sHTML+=s;

				/*** tabs ***/
				if(sTagName!="TEXTAREA")sHTML+= lineBreak1(sTagName)[1];
				if(sTagName!="TEXTAREA")if(lineBreak1(sTagName)[1] !="") sHTML+= sT;//If new line, use base Tabs
				/************/

				if(bClosingTag)
					{
					/*** CONTENT ***/
					s=oNode.outerHTML;					
					if(sTagName=="SCRIPT")
						{
						s = s.replace(/<script([^>]*)>[\n+\s+\t+]*/ig,"<script$1>");//clean spaces
						s = s.replace(/[\n+\s+\t+]*<\/script>/ig,"<\/script>");//clean spaces
						s = s.replace(/<script([^>]*)>\/\/<!\[CDATA\[/ig,"");
						s = s.replace(/\/\/\]\]><\/script>/ig,"");
						s = s.replace(/<script([^>]*)>/ig,"");
						s = s.replace(/<\/script>/ig,"");		
						s = s.replace(/^\s+/,'').replace(/\s+$/,'');						

						sHTML+="\n"+
							sT + "//<![CDATA[\n"+
							sT + s + "\n"+
							sT + "//]]>\n"+sT;
						}
					if(sTagName=="STYLE")
						{
						s = s.replace(/<style([^>]*)>[\n+\s+\t+]*/ig,"<style$1>");//clean spaces
						s = s.replace(/[\n+\s+\t+]*<\/style>/ig,"<\/style>");//clean spaces			
						s = s.replace(/<style([^>]*)><!--/ig,"");
						s = s.replace(/--><\/style>/ig,"");
						s = s.replace(/<style([^>]*)>/ig,"");
						s = s.replace(/<\/style>/ig,"");		
						s = s.replace(/^\s+/,"").replace(/\s+$/,"");					

						sHTML+="\n"+
							sT + "<!--\n"+
							sT + s + "\n"+
							sT + "-->\n"+sT;
						}
					if(sTagName=="DIV"||sTagName=="P")
						{
						if(oNode.innerHTML==""||oNode.innerHTML=="&nbsp;")
							{
							sHTML+="&nbsp;";
							}
						else sHTML+=recur(oNode,sT+"\t");
						}
					else
						{
						sHTML+=recur(oNode,sT+"\t");
						}

					/*** tabs ***/
					if(sTagName!="TEXTAREA")sHTML+=lineBreak1(sTagName)[2];
					if(sTagName!="TEXTAREA")if(lineBreak1(sTagName)[2] !="")sHTML+=sT;//If new line, use base Tabs
					/************/

					//sHTML+="</" + sTagName.toLowerCase() + ">";
					sHTML+="</" + sCloseTag.toLowerCase() + ">";//spy bisa <a:b>
					}
				}
			}
		else if(oNode.nodeType==3)//text
			{
			sHTML+= fixVal(oNode.nodeValue);
			}
		else if(oNode.nodeType==8)
			{
			if(oNode.outerHTML.substring(0,2)=="<"+"%")
				{//server side script
				sTmp=(oNode.outerHTML.substring(2));
				sTmp=sTmp.substring(0,sTmp.length-2);
				sTmp = sTmp.replace(/^\s+/,"").replace(/\s+$/,"");

				/*** tabs ***/
				var sT= sTab;
				/************/

				sHTML+="\n" +
					sT + "<%\n"+
					sT + sTmp + "\n" +
					sT + "%>\n"+sT;
				}
			else
				{//comments
				sTmp=oNode.nodeValue;
				sTmp = sTmp.replace(/^\s+/,"").replace(/\s+$/,"");

				sHTML+="\n" +
					sT + "<!--\n"+
					sT + sTmp + "\n" +
					sT + "-->\n"+sT;
				}
			}
		else
			{
			;//Not Processed
			}
		}
	return sHTML;
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
    
    var sHTML=getXHTMLBody(idIframe);

    var docBody = oEditor.document.body;
    docBody.innerHTML = "";
    docBody.innerText = sHTML.replace(/\n+/, "");
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