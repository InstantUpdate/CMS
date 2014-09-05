var oUtil = new EditorUtil();

function EditorUtil() {
    this.obj = null;
    this.oEditor = null;
    this.arrEditor = [];

    var oScripts=document.getElementsByTagName("script");
    for(var i=0;i<oScripts.length;i++)
      {
        var sSrc=oScripts[i].src.toLowerCase();
        if(sSrc.indexOf("scripts/quick/ie/xhtmleditor.js")!=-1) this.scriptPath = oScripts[i].src.replace(/quick\/ie\/xhtmleditor.js/ig,"");
      }    
}

function InnovaEditor(oName) {
    this.oName = oName;
    this.height = "400px";
    this.width = "100%";
    this.heightAdjustment=-20;
    this.rangeBookmark = null;
    this.controlBookmark=null;    
    
    this.RENDER = RENDER;
    this.doCmd = edt_doCmd;
    this.getHTMLBody = edt_getHTMLBody;
    this.getXHTMLBody = edt_getXHTMLBody;
    this.insertHTML = edt_insertHTML;
    this.cleanDeprecated = edt_cleanDeprecated;
    this.cleanEmptySpan =  edt_cleanEmptySpan;
    this.cleanFonts = edt_cleanFonts;
    this.cleanTags = edt_cleanTags; 
    this.replaceTags = edt_replaceTags;
    this.toggleViewSource = edt_toggleViewSource;
    this.viewSource = edt_viewSource;
    this.applySource = edt_applySource;
    this.encodeIO = false;
    this.init = function(){return true;};
    
    this.bookmarkSelection = function() {
    
      var oEditor=eval("idContent"+this.oName);
      var oSel=oEditor.document.selection;
      var oRange=oSel.createRange();

      if (oSel.type == "None" || oSel.type == "Text") {
        this.rangeBookmark = oRange;
        this.controlBookmark=null;
      } else {
        this.controlBookmark = oRange.item(0);
        this.rangeBookmark=null;
      }    
    };

    this.setFocus=function() {
    
      var oEditor=eval("idContent"+this.oName);
      oEditor.focus();

        try {
          if(this.rangeBookmark!=null) {

            var oSel=oEditor.document.selection;
            var oRange = oSel.createRange()
            var bmRange = this.rangeBookmark;

            if(bmRange.parentElement()) {
              oRange.moveToElementText(bmRange.parentElement());
              oRange.setEndPoint("StarttoStart", bmRange);
              oRange.setEndPoint("EndToEnd", bmRange);
              oRange.select();
            }

          } else 
          if(this.controlBookmark!=null) {
            var oSel = oEditor.document.body.createControlRange();
            oSel.add(this.controlBookmark); oSel.select()
          }

        } catch(e) {}    
    };
    
    this.adjustHeight = function() {
      if(document.compatMode && document.compatMode!="BackCompat") {
        if(String(this.height).indexOf("%") == -1) {
          var eh = parseInt(this.height, 10);
          eh += parseInt(this.heightAdjustment, 10);
          this.height = eh + "px";
          var edtArea = document.getElementById("idArea" + oName);
          edtArea.style.height=this.height;
        }
      }
    }
}

function RENDER() {
}

function edt_doCmd(sCmd,sOption)
  {
  var oEditor=eval("idContent"+this.oName);
  var oSel=oEditor.document.selection.createRange();
  var sType=oEditor.document.selection.type;
  var oTarget=(sType=="None"?oEditor.document:oSel);
  oTarget.execCommand(sCmd,false,sOption);
  }

function edt_getHTMLBody()
  {
  var oEditor=eval("idContent"+this.oName);

  sHTML=oEditor.document.body.innerHTML;
  sHTML=String(sHTML).replace(/\<PARAM NAME=\"Play\" VALUE=\"0\">/ig,"<PARAM NAME=\"Play\" VALUE=\"-1\">");
  if(this.encodeIO)sHTML = encodeHTMLCode(sHTML);
  return sHTML;
  }

