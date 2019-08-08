/*

  Typografica library: typografica JavaScript class.
  v.2.6
  23 February 2005. 

  ---------

  http://www.pixel-apes.com/typografica

  Copyright (c) 2004, Kuso Mendokusee <mailto:mendokusee@yandex.ru>
  All rights reserved.

  For LICENSE see license.txt

  ---------

  Corrector (JS-version of Typografica) aka << TYPOGRAFICA.3 [������ siteCore] >>
  
  * constructor( settings, afunc ) -- �����������
      - settings -- ����� �������� ����� ������, �������� "laquo br (c)"
      - afunc    -- �������, ����� ������� ����� ������������� ��������� ������

  * getDescriptions() -- ���������� �������� ��������� �������� (������������ ��� ��������� ����� ��������)
  * correct( data, noParagraph ) -- ���������� ���������������� �����
      - data -- �����, ������� ����� ��������������
      - noParagraph -- �� ������������ ������ "<//>" (�� �������� ��������� in general)

=============================================================== (kuso@npj)
*/
// -----------------------------------------------------------------------------------
// ����������� ������. ������� �������� - ����� ���������.
function typografica( settings, afunc ) // ������ ���� "laquo br (c) (r) http", ������� ��������� ������ �(..)
{
  this.skipTags = true;   // ��������� �� ��������� ����
  this.Pprefix ="<p class=typo>";     //  ����� ���������� ����� ����������� ���
  this.Ppostfix="</p>";   //  ������������� <//>
  this.Asoft = true; // ���� ��������
  this.Indent1 = "<img src=/z.gif width=25 height=1 border=0 alt='' align=top>"; // <->
  this.Indent2 = "<img src=/z.gif width=50 height=1 border=0 alt='' align=top>"; // <-->
  this.FixedSize = 80;

  this.phonemasks = new Array(
                        new Array(
                                  "(\\([0-9\\+\\-]+\\)) ?([0-9]{3})\\-([0-9]{2})\\-([0-9]{2})",
                                  "(\\([0-9\\+\\-]+\\)) ?([0-9]{2})\\-([0-9]{2})\\-([0-9]{2})",
                                  "(\\([0-9\\+\\-]+\\)) ?([0-9]{3})\\-([0-9]{2})",
                                  "(\\([0-9\\+\\-]+\\)) ?([0-9]{2})\\-([0-9]{3})",
                                  "([0-9]{3})\\-([0-9]{2})\\-([0-9]{2})",
                                  "([0-9]{2})\\-([0-9]{2})\\-([0-9]{2})",
                                  "([0-9]{3})\\-([0-9]{2})",
                                  "([0-9]{2})\\-([0-9]{3})"
                                  ),
                        new Array("<nobr>$1&nbsp;$2&#0150;$3&#0150;$4</nobr>",
                                  "<nobr>$1&nbsp;$2&#0150;$3&#0150;$4</nobr>",
                                  "<nobr>$1&nbsp;$2&#0150;$3</nobr>",
                                  "<nobr>$1&nbsp;$2&#0150;$3</nobr>",
                                  "<nobr>$1&#0150;$2&#0150;$3</nobr>",
                                  "<nobr>$1&#0150;$2&#0150;$3</nobr>",
                                  "<nobr>$1&#0150;$2&#0150;</nobr>",
                                  "<nobr>$1&#0150;$2&#0150;</nobr>"
                                  )
                              );
  this.glueleft = new Array( "���\\.", "����\\.", "��\\.", "��\\.", "��\\.", "���\\.", "��\\.", "����", "��\\." );
  this.glueright = new Array( "���\\.", "���\\.", "�\\.�\\.", "���\\." );

  this.afunc = afunc;

  // ������������� ��������� �������
  this.correct = typografica_Correct;
  this.getDescriptions = typografica_GetDescriptions;
  // ������������� ��������� �������
  this._replaceSpecials = typografica_ReplaceSpecials;
  this._replaceMacros = typografica_ReplaceMacros;

  // ������������ ��������
  if (settings == undef()) settings = "inches laquo quotes dash emdash (c) (r) (tm) (p) +- degrees <--> mailto http dashglue wordglue spacing phones";
  this._settings = settings;
  var _settings = settings.split(" ");
  this.settings = new Array();
  for (var i=0; i<_settings.length; i++)
  {
   this.settings[ _settings[i] ] = true;
  }
}

