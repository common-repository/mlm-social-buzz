//Modified version of http://assets.pinterest.com/js/pinit.js
//Pinterest last updated 3/16/2012
//Added "always-show-count" attribute, updated style widths/heights
//Points to same iFrame html

(function(){
	for(var e=[],c=document.getElementsByTagName("a"),b=0;b<c.length;b++)
		e.push(c[b]);
	for(;e.length>0;){
		c=e.pop();
        
		//if(!(!c.className||c.className.indexOf("pin-it-button")<0)){
        
        //Changed class detection to avoid conflict with original pinit.js
        if(!(!c.className||c.className.indexOf("pin-it-button2")<0)){
        
			var d=c.getAttribute("href");
			b={};
			d=d.slice(d.indexOf("?")+1).split("&");
			for(var a=0;a<d.length;a++){
				var g=d[a].split("=");
				b[g[0]]=g[1]
            }
            b.layout=c.getAttribute("count-layout");
			b.count=c.getAttribute("always-show-count");
			a="?";
			
            //d=window.location.protocol+"//d3io1k5o0zdpqr.cloudfront.net/pinit.html";

			//Point to local JS
			d = iFrameBtnUrl;
            
			for(var f in b)
				if(b[f]){
					d+=a+f+"="+b[f];
					a="&"
				}
			a=document.createElement("iframe");
			a.setAttribute("src",d);
			a.setAttribute("scrolling","no");
			a.allowTransparency=true;
			a.frameBorder=0;
			a.style.border="none";
			if(b.layout=="none"){
				a.style.width="43px";
				a.style.height="20px"
			}else if(b.layout=="vertical"){
				a.style.width="43px";
				a.style.height="58px"
			}else{
				a.style.width="90px";
				a.style.height="20px"
			}
			c.parentNode.replaceChild(a,c)
        }
    }
})();
