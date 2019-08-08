/*

  Typografica library: supplementary scripts
  ---------
  http://www.pixel-apes.com/typografica

  Copyright (c) 2004, Kuso Mendokusee <mailto:mendokusee@yandex.ru>
  All rights reserved.

  For LICENSE see license.txt

=============================================================== (kuso@npj)
*/

function undef(p) { return p; } 
function openNewWindow( chapter )
{
 window.open('','Qresult'+chapter ,'status=no,scrollbars=yes,resizable=yes,width=710');
}

function sign(x)
{
  if (x > 0) return 1;
  if (x < 0) return -1;
  return 0;
}

function BrowserCheck() {
    var b = navigator.appName;
    this.os = navigator.userAgent.toLowerCase(); //TM
    if (b == "Netscape") this.b = "ns";
    else if (b == "Microsoft Internet Explorer") this.b = "ie";
    else this.b = b;
    this.version = navigator.appVersion;
    this.v = parseInt(this.version);
    this.ns = (this.b == "ns" && this.v >= 4);
    this.ns4 = (this.ns && this.v == 4);
    this.ns5 = (this.ns && this.v == 5);
    this.ie = (this.b == "ie" && this.v >= 4);
    this.ie4 = (this.version.indexOf('MSIE 4') > 0);
    this.ie5 = (this.version.indexOf('MSIE 5') > 0);
    this.min = (this.ns || this.ie);
    this.opera = (this.os.indexOf("opera") != -1); //TM
    this.win = (this.os.indexOf("win") != -1); //TM
    this.win9 = ((this.os.indexOf("win 9") != -1) || (this.os.indexOf("win9") != -1) || (this.os.indexOf("windows 9") != -1)|| (this.os.indexOf("windows9") != -1)); //TM
    this.winnt = ((this.os.indexOf("win nt") != -1) || (this.os.indexOf("winnt") != -1)); //TM
    this.win2000 = ((this.os.indexOf("win nt 5.0") != -1) || (this.os.indexOf("windows nt 5.0") != -1)); //TM
    this.mac = (this.os.indexOf("mac") != -1); //TM
    this.unix = (this.os.indexOf("x11") != -1); //TM
}

is = new BrowserCheck();

function travelA( Aname )
{
  if (!(is.ie && !is.opera)) return true;
  z = document.all[Aname];
  var x=0,y=0;
  while (z != document.body)
  {
    x += parseInt(isNaN(parseInt(z.offsetLeft))?0:z.offsetLeft);
    y += parseInt(isNaN(parseInt(z.offsetTop))?0:z.offsetTop);
    z = z.offsetParent;
  }
  travelto( x,  y );
  return false;
}
function travelto(x, y)
{
  do
    {
      ox = document.body.scrollLeft;
      oy = document.body.scrollTop;
      dx = (x - ox) / 10;
      dx = sign(dx) * Math.ceil(Math.abs(dx));
      dy = (y - oy) / 10;
      dy = sign(dy) * Math.ceil(Math.abs(dy));
      window.scrollBy(dx, dy);
      cx = document.body.scrollLeft;
      cy = document.body.scrollTop;
    }
  while (( (ox-cx) != 0 ) || ( (oy-cy) != 0 ));
}


var picArray = new Array();
var preloadFlag = false;
var ok = false;

function cI()
{
 if (document.images && (preloadFlag == true)) 
 {
  for (var i=0; i<cI.arguments.length; i+=2) 
  {
    document[cI.arguments[i]].src = picArray[ cI.arguments[i+1] ].src;
  }
 }
 return false;
}


function preloadPics()
{
  var dir = ""+preloadPics.arguments[0]+"/";
  for (var i=1; i<preloadPics.arguments.length; i++)
    {
      picArray[preloadPics.arguments[i]] = new Image();
      picArray[preloadPics.arguments[i]].src = dir + preloadPics.arguments[i] + ".gif";
      if (ok)
      ok = confirm( "preload:"+dir + preloadPics.arguments[i] + ".gif" );
    }
}

function preloadImages()
{
  if (document.images) 
  {
    preloadPics( "",
      "menubar", "menubar_", "menubar0",
      "top", "top_"
      );
    preloadPics( "",
      "go__", "go", "go_"
      );
  }
  preloadFlag = true;
}

function toggleMenu(N, FN)
{
  if (preloadFlag)
    document[N].src = picArray[FN].src;
}

      
    
