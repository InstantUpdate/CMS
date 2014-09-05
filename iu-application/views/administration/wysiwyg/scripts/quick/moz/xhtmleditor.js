var oUtil = new EditorUtil();

var onloadOverrided = false;

function onload_new()
  { 
  onload_original();  
  setMozEdit();
  }
  
function onload_original()
  {
  }  

function setMozEdit(oName) 
  { 
    if ((oName != null) && (oName!="")) {
        try {
            var d = document.getElementById("idContent" + oName).contentDocument;
            d.designMode="on";
            if(typeof(d.contentEditable)!="undefined") d.contentEditable=true;
        } catch(e) {}
    } else {
        for (var i=0; i<oUtil.arrEditor.length; i++)
        {
            try {
                var d = document.getElementById("idContent" + oUtil.arrEditor[i]).contentDocument;
                d.designMode="on";
                if(typeof(d.contentEditable)!="undefined") d.contentEditable=true;
            } catch(e) {}
        }
    }
  } 

function EditorUtil() {
    this.obj = null;
    this.oEditor = null;
    this.arrEditor = [];

    var oScripts=document.getElementsByTagName("script");
    for(var i=0;i<oScripts.length;i++)
      {
        var sSrc=oScripts[i].src.toLowerCase();
        if(sSrc.indexOf("scripts/quick/moz/xhtmleditor.js")!=-1) this.scriptPath = oScripts[i].src.replace(/quick\/moz\/xhtmleditor.js/ig,"");
      }    
    
}

function InnovaEditor(oName) {
    this.oName = oName;
    this.height = "400px";
    this.width = "100%";
    
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
}

function RENDER() {
}

function edt_doCmd(sCmd,sOption)
  {
    var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
    oEditor.document.execCommand(sCmd,false,sOption);
    }

function edt_getHTMLBody()
  {
    var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
    sHTML=oEditor.document.body.innerHTML;
    sHTML=String(sHTML).replace(/ contentEditable=true/g,"");
    sHTML = String(sHTML).replace(/\<PARAM NAME=\"Play\" VALUE=\"0\">/ig,"<PARAM NAME=\"Play\" VALUE=\"-1\">");
    if(this.encodeIO)sHTML = encodeHTMLCode(sHTML);
    sHTML = sHTML.replace(/class="Apple-style-span"/gi, "");
    return sHTML;
  }

function edt_getXHTMLBody()
  {
  var sHTML=""
  if (document.getElementById("chkViewSource"+this.oName).checked) 
      {
            var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
          sHTML = oEditor.document.body.textContent;
        } else {
            var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
            this.cleanDeprecated();

            sHTML = recur(oEditor.document.body,"");
        } 
  if(this.encodeIO)sHTML = encodeHTMLCode(sHTML);
  sHTML = sHTML.replace(/class="Apple-style-span"/gi, "");
  return sHTML;
  }

