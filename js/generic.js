var tab='inet';
function gId(id){if(document.getElementById)return document.getElementById(id);else return false;}
function show(obj){gId(tab).className="";gId(obj).className="active";gId(obj+'-field').focus();switch(obj){case'inet':if(typeof(ac)!='undefined')ac.init(obj+'-field',8);break;case'firms':if(typeof(ac)!='undefined')ac.init(obj+'-field',8)
;break;}gId(obj+'-field').value=gId(tab+'-field').value;tab=obj; return false;}

addEvent(window, "load", makeNiceTitles);
var XHTMLNS = "http://www.w3.org/1999/xhtml";
var CURRENT_NICE_TITLE;
var browser = new Browser();
function makeNiceTitles() {
        if (!document.createElement || !document.getElementsByTagName) return;
        if(!document.createElementNS)
        {
                document.createElementNS = function(ns,elt) {
                        return document.createElement(elt);
                }
        }

        if( !document.links )
        {
                document.links = document.getElementsByTagName("a");
        }
        for (var ti=0;ti<document.links.length;ti++) {
                var lnk = document.links[ti];
                if (lnk.title) {
                        lnk.setAttribute("nicetitle",lnk.title);
                        lnk.removeAttribute("title");
                        addEvent(lnk,"mouseover",showNiceTitle);
                        addEvent(lnk,"mouseout",hideNiceTitle);
                        addEvent(lnk,"focus",showNiceTitle);
                        addEvent(lnk,"blur",hideNiceTitle);
                }
        }
        var instags = document.getElementsByTagName("ins");
        if (instags) {
                for (var ti=0;ti<instags.length;ti++) {
                        var instag = instags[ti];
                        if (instag.dateTime) {
                                var strDate = instag.dateTime;
                                var dtIns = new Date(strDate.substring(0,4),parseInt(strDate.substring(4,6)-1),strDate.substring(6,8),strDate.substring(9,11),strDate.substring(11,13),strDate.substring(13,15));
                                instag.setAttribute("nicetitle","Added on "+dtIns.toString());
                                addEvent(instag,"mouseover",showNiceTitle);
                                addEvent(instag,"mouseout",hideNiceTitle);
                                addEvent(instag,"focus",showNiceTitle);
                                addEvent(instag,"blur",hideNiceTitle);
                        }
                }
        }
}

function findPosition( oLink ) {
        if( oLink.offsetParent ) {
                for( var posX = 0, posY = 0; oLink.offsetParent; oLink = oLink.offsetParent ) {
                        posX += oLink.offsetLeft;
                        posY += oLink.offsetTop;
                }
                return [ posX, posY ];
        } else {
                return [ oLink.x, oLink.y ];
        }
}

function showNiceTitle(e) {
        if (CURRENT_NICE_TITLE) hideNiceTitle(CURRENT_NICE_TITLE);
        if (!document.getElementsByTagName) return;
        if (window.event && window.event.srcElement) {
                lnk = window.event.srcElement
        } else if (e && e.target) {
                lnk = e.target
        }
        if (!lnk) return;
        if (lnk.nodeName.toUpperCase() != 'A') {
                lnk = getParent(lnk,"A");
        }

        if (!lnk) return;
        nicetitle = lnk.getAttribute("nicetitle");

        var d = document.createElementNS(XHTMLNS,"div");
        d.className = "nicetitle";
        tnt = document.createTextNode(nicetitle);
        pat = document.createElementNS(XHTMLNS,"p");
        pat.className = "titletext";
        pat.appendChild(tnt);
        d.appendChild(pat);
        if (lnk.href) {
                tnd = document.createTextNode(lnk.href);
                pad = document.createElementNS(XHTMLNS,"p");
                pad.className = "destination";
                pad.appendChild(tnd);
                d.appendChild(pad);
        }

        STD_WIDTH = 230;
        if (lnk.href) {
                h = lnk.href.length;
        } else { h = nicetitle.length; }
        if (nicetitle.length) {
                t = nicetitle.length;
        }
        h_pixels = h*6; t_pixels = t*10;

        if (h_pixels > STD_WIDTH) {
                w = h_pixels;
        } else if ((STD_WIDTH>t_pixels) && (t_pixels>h_pixels)) {
                w = t_pixels;
        } else if ((STD_WIDTH>t_pixels) && (h_pixels>t_pixels)) {
                w = h_pixels;
        } else {
                w = STD_WIDTH;
        }

        d.style.width = w + 'px';

        mpos = findPosition(lnk);
        mx = mpos[0];
        my = mpos[1];

        d.style.left = (mx+8) + 'px';

        d.style.top = (my-40) + 'px';

        if (window.innerWidth && ((mx+w) > window.innerWidth)) {
                d.style.left = (window.innerWidth - w - 25) + "px";
        }
        if (document.body.scrollWidth && ((mx+w) > document.body.scrollWidth)) {
        d.style.left = (document.body.scrollWidth - w - 25) + "px";
    }

    document.getElementsByTagName("body")[0].appendChild(d);

    CURRENT_NICE_TITLE = d;
}

function hideNiceTitle(e) {
    if (!document.getElementsByTagName) return;
    if (CURRENT_NICE_TITLE) {
        document.getElementsByTagName("body")[0].removeChild(CURRENT_NICE_TITLE);
        CURRENT_NICE_TITLE = null;
    }
}