function edt_getXHTMLBody()
  {
  var sHTML = "";
  if (document.getElementById("chkViewSource"+this.oName).checked) 
      {   
          var oEditor=eval("idContent"+this.oName);
          sHTML = oEditor.document.body.innerText
      } else {
          var oEditor=eval("idContent"+this.oName);
          this.cleanDeprecated();
          sHTML = recur(oEditor.document.body,"");
      }
  
  if(this.encodeIO)sHTML = encodeHTMLCode(sHTML);
  return sHTML;
    
  }

/*Insert custom HTML function*/
function edt_insertHTML(sHTML)
  {
  var oEditor=eval("idContent"+this.oName);
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
function edt_cleanDeprecated()
  {
    var oEditor=eval("idContent"+this.oName);

  var elements;
  
  elements=oEditor.document.body.getElementsByTagName("STRIKE");
  this.cleanTags(elements,"line-through");
  elements=oEditor.document.body.getElementsByTagName("S");
  this.cleanTags(elements,"line-through");
  
  elements=oEditor.document.body.getElementsByTagName("U");
  this.cleanTags(elements,"underline");

  this.replaceTags("DIR","DIV");
  this.replaceTags("MENU","DIV"); 
  this.replaceTags("CENTER","DIV");
  this.replaceTags("XMP","PRE");
  this.replaceTags("BASEFONT","SPAN");//will be removed by cleanEmptySpan()
  
  elements=oEditor.document.body.getElementsByTagName("APPLET");
  var count=elements.length;
  while(count>0) 
    {
    f=elements[0];
    f.removeNode(false);   
    count--;
    }
  
  this.cleanFonts();
  this.cleanEmptySpan();

  return true;
  }
function edt_cleanEmptySpan()//WARNING: blm bisa remove span yg bertumpuk dgn style sama,dst.
  {
  var bReturn=false;
  var oEditor=eval("idContent"+this.oName);
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
  
function edt_cleanFonts()
  {
  var oEditor=eval("idContent"+this.oName);
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
  
function edt_cleanTags(elements,sVal)//WARNING: Dgn asumsi underline & linethrough tidak bertumpuk
  {
  var oEditor=eval("idContent"+this.oName);
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
function edt_replaceTags(sFrom,sTo)
  {
  var oEditor=eval("idContent"+this.oName);
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
  if( tag=="A"||tag=="B"||tag=="CITE"||tag=="CODE"||tag=="EM"||
    tag=="FONT"||tag=="I"||tag=="SMALL"||tag=="STRIKE"||tag=="BIG"||
    tag=="STRONG"||tag=="SUB"||tag=="SUP"||tag=="U"||tag=="SAMP"||
    tag=="S"||tag=="VAR"||tag=="BASEFONT"||tag=="KBD"||tag=="TT")
    arrReturn=["","",""];

  if( tag=="TEXTAREA"||tag=="TABLE"||tag=="THEAD"||tag=="TBODY"||
    tag=="TR"||tag=="OL"||tag=="UL"||tag=="DIR"||tag=="MENU"||
    tag=="FORM"||tag=="SELECT"||tag=="MAP"||tag=="DL"||tag=="HEAD"||
    tag=="BODY"||tag=="HTML")
    arrReturn=["\n","","\n"];

  if( tag=="STYLE"||tag=="SCRIPT")
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
  var UA = navigator.userAgent.toLowerCase();
  var isIE9 = (UA.indexOf('msie 9.0') >= 0) ? true : false;

  var sHTML="";
  for(var i=0;i<oEl.childNodes.length;i++)
    {
    var oNode=oEl.childNodes[i];
    
    if(oNode.parentNode!=oEl)continue; //add this line
    
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
        //s=oNode.outerHTML;
        s=getOuterHTML(oNode);

        s=s.replace(/\"[^\"]*\"/ig,function(x){
            x=x.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&apos;").replace(/\s+/ig,"#_#").replace(/&amp;amp;/gi,"&amp;");
            return x});
        s=s.replace(/<([^ >]*)/ig,function(x){return x.toLowerCase()});   
        s=s.replace(/ ([^=]+)=([^"' >]+)/ig," $1=\"$2\"");//new
        s=s.replace(/ ([^=]+)=/ig,function(x){return x.toLowerCase()});
        s=s.replace(/#_#/ig," ");

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

        if(sTagName=="BODY") {
          var ht = oNode.outerHTML;
          if(isIE9) {
            ht = getOuterHTML(oNode);
          }
          var ht = getOuterHTML(oNode);
          s=ht.substring(0, ht.indexOf(">")+1);
        } else {
          var oNode2=oNode.cloneNode();
          if (oNode.checked) oNode2.checked=oNode.checked;
          if (oNode.selected) oNode2.selected=oNode.selected;
          s=oNode2.outerHTML.replace(/<\/[^>]*>/,"");
          if(isIE9) {
            s=getOuterHTML(oNode2).replace(/<\/[^>]*>/,"");
          }
        }

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
        s=s.replace(/(<hr[^>]*)(noshade=""|noshade )/ig,"$1noshade=\"noshade\" ");
        s=s.replace(/(<input[^>]*)(checked=""|checked )/ig,"$1checked=\"checked\" ");
        s=s.replace(/(<select[^>]*)(multiple=""|multiple )/ig,"$1multiple=\"multiple\" ");
        s=s.replace(/(<option[^>]*)(selected=""|selected )/ig,"$1selected=\"true\" ");
        s=s.replace(/(<input[^>]*)(readonly=""|readonly )/ig,"$1readonly=\"readonly\" ");
        s=s.replace(/(<input[^>]*)(disabled=""|disabled )/ig,"$1disabled=\"disabled\" ");
        s=s.replace(/(<td[^>]*)(nowrap=""|nowrap )/ig,"$1nowrap=\"nowrap\" ");
        s=s.replace(/(<td[^>]*)(nowrap=""\>|nowrap\>)/ig,"$1nowrap=\"nowrap\"\>");

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
          if (sTagName == "STYLE" || sTagName=="SCRIPT")
            {
            //do nothing
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
          //sHTML+="</" + sCloseTag.toLowerCase() + ">";//spy bisa <a:b>
          if (sCloseTag.indexOf(":") >= 0)  //deteksi jika tag tersebut adalah custom tag.
            {
            sHTML+="</" + sCloseTag.toLowerCase() + ">";//spy bisa <a:b>
            } 
          else 
            {
            sHTML+="</" + sTagName.toLowerCase() + ">";
            }
          }
        }
      }
    else if(oNode.nodeType==3)//text
      {
      sHTML+= fixVal(oNode.nodeValue).replace(/^[\t\r\n\v\f]*/, "").replace(/[\t\r\n\v\f]*$/, "");
      }
    else if(oNode.nodeType==8)
      {
        var sTmp=oNode.nodeValue;
        sTmp = sTmp.replace(/^\s+/,"").replace(/\s+$/,"");
        var sT="";
        sHTML+="\n" +
          sT + "<!--\n"+
          sT + sTmp + "\n" +
          sT + "-->\n"+sT;
      }
    else
      {
      ;//Not Processed
      }
    }
  return sHTML;
  };
  
function edt_toggleViewSource(chk) {
    if (chk.checked) {
        //view souce
        this.viewSource();
    } else {
        //wysiwyg mode
        this.applySource();
    }
}

function edt_viewSource() {
    var oEditor=eval("idContent" + this.oName);
    
  this.cleanDeprecated();
  var sHTML=recur(oEditor.document.body,"");

    var docBody = oEditor.document.body;
    docBody.innerHTML = "";
    docBody.innerText = sHTML.replace(/\n+/, "");
}

function edt_applySource() {
    var oEditor=eval("idContent" + this.oName);
    
    var s = oEditor.document.documentElement.outerHTML;
    s = s.replace(/<body/gi, "<body");
    s = s.replace(/<\/body>/gi, "</body>");
  
    var arrTmp = s.split("<body")
    var beforeBody = arrTmp[0] + "<body" + arrTmp[1].substring(0, arrTmp[1].indexOf(">")+1);
    var afterBody = s.substr(s.indexOf("</body>"));
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

function encodeHTMLCode(sHTML) {
  return sHTML.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
}

function getOuterHTML(node) 
  {
    var sHTML = "";
    
    switch (node.nodeType) 
    {
        case 1:
            sHTML = "<" + node.nodeName;
            
            var tagVal ="";
            for (var atr=0; atr < node.attributes.length; atr++) 
            {       
                if (node.attributes[atr].nodeName.substr(0,4) == "_moz" ) continue;
                if (node.attributes[atr].nodeValue.substr(0,4) == "_moz" ) continue;//yus                
                if (node.nodeName=='TEXTAREA' && node.attributes[atr].nodeName.toLowerCase()=='value') 
                {
                    tagVal = node.attributes[atr].nodeValue;
                } 
                else 
                {
                    sHTML += ' ' + node.attributes[atr].nodeName + '="' + node.attributes[atr].nodeValue.replace(/"/gi, "'") + '"';
                } 
            }
            sHTML += '>'; 
            if(node.nodeName=='TEXTAREA') {
                sHTML += tagVal;
            } else if(node.nodeName=='OBJECT'){
                var ch;
                for(var i=0; i<node.childNodes.length;i++) {
                  ch = node.childNodes[i]; 
                  if(ch.nodeType==1) {
                    if(ch.tagName=="PARAM") {
                      sHTML += "<param name=\""+ch.name+"\" value=\""+ ch.value.replace(/"/gi, "'") +"\"/>\n";
                    } else if(ch.tagName=="EMBED") {
                      sHTML += getOuterHTML(ch);
                    }
                  }
                }
            } else {
                sHTML += node.innerHTML
            }
            sHTML += "</"+node.nodeName+">";
            break;
        case 8: //comment
            sHTML = "<!"+"--"+node.nodeValue+ "--"+">"; break;
        case 3: //text
            sHTML = node.nodeValue; break;
    }
    
    return sHTML;
  };

function ISWindow(id) {
  
  var ua = navigator.userAgent.toUpperCase();
  var isIE =(ua.indexOf('MSIE') >= 0) ? true : false,
  isIE7=(ua.indexOf("MSIE 7.0") >=0),
  isIE8=(ua.indexOf("MSIE 8.0") >=0),
  isIE6=(!isIE7 && !isIE8 && isIE),
  IEBackCompat = (isIE && document.compatMode=="BackCompat");
  
  var me=this;
  
  this.id=id;
  this.opts=null;
  this.rt={};
  this.iconPath="icons/";
  
  ISWindow.objs[id] = this;
    
  this.show=function(opt) {
  
    if(!document.getElementById(this.id)) {
      //render
      var e = document.createElement("div");
      e.id = "cnt$"+this.id;
      e.innerHTML = this.render(opt.url);
      document.body.insertBefore(e, document.body.childNodes[0]);
    }
  
    if(!this.rt.win) {
      this.rt.win = document.getElementById(this.id);
      this.rt.frm = document.getElementById("frm$"+this.id);
      this.rt.ttl = document.getElementById("ttl$"+this.id);
    }
    
    if(opt.overlay==true) this.showOverlay();
    
    this.setSize(opt.width, opt.height, opt.center);
    ISWindow.zIndex+=2;
    this.rt.win.style.zIndex = ISWindow.zIndex;
    this.rt.win.style.display="block";
    
    var fn = 
        
        function() {
          me.rt.ttl.innerHTML = me.rt.frm.contentWindow.document.title;
          me.rt.frm.contentWindow.openerWin = opt.openerWin ? opt.openerWin : window;
          me.rt.frm.contentWindow.opener = opt.openerWin ? opt.openerWin : window;
          me.rt.frm.contentWindow.options = opt.options?opt.options:{};
          me.rt.frm.contentWindow.close=function() {
            me.close();
          };          
          if (typeof(me.rt.frm.contentWindow.bodyOnLoad) != "undefined") me.rt.frm.contentWindow.bodyOnLoad();
        } ;
    
    if(this.rt.frm.attachEvent) this.rt.frm.attachEvent("onload", fn);
    if(this.rt.frm.addEventListener) this.rt.frm.addEventListener("load", fn, true);
    
    
    setTimeout(function() {me.rt.frm.src = opt.url;}, 0);
    
  };
  
  this.close = function() {
    var d = document.getElementById("cnt$"+this.id);    
    if(d) {
      if(this.rt.frm.contentWindow.bodyOnUnload) this.rt.frm.contentWindow.bodyOnUnload();
      d.parentNode.removeChild(d);
    }
    this.hideOverlay();
  };
  
  this.hide=function() {
    if(!this.rt.win) {
      this.rt.win = document.getElementById(this.id);
    }
    this.rt.win.style.display="none";
  };
  
  this.showOverlay=function() {
    
    var ov;
    if(!document.getElementById("ovr$"+this.id)) {
      ov = document.createElement("div");
      ov.id = "ovr$"+this.id;
      ov.style.display="none";
      ov.style.position=(isIE6 || IEBackCompat ? "absolute" : "fixed");
      ov.style.backgroundColor="#ffffff";
      ov.style.filter = "alpha(opacity=35)";
      ov.style.mozOpacity = "0.4";
      ov.style.opacity = "0.4";
      ov.style.Opacity = "0.4";
      document.body.insertBefore(ov, document.body.childNodes[0]);
    }
    
    var cl = ISWindow.clientSize();
      
    if(isIE6 || IEBackCompat) {
        
      var db=document.body, de=document.documentElement, w, h;
      w=Math.min(
            Math.max(db.scrollWidth, de.scrollWidth),
            Math.max(db.offsetWidth, de.offsetWidth)
          );

      var mf=((de.scrollHeight<de.offsetHeight) || (db.scrollHeight<db.offsetHeight))?Math.min:Math.max;  
      h=mf(
            Math.max(db.scrollHeight, de.scrollHeight),
            Math.max(db.offsetHeight, de.offsetHeight)
          );

      cl.w = Math.max(cl.w, w);
      cl.h = Math.max(cl.h, h);
      
      ov.style.position="absolute";
    }
      
    ov.style.width = cl.w+"px";
    ov.style.height = cl.h+"px";
    ov.style.top="0px";
    ov.style.left="0px";
    ov.style.zIndex = ISWindow.zIndex+1;
    ov.style.display="block";
    
  };
  
  this.hideOverlay=function() {
    var ov=document.getElementById("ovr$"+this.id);
    if(ov) ov.style.display="none";
  };
  
  this.setSize=function(w, h, center) {
    this.rt.win.style.width=w;
    this.rt.win.style.height=h;
    this.rt.frm.style.height=parseInt(h, 10)-30 + "px";
    if(center) {
      this.center();
    }
  };
  
  this.center=function() {
    
    var c=ISWindow.clientSize();
    var px=parseInt(this.rt.win.style.width, 10), py=parseInt(this.rt.win.style.height, 10);
    px=(isNaN(px)?0:(px>c.w?c.w:px));
    py=(isNaN(py)?0:(py>c.h?c.h:py));
    var p = {x:(c.w-px)/2, y:(c.h-py)/2};
    if(isIE6 || IEBackCompat) {
      p.x=p.x+(document.body.scrollLeft||document.documentElement.scrollLeft);
      p.y=p.y+(document.body.scrollTop||document.documentElement.scrollTop);
    }
    this.setPosition(p.x, p.y);
  
  };
  
  this.setPosition=function(x, y) {
    
    this.rt.win.style.top=y+"px";
    this.rt.win.style.left=x+"px";    
    
  };
  
  this.render=function(attr) {
    
    var s=[],j=0,ps=isIE6 || IEBackCompat ?"absolute":"fixed";
    s[j++] = "<div style='position:"+ps+";display:none;z-index:100000;background-color:#ffffff;filter:alpha(opacity=25);opacity:0.25;-moz-opacity:0.25;border:#999999 1px solid' id=\"dd$"+this.id+"\"></div>";
    s[j++] = "<div unselectable='on' id=\""+this.id+"\" style='position:"+ps+";z-index:100000;border:#d7d7d7 1px solid;'>";
    s[j++] = "  <div unselectable=\"on\" style=\"cursor:move;height:30px;background-image:url("+this.iconPath+"dialogbg.gif);\" onmousedown=\"ISWindow._ddMouseDown(event, '"+this.id+"');\"><span style=\"font-weight:bold;float:left;margin-top:7px;margin-left:11px;\" id=\"ttl$"+this.id+"\"></span><img src=\""+this.iconPath+"btnClose.gif\" onmousedown=\"event.cancelBubble=true;if(event.preventDefault) event.preventDefault();\" onclick=\"ISWindow.objs['" + this.id + "'].close();\" style='float:right;margin-top:5px;margin-right:5px;cursor:pointer' /></div>";
    s[j++] = "  <iframe id=\"frm$"+this.id+"\" style=\"width:100%;\" frameborder='no'></iframe>";
    s[j++] = "</div>";
    return s.join("");
  };
  
  ISWindow.clientSize=function() {
    return {w:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth, 
            h:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight};
  };
  
  ISWindow._ddMouseDown=function(ev, elId) {
    
    var d=document;    
    
    d.onmousemove=function(e) {ISWindow._startDrag(e?e:ev);}
    d.onmouseup=function(e) {ISWindow._endDrag(e?e:ev);}
    d.onselectstart=function() { return false;}
    d.onmousedown=function() { return false;}
    d.ondragstart=function() { return false;}
    
    ISWindow.trgElm = document.getElementById(elId);
    
    ISWindow.gstElm = document.getElementById("dd$"+elId);
    ISWindow.gstElm.style.top=ISWindow.trgElm.style.top;
    ISWindow.gstElm.style.left=ISWindow.trgElm.style.left;
    ISWindow.gstElm.style.width=ISWindow.trgElm.style.width;
    ISWindow.gstElm.style.height=ISWindow.trgElm.style.height;
    ISWindow.gstElm.style.display="block";
    
    ISWindow.posDif = {x:ev.clientX-parseInt(ISWindow.trgElm.style.left, 10),
                       y:ev.clientY-parseInt(ISWindow.trgElm.style.top, 10)};    
  };
  
  ISWindow._startDrag = function(ev) {
    ISWindow.gstElm.style.left=(ev.clientX-ISWindow.posDif.x)+"px";
    ISWindow.gstElm.style.top=(ev.clientY-ISWindow.posDif.y)+"px";
  };
  
  ISWindow._endDrag = function(ev) {
    
    ISWindow.gstElm.style.display="none";
    
    ISWindow.trgElm.style.top=ISWindow.gstElm.style.top;
    ISWindow.trgElm.style.left=ISWindow.gstElm.style.left;
    
    document.onmousemove=null;
    document.onmouseup=null;
    document.onmousedown=function() { return true;};
    document.onselectstart=function() { return true;};
    document.onselectstart=function() { return true;};
    
  };
  
};

ISWindow.objs={};
ISWindow.zIndex=2000;