/*!
 * jQuery Cookie Plugin
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function(g){g.cookie=function(h,b,a){if(1<arguments.length&&(!/Object/.test(Object.prototype.toString.call(b))||null===b||void 0===b)){a=g.extend({},a);if(null===b||void 0===b)a.a=-1;if("number"===typeof a.a){var d=a.a,c=a.a=new Date;c.setDate(c.getDate()+d)}b=String(b);return document.cookie=[encodeURIComponent(h),"=",a.b?b:encodeURIComponent(b),a.a?"; expires="+a.a.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+a.domain:"",a.c?"; secure":""].join("")}a=b||{};for(var d=a.b?function(a){return a}:
decodeURIComponent,c=document.cookie.split("; "),e=0,f;f=c[e]&&c[e].split("=");e++)if(d(f[0])===h)return d(f[1]||"");return null}})(jQuery);