/*Insert custon HTML function*/
function edt_insertHTML(sHTML)
  {
  var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
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

/************************************
  CLEAN DEPRECATED TAGS; Used in loadHTML, getHTMLBody, getXHTMLBody 
*************************************/
function edt_cleanDeprecated()
  {
    var oEditor=document.getElementById("idContent"+this.oName).contentWindow;

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
  while(elements.length>0) 
    {
    var f = elements[0];
    theParent = f.parentNode;
    theParent.removeChild(f);
    }
  
  this.cleanFonts();
  this.cleanEmptySpan();

  return true;
  }

function edt_cleanEmptySpan()
  {
  var bReturn=false;
  var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
  var reg = /<\s*SPAN\s*>/gi;

  while (true) 
    {
    var allSpans = oEditor.document.getElementsByTagName("SPAN");
    if(allSpans.length==0) break;

    var emptySpans = []; 
    for (var i=0; i<allSpans.length; i++) 
      {
      if (getOuterHTML(allSpans[i]).search(reg) == 0)
        emptySpans[emptySpans.length]=allSpans[i];
      }
    if (emptySpans.length == 0) break;
    var theSpan, theParent;
    for (var i=0; i<emptySpans.length; i++) 
      {
      theSpan = emptySpans[i];
      theParent = theSpan.parentNode;
      if (!theParent) continue;
      if (theSpan.hasChildNodes()) 
        {
        var range = oEditor.document.createRange();
        range.selectNodeContents(theSpan);
        var docFrag = range.extractContents();
        theParent.replaceChild(docFrag, theSpan);
        } 
      else 
        {
        theParent.removeChild(theSpan);
        }
      bReturn=true;
      }
    }
  return bReturn;
  }
  
function edt_cleanFonts() 
  {
  var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
  var allFonts = oEditor.document.body.getElementsByTagName("FONT");
  if(allFonts.length==0)return false;
  
  var f; var range;
  while (allFonts.length>0) 
    {
    f = allFonts[0];
    if (f.hasChildNodes && f.childNodes.length==1 && f.childNodes[0].nodeType==1 && f.childNodes[0].nodeName=="SPAN") 
      {
      //if font containts only span child node
      
      var theSpan = f.childNodes[0];
      copyAttribute(theSpan, f);
      
      range = oEditor.document.createRange();
      range.selectNode(f);
      range.insertNode(theSpan);
      range.selectNode(f);
      range.deleteContents();
      } 
    else 
      if (f.parentNode.nodeName=="SPAN" && f.parentNode.childNodes.length==1) 
        {
        //font is the only child node of span.
        var theSpan = f.parentNode;
        copyAttribute(theSpan, f);
        theSpan.innerHTML = f.innerHTML;
        } 
      else 
        {
        var newSpan = oEditor.document.createElement("SPAN");
        copyAttribute(newSpan, f);
        newSpan.innerHTML = f.innerHTML;
        f.parentNode.replaceChild(newSpan, f);
        }
    }
  return true;
  }
function edt_cleanTags(elements,sVal)
  {
  var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
  if(elements.length==0)return false;
  
  var f;var range;
  while(elements.length>0) 
    {
    f = elements[0];
    if(f.hasChildNodes && f.childNodes.length==1 && f.childNodes[0].nodeType==1 && f.childNodes[0].nodeName=="SPAN") 
      {//if font containts only span child node      
      var theSpan=f.childNodes[0];
      if(sVal=="bold")theSpan.style.fontWeight="bold";
      if(sVal=="italic")theSpan.style.fontStyle="italic";
      if(sVal=="line-through")theSpan.style.textDecoration="line-through";
      if(sVal=="underline")theSpan.style.textDecoration="underline";

      range=oEditor.document.createRange();
      range.selectNode(f);
      range.insertNode(theSpan);
      range.selectNode(f);
      range.deleteContents();
      } 
    else 
      if (f.parentNode.nodeName=="SPAN" && f.parentNode.childNodes.length==1) 
        {
        //font is the only child node of span.
        var theSpan=f.parentNode;
        if(sVal=="bold")theSpan.style.fontWeight="bold";
        if(sVal=="italic")theSpan.style.fontStyle="italic";
        if(sVal=="line-through")theSpan.style.textDecoration="line-through";
        if(sVal=="underline")theSpan.style.textDecoration="underline";
        
        theSpan.innerHTML=f.innerHTML;
        } 
      else 
        {
        var newSpan = oEditor.document.createElement("SPAN");
        if(sVal=="bold")newSpan.style.fontWeight="bold";
        if(sVal=="italic")newSpan.style.fontStyle="italic";
        if(sVal=="line-through")newSpan.style.textDecoration="line-through";
        if(sVal=="underline")newSpan.style.textDecoration="underline";

        newSpan.innerHTML=f.innerHTML;
        f.parentNode.replaceChild(newSpan,f);
        }
    }
  return true;
  }

function edt_replaceTags(sFrom,sTo)
  {
  var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
  
  var elements=oEditor.document.body.getElementsByTagName(sFrom);
  
  while(elements.length>0) 
    {
    f = elements[0];
    
    var newSpan = oEditor.document.createElement(sTo);
    newSpan.innerHTML=f.innerHTML;
    f.parentNode.replaceChild(newSpan,f);
    }
  }
function copyAttribute(newSpan,f) 
    {
    if ((f.face != null) && (f.face != ""))newSpan.style.fontFamily=f.face;
    if ((f.size != null) && (f.size != ""))
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
    if ((f.style.backgroundColor != null)&&(f.style.backgroundColor != ""))newSpan.style.backgroundColor=f.style.backgroundColor;
    if ((f.color != null)&&(f.color != ""))newSpan.style.color=f.color;
    }
function GetElement(oElement,sMatchTag)//Used in realTime() only.
    {
    while (oElement!=null&&oElement.tagName!=sMatchTag)
        {
        if(oElement.tagName=="BODY")return null;
        oElement=oElement.parentNode;
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
  var sHTML="";
  for(var i=0;i<oEl.childNodes.length;i++)
    {
    var oNode=oEl.childNodes[i];
    if(oNode.nodeType==1)//tag
      {
      var sTagName = oNode.nodeName;

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
        s=getOuterHTML(oNode);

        s=s.replace(/\"[^\"]*\"/ig,function(x){           
            x=x.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&apos;").replace(/\s+/ig,"#_#");
            return x});
        s=s.replace(/<([^ >]*)/ig,function(x){return x.toLowerCase()})            
        s=s.replace(/ ([^=]+)=([^"' >]+)/ig," $1=\"$2\"");//new
        
        s=s.replace(/ ([^=]+)=/ig,function(x){return x.toLowerCase()});
        s=s.replace(/#_#/ig," ");
        
        s=s.replace(/<param([^>]*)>/ig,"\n<param$1 />").replace(/\/ \/>$/ig," \/>");//no closing tag

        if(sTagName=="EMBED")
          if(oNode.innerHTML=="")
            s=s.replace(/>$/ig," \/>").replace(/\/ \/>$/ig,"\/>");//no closing tag
        
        s=s.replace(/<param name=\"Play\" value=\"0\" \/>/,"<param name=\"Play\" value=\"-1\" \/>")
        
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
          
        var oNode2=oNode.cloneNode(false);       
        s=getOuterHTML(oNode2).replace(/<\/[^>]*>/,"");
        
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
    
        s=s.replace(/<([^ >]*)/ig,function(x){return x.toLowerCase()})            
        s=s.replace(/ ([^=]+)=([^" >]+)/ig," $1=\"$2\"");
        s=s.replace(/ ([^=]+)=/ig,function(x){return x.toLowerCase()});
        s=s.replace(/#_#/ig," ");
        
        //single attribute
        s=s.replace(/[<hr]?(noshade="")/ig,"noshade=\"noshade\"");
        s=s.replace(/[<input]?(checked="")/ig,"checked=\"checked\"");
        s=s.replace(/[<select]?(multiple="")/ig,"multiple=\"multiple\"");
        s=s.replace(/[<option]?(selected="")/ig,"selected=\"true\"");
        s=s.replace(/[<input]?(readonly="")/ig,"readonly=\"readonly\"");
        s=s.replace(/[<input]?(disabled="")/ig,"disabled=\"disabled\"");
        s=s.replace(/[<td]?(nowrap="" )/ig,"nowrap=\"nowrap\" ");
        s=s.replace(/[<td]?(nowrap=""\>)/ig,"nowrap=\"nowrap\"\>");
        
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
          s=getOuterHTML(oNode);
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
              sT + s + "\n" +
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
              sT + s + "\n" +
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
            
          sHTML+="</" + sTagName.toLowerCase() + ">";
          }     
        }     
      }
    else if(oNode.nodeType==3)//text
      {
      sHTML+= fixVal(oNode.nodeValue);
      }
    else if(oNode.nodeType==8)
      {
      if(getOuterHTML(oNode).substring(0,2)=="<"+"%")
        {//server side script
        sTmp=(getOuterHTML(oNode).substring(2))
        sTmp=sTmp.substring(0,sTmp.length-2)
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

        /*** tabs ***/
        var sT= sTab;
        /************/
        
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

function getOuterHTML(node) 
  {
    var sHTML = "";
    switch (node.nodeType) 
    {
        case Node.ELEMENT_NODE:
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
                    sHTML += ' ' + node.attributes[atr].nodeName + '="' + node.attributes[atr].nodeValue + '"';
          }
        }
            sHTML += '>'; 
            sHTML += (node.nodeName!='TEXTAREA' ? node.innerHTML : tagVal);
            sHTML += "</"+node.nodeName+">";
            break;
        case Node.COMMENT_NODE:
            sHTML = "<!"+"--"+node.nodeValue+ "--"+">"; break;
        case Node.TEXT_NODE:
            sHTML = node.nodeValue; break;
    }
    return sHTML
  }
  
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
    var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
    
    this.cleanDeprecated();
    var sHTML=recur(oEditor.document.body,"");
    sHTML = sHTML.replace(/class="Apple-style-span"/gi, "");

    var docBody = oEditor.document.body;
    docBody.innerHTML = "";
    docBody.appendChild(oEditor.document.createTextNode(sHTML));
}

function edt_applySource() {
    var oEditor=document.getElementById("idContent"+this.oName).contentWindow;
    
    var range = oEditor.document.body.ownerDocument.createRange();
    range.selectNodeContents(oEditor.document.body);
    var sHTML = range.toString();
    sHTML = sHTML.replace(/>\s+</gi, "><"); //replace space between tag
    sHTML = sHTML.replace(/\r/gi, ""); //replace space between tag
    sHTML = sHTML.replace(/(<br>)\s+/gi, "$1"); //replace space between BR and text
    sHTML = sHTML.replace(/(<br\s*\/>)\s+/gi, "$1"); //replace space between <BR/> and text. spasi antara <br /> menyebebkan content menggeser kekanan saat di apply
    sHTML = sHTML.replace(/\s+/gi, " "); //replace spaces with space    
    oEditor.document.body.innerHTML = sHTML;
}

function encodeHTMLCode(sHTML) {
  return sHTML.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
}

function getSelectedElement(sel) 
  {
    var range = sel.getRangeAt(0);
    var node = range.startContainer;
    if (node.nodeType == Node.ELEMENT_NODE) 
    {
        if (range.startOffset >= node.childNodes.length) return node;
        node = node.childNodes[range.startOffset];
    }
    if (node.nodeType == Node.TEXT_NODE) 
    {    
        if (node.nodeValue.length == range.startOffset) 
      {
      var el = node.nextSibling;
      if (el && el.nodeType==Node.ELEMENT_NODE) 
        {
        if (range.endContainer.nodeType==Node.TEXT_NODE && range.endContainer.nodeValue.length == range.endOffset) 
          {
          if (el == range.endContainer.parentNode) 
            {
            return el;
            }
          }
        }
      }    
        while (node!=null && node.nodeType != Node.ELEMENT_NODE) 
      {
            node = node.parentNode;
      }
    }    
    return node;
  }

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