// -----------------------------------------------------------------------------------
// ���������� ��������������� ������, � ������� ������ ������� - ���� (���,��������)
//  ����: 0-����.�������, 1-�������, 2-���������
function typografica_GetDescriptions()
{
  var result = new Array();

  result["inches"]    = new Array(0, "����� � �������: 15\", 3.5\"");
  result["laquo"]     = new Array(0, "�������-������: &laquo; / &raquo;");
  result["farlaquo"]  = new Array(0, "�������-������ ��� FAR: &laquo; / &raquo;");
  result["quotes"]    = new Array(0, "�������-�����: &#147; / &#148;");
  result["dash"]      = new Array(0, "����: - / &#0150;");
  result["emdash"]    = new Array(0, "������� ����: -- / &#0151;");
  result["(c)"]       = new Array(0, "copyright (c)");
  result["(r)"]       = new Array(0, "registered (r)");
  result["(tm)"]      = new Array(0, "trademark (tm)");
  result["(p)"]       = new Array(0, "�������� (p) / &#0167;");
  result["+-"]        = new Array(0, "+- / &#0177;");
  result["degrees"]   = new Array(0, "^C / &#0176;C, ^F / &#0176;F");
  result["phones"]    = new Array(0, "�������� ���� � ���������");

  result["<//>"]    = new Array(1, "��������� �� ������ �� &lt;//>");
  result["<-->"]    = new Array(1, "������� ������ �� <->, <-->");
  result["mailto"]  = new Array(1, "����-<u>mailto:ni@sharpdesign.ru</u>");
  result["http"]    = new Array(1, "����-<u>http://www.sharpdesign.ru</u>");

  result["br"]                  = new Array(2, "�������� �����");
  result["br2"]                 = new Array(2, "������� �������� �����");
  result["wordglue"]            = new Array(2, "�������� � &amp;nbsp;");
  result["dashglue"]            = new Array(2, "������ � &amp;nbsp;");
  result["spacing"]             = new Array(2, "������� � �������");
  result["html"]                = new Array(2, "������ ����� HTML");

  result["fixed"]           = new Array(2, "������ ��� ������������� ������");

}

// -----------------------------------------------------------------------------------
//  ��������� �����������. ���������� ������������������ ������
function typografica_Correct( data, noParagraph )
{
  // -1. ������ ����� HTML
  if (this.settings["html"])
  {
    data = data.replace(/\&/gi, "&amp;");
  }

  // 0. �������� ����
  //  �������� �� ����� ���� � ���, �� ��� ������ ����.
  //   ������� 1, ������� (����������� ���) </abcz>
  //   ������� 2, ������� (������ ���)      <abcz>
  //   ������� 3, ���������                 <abcz href="abcz">
  //   ����� ������� ������� - ��� ����� � ��������� ���� ����������� ����� ������ ">"
  //   ��� ��: <abcz href="abcz>">
  //  ��� �������� ���������? ����� ����������. ��, ��, ����������.
  //    ��� �� ��� �������� =)
  //  ������� ��� ���� �� ����.������, ��������� ������������ �� � ������. 
  //    � ���������� ������� �� ��� ������ ������ - ����� ���������
  var tags = new Array();
  if (this.skipTags)
  {
    var re = new RegExp("</?[a-z0-9]+("+
                          "\\s+("+
                              "[a-z]+("+
                                  "=((\'[^\']+\')|(\"[^\"]+\")|([0-9@\\-_a-z:/?&=.]+))"+
                              ")?"+
                          ")?"+
                        ")*>","i");
    var arr;
    while ((arr = data.match(re)) != null)
    {
      tags = tags.concat( data.substring( arr.index, arr.lastIndex ));
      if (this.settings["html"]) 
        tags[tags.length-1] = "&lt;"+tags[tags.length-1].substr(1);
      data = data.replace( re, "\200" );
    }
  }

  // 1. ������� � �������
  if (this.settings["spacing"])
  {
    data = data.replace(/([\s]*)([,.!?]*)/gi, "$2$1");
  }

  // 2. ��������� �� ������ ������ �� ����� �� ��������
  if (this.settings["fixed"])
  {
    var _a = data.split("\n");
    var _data = "";
    for (var i=0; i<_a.length; i++)
      if (_a[i].length <= this.FixedSize) _data += _a[i]+"<br/>"; // ������ ���������� �������
      else
      {
        var l=0;
        var __a = _a[i].split(" ");       // ��������� ������ �� ��������. ������������ ����� � ��������� �������� ��������� �����.
        for (var j=0; j<__a.length; j++)
        if (l+__a[j].length <= this.FixedSize) 
        { _data += __a[j]+" ";
          l += __a[j].length+1;
        } else
        { _data += "<br/>"+__a[j]+" ";
          l = __a[j].length+1;
        }
      }
    data = _data;

    // 2a. ��������� �� ������� ������������ ���� � ������������� �����������
    var _data="";
    while (_data != data)
    {
      _data = data;
      var re3 = new RegExp( "([^<>\\s\\200]{"+(this.FixedSize)+"})([^<>\\s\\200])", "i");
      data = data.replace( re3, "$1<br/>$2");
    }
  }
  // 3. �����������
  data = this._replaceSpecials( data );

  // 4. �������� ����� � &nbsp;
  if (this.settings["wordglue"])
  {
    data = data.replace(/([\s]+)([a-z�-��-�]{1,3})([\s]+)/gi, "$1$2&nbsp;");
    for (var i in this.glueleft) 
      data = data.replace( new RegExp( "([\\s]+)("+this.glueleft[i]+")([\\s]+)","gi"), "$1$2&nbsp;");
    for (var i in this.glueright) 
      data = data.replace( new RegExp( "([\\s]+)("+this.glueright[i]+")([\\s]+)","gi"), "&nbsp;$2$3");
  }
  // 5. ������� ����. ����! �������.
  if (this.settings["dashglue"])
  {
    data = data.replace(/([a-z�-��-�0-9]+(\-[a-z�-��-�0-9]+)+)/gi, "<nobr>$1</nobr>");
  }
  // 6. �������
  data = this._replaceMacros( data, noParagraph );
  // 7. �������� �����
  if (this.settings["br2"])
    data = data.replace( /\n[ \f\r\t\v]*\n/g, "<br/><br/>");
  if (this.settings["br"])
    data = data.replace( /\n/g, "<br/>");

  // �������������. ��������� ���� �������.
  if (this.skipTags)
  {
    data += " ";
    var re2 = new RegExp("\200","i");
    var c=0;
    while ((arr = data.match(re2)) != null)
    {
      data = data.replace( re2, tags[c++] );
    }
  }

  // �����: ������������� ������ ����� A(...)
  if (this.afunc != undef())
  {
    data += " ";
    var re2 = new RegExp("<a href=([^>]+)>([^<]+)</a>","i");
    var c=0;
    while ((arr = data.match(re2)) != null)
    {
      arr[1] = arr[1].replace(/[\"\']/gi, "");
      data = data.replace( re2, this.afunc(arr[1],arr[2]) );
      data = data.replace( /<a/i, "<\200a" );
    }
    data = data.replace( /<\200a/gi, "<a" );
  }

  return data.replace(/^(\s)+/,""); 
}


// -----------------------------------------------------------------------------------
// ����� ��� ����������� �������������. ��������� ������ ����.�������
function typografica_ReplaceSpecials( data )
{
  // 0. ����� � �������
  if (this.settings["inches"])
  {
    data = data.replace(/(([^0-9\"])([0-9]+([.,][0-9]+)?))\"/gi, "$1&quot;");
  }
  // 1. �����
  if (this.settings["quotes"])
  {
    data = data.replace(/(^|\s|>)\"([A-Za-z0-9\'\!\s\.\?\,\-\&\;\:]+\")/gi, "$1&#147;$2");
    data = data.replace(/(\&\#147\;([A-Za-z0-9\'\!\s\.\?\,\-\&\;\:]*)[A-Za-z0-9])\"/gi, "$1&#148;");      //'
  }
  // 2. ������
  if (this.settings["laquo"])
  {
    data = data.replace(/(^|\s|>)\"([A-Za-z�-��-�])/gi, "$1&laquo;$2");
    var _data="";
    while (_data != data)
    { _data = data;
      data = data.replace(/(\&laquo\;([^\"]*))\"/gi, "$1&raquo;");
    }
  }
  // 2a. ������ ��� FAR manager
  if (this.settings["farlaquo"])
  {
    data = data.replace(/(^|\s|>)\<([A-Za-z�-��-�])/gi, "$1&laquo;$2");
    var _data="";
    while (_data != data)
    { _data = data;
      data = data.replace(/(\&laquo\;(.*))\>/gi, "$1&raquo;");
    }
  }
  // 3. ������������ ������ � �����
  if ((this.settings["quotes"]) && ((this.settings["laquo"]) || (this.settings["farlaquo"])))
  {
    data = data.replace(/(\&\#147\;([A-Za-z0-9\'\!\s\.\?\,\-\&\;\:]*)\&laquo\;(.*)\&raquo\;)\&raquo\;/gi, "$1&#148;"); //'
  }
  // 3. ����
  if (this.settings["dash"])
    data = data.replace(/(\s|;)\-(\s)/gi, "$1&#0150;$2");
  // 3a. ���� �������
  if (this.settings["emdash"])
    data = data.replace(/(\s|;)\-\-(\s)/gi, "$1&#0151;$2");
  // 4. (�)
  if (this.settings["(c)"])
    data = data.replace(/\([cC��]\)/gi, "&copy;");
  // 4a. (r)
  if (this.settings["(r)"])
    data = data.replace(/\([rR]\)/gi, "<sup>&#0174;</sup>");
  // 4b. (tm)
  if (this.settings["(tm)"])
    data = data.replace(/\([tT][mM]\)/gi, "&#0153;");
  // 4c. (p)
  if (this.settings["(p)"])
    data = data.replace(/\([pP]\)/gi, "&#0167;");
  // 5. +/-
  if (this.settings["+-"])
    data = data.replace(/\+\-/gi, "&#0177;");
  // 5a. 12^C
  if (this.settings["degrees"])
  {
    data = data.replace(/\^([FC�])/g, "&#0176;$1");
  }

  // 6. ��������
  if (this.settings["phones"])
  {
    for (var i in this.phonemasks[0])
      data = data.replace(new RegExp(this.phonemasks[0][i],"gi"), this.phonemasks[1][i]);
  }

  return data;
}

// -----------------------------------------------------------------------------------
// ����� ��� ����������� �������������. ��������� ������ �������
function typografica_ReplaceMacros( data, noParagraph )
{
  // 1. ������
  if (this.settings["<//>"] && !noParagraph)
  {
    data = this.Pprefix+ data.replace(/[\s\r\n]*\<\/\/\>[\s\n\r]*/gi, this.Ppostfix+this.Pprefix) + this.Ppostfix; 
  }
  // 2. ������� ������
  if (this.settings["<-->"])
  {
    data = data.replace(/\<\-\>/gi, this.Indent1); 
    data = data.replace(/\<\-\-\>/gi, this.Indent2); 
  }
  // 3. mailto:
  if (this.settings["mailto"])
  {
    data = data.replace(/\<a\ href\=mailto\:/gi, "<a href=mailthru:");
    var re1 = new RegExp("(mailto\\:([a-z\\.\\-\\_0-9]+)@([a-z\\.\\-\\_0-9]+\\.[a-z]+))", "gi"); // mailto:aaa@bbb.cc
    var re2 = new RegExp("(([a-z\\.\\-\\_0-9]+)@([a-z\\.\\-\\_0-9]+\\.[a-z]+))", "gi"); // aaa@bbb.cc
    if (this.Asoft)
      data = data.replace(re2, "<a href=mailthru:$1>$1</a>"); 
    else
      data = data.replace(re1, "<a href=mailthru:$1>$1</a>"); 
    data = data.replace(/\<a\ href\=mailthru\:/gi, "<a href=mailto:");
  }
  // 4. http://
  if (this.settings["http"])
  {
    data = data.replace(/\<a\ href\=http\/\/\:/gi, "<a href=httpthru:");
    data = data.replace(/\<a\ href\=ftp\/\/\:/gi, "<a href=ftpthru:");
    var re1 = new RegExp("((ht|f)tp://([\\!a-z\\.\\-\\_0-9]+\\.[a-z]+((\\/|\\?)[\\!~a-z\\.\\-\\_0-9\\=\\/\\?\\&]*)?))", "gi"); // http://x123.ru/int_ed.html
    var re2 = new RegExp("([^\/])(www\\.([\\!a-z\\.\\-_0-9]+\.[a-z]+((\\/|\\?)[\\!~a-z\\.\\-\\=\\_0-9\\/\\?\\&]*)?))", "gi"); // www.x123.ru/int_ed.html
    var re3 = new RegExp("([^\/])(ftp\\.([\\!a-z\\.\\-_0-9]+\.[a-z]+((\\/|\\?)[\\!~a-z\\.\\-\\=\\_0-9\\/\\?\\&]*)?))", "gi"); // www.x123.ru/int_ed.html
    data = data.replace(re1, "<a href='$1'>$1</a>"); 
    if (this.Asoft)
    {
      data = data.replace(re2, "$1<a href='http://$2'>$2</a>"); 
      data = data.replace(re3, "$1<a href='ftp://$2'>$2</a>"); 
    }
    data = data.replace(/\<a\ href\=httpthru\:/gi, "<a href=http:");
    data = data.replace(/\<a\ href\=ftpthru\:/gi, "<a href=ftp:");
  }

  return data;
}

