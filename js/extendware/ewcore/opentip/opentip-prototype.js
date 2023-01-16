var Opentip,firstAdapter,i,mouseMoved,mousePosition,mousePositionObservers,position,vendors,_i,_len,_ref,__slice=[].slice,__indexOf=[].indexOf||function(c){for(var a=0,b=this.length;a<b;a++)if(a in this&&this[a]===c)return a;return-1},__hasProp={}.hasOwnProperty;
Opentip=function(){function c(a,b,d,e){var k,f,g,m=this;this.id=++c.lastId;this.debug("Creating Opentip.");c.tips.push(this);this.adapter=c.adapter;k=this.adapter.data(a,"opentips")||[];k.push(this);this.adapter.data(a,"opentips",k);this.triggerElement=this.adapter.wrap(a);if(1<this.triggerElement.length)throw Error("You can't call Opentip on multiple elements.");if(1>this.triggerElement.length)throw Error("Invalid element.");this.waitingToHide=this.waitingToShow=this.visible=this.loading=this.loaded=
!1;this.currentPosition={left:0,top:0};this.dimensions={width:100,height:50};this.content="";this.redraw=!0;this.currentObservers={showing:!1,visible:!1,hiding:!1,hidden:!1};e=this.adapter.clone(e);"object"===typeof b?(e=b,b=d=void 0):"object"===typeof d&&(e=d,d=void 0);null!=d&&(e.title=d);null!=b&&this.setContent(b);null==e["extends"]&&(e["extends"]=null!=e.style?e.style:c.defaultStyle);a=[e];for(d=e;d["extends"];){b=d["extends"];d=c.styles[b];if(null==d)throw Error("Invalid style: "+b);a.unshift(d);
null==d["extends"]&&"standard"!==b&&(d["extends"]="standard")}e=(g=this.adapter).extend.apply(g,[{}].concat(__slice.call(a)));e.hideTriggers=function(){var a,d,b,c;b=e.hideTriggers;c=[];a=0;for(d=b.length;a<d;a++)f=b[a],c.push(f);return c}();e.hideTrigger&&0===e.hideTriggers.length&&e.hideTriggers.push(e.hideTrigger);d=["tipJoint","targetJoint","stem"];a=0;for(b=d.length;a<b;a++)g=d[a],e[g]&&"string"===typeof e[g]&&(e[g]=new c.Joint(e[g]));!e.ajax||!0!==e.ajax&&e.ajax||("A"===this.adapter.tagName(this.triggerElement)?
e.ajax=this.adapter.attr(this.triggerElement,"href"):e.ajax=!1);"click"===e.showOn&&"A"===this.adapter.tagName(this.triggerElement)&&this.adapter.observe(this.triggerElement,"click",function(a){a.preventDefault();a.stopPropagation();return a.stopped=!0});e.target&&(e.fixed=!0);!0===e.stem&&(e.stem=new c.Joint(e.tipJoint));!0===e.target?e.target=this.triggerElement:e.target&&(e.target=this.adapter.wrap(e.target));this.currentStem=e.stem;null==e.delay&&(e.delay="mouseover"===e.showOn?0.2:0);null==e.targetJoint&&
(e.targetJoint=(new c.Joint(e.tipJoint)).flip());this.showTriggers=[];this.showTriggersWhenVisible=[];this.hideTriggers=[];e.showOn&&"creation"!==e.showOn&&this.showTriggers.push({element:this.triggerElement,event:e.showOn});null!=e.ajaxCache&&(e.cache=e.ajaxCache,delete e.ajaxCache);this.options=e;this.bound={};d=["prepareToShow","prepareToHide","show","hide","reposition"];a=0;for(b=d.length;a<b;a++)g=d[a],this.bound[g]=function(a){return function(){return m[a].apply(m,arguments)}}(g);this.adapter.domReady(function(){m.activate();
if("creation"===m.options.showOn)return m.prepareToShow()})}c.prototype.STICKS_OUT_TOP=1;c.prototype.STICKS_OUT_BOTTOM=2;c.prototype.STICKS_OUT_LEFT=1;c.prototype.STICKS_OUT_RIGHT=2;c.prototype["class"]={container:"opentip-container",opentip:"opentip",header:"ot-header",content:"ot-content",loadingIndicator:"ot-loading-indicator",close:"ot-close",goingToHide:"ot-going-to-hide",hidden:"ot-hidden",hiding:"ot-hiding",goingToShow:"ot-going-to-show",showing:"ot-showing",visible:"ot-visible",loading:"ot-loading",
ajaxError:"ot-ajax-error",fixed:"ot-fixed",showEffectPrefix:"ot-show-effect-",hideEffectPrefix:"ot-hide-effect-",stylePrefix:"style-"};c.prototype._setup=function(){var a,b,d,e,c,f;this.debug("Setting up the tooltip.");this._buildContainer();this.hideTriggers=[];f=this.options.hideTriggers;a=e=0;for(c=f.length;e<c;a=++e){b=f[a];d=null;a=this.options.hideOn instanceof Array?this.options.hideOn[a]:this.options.hideOn;if("string"===typeof b)switch(b){case "trigger":a=a||"mouseout";d=this.triggerElement;
break;case "tip":a=a||"mouseover";d=this.container;break;case "target":a=a||"mouseover";d=this.options.target;break;case "closeButton":break;default:throw Error("Unknown hide trigger: "+b+".");}else a=a||"mouseover",d=this.adapter.wrap(b);d&&this.hideTriggers.push({element:d,event:a,original:b})}c=this.hideTriggers;f=[];d=0;for(e=c.length;d<e;d++)b=c[d],f.push(this.showTriggersWhenVisible.push({element:b.element,event:"mouseover"}));return f};c.prototype._buildContainer=function(){this.container=
this.adapter.create('<div id="opentip-'+this.id+'" class="'+this["class"].container+" "+this["class"].hidden+" "+this["class"].stylePrefix+this.options.className+'"></div>');this.adapter.css(this.container,{position:"absolute"});this.options.ajax&&this.adapter.addClass(this.container,this["class"].loading);this.options.fixed&&this.adapter.addClass(this.container,this["class"].fixed);this.options.showEffect&&this.adapter.addClass(this.container,""+this["class"].showEffectPrefix+this.options.showEffect);
if(this.options.hideEffect)return this.adapter.addClass(this.container,""+this["class"].hideEffectPrefix+this.options.hideEffect)};c.prototype._buildElements=function(){var a,b;this.tooltipElement=this.adapter.create('<div class="'+this["class"].opentip+'"><div class="'+this["class"].header+'"></div><div class="'+this["class"].content+'"></div></div>');this.backgroundCanvas=this.adapter.wrap(document.createElement("canvas"));this.adapter.css(this.backgroundCanvas,{position:"absolute"});"undefined"!==
typeof G_vmlCanvasManager&&null!==G_vmlCanvasManager&&G_vmlCanvasManager.initElement(this.adapter.unwrap(this.backgroundCanvas));a=this.adapter.find(this.tooltipElement,"."+this["class"].header);this.options.title&&(b=this.adapter.create("<h1></h1>"),this.adapter.update(b,this.options.title,this.options.escapeTitle),this.adapter.append(a,b));this.options.ajax&&!this.loaded&&this.adapter.append(this.tooltipElement,this.adapter.create('<div class="'+this["class"].loadingIndicator+'"><span>\u21bb</span></div>'));
0<=__indexOf.call(this.options.hideTriggers,"closeButton")&&(this.closeButtonElement=this.adapter.create('<a href="javascript:undefined;" class="'+this["class"].close+'"><span>Close</span></a>'),this.adapter.append(a,this.closeButtonElement));this.adapter.append(this.container,this.backgroundCanvas);this.adapter.append(this.container,this.tooltipElement);this.adapter.append(document.body,this.container);return this.redraw=this._newContent=!0};c.prototype.setContent=function(a){this.content=a;this._newContent=
!0;"function"===typeof this.content?(this._contentFunction=this.content,this.content=""):this._contentFunction=null;if(this.visible)return this._updateElementContent()};c.prototype._updateElementContent=function(){var a;if(this._newContent||!this.options.cache&&this._contentFunction)a=this.adapter.find(this.container,"."+this["class"].content),null!=a&&(this._contentFunction&&(this.debug("Executing content function."),this.content=this._contentFunction(this)),this.adapter.update(a,this.content,this.options.escapeContent)),
this._newContent=!1;this._storeAndLockDimensions();return this.reposition()};c.prototype._storeAndLockDimensions=function(){var a;if(this.container&&(a=this.dimensions,this.adapter.css(this.container,{width:"auto",left:"0px",top:"0px"}),this.dimensions=this.adapter.dimensions(this.container),this.dimensions.width+=1,this.adapter.css(this.container,{width:""+this.dimensions.width+"px",top:""+this.currentPosition.top+"px",left:""+this.currentPosition.left+"px"}),!this._dimensionsEqual(this.dimensions,
a)))return this.redraw=!0,this._draw()};c.prototype.activate=function(){return this._setupObservers("hidden","hiding")};c.prototype.deactivate=function(){this.debug("Deactivating tooltip.");this.hide();return this._setupObservers("-showing","-visible","-hidden","-hiding")};c.prototype._setupObservers=function(){var a,b,d,e,c,f,g,m,l,h=this;e=1<=arguments.length?__slice.call(arguments,0):[];c=0;for(g=e.length;c<g;c++)if(d=e[c],b=!1,"-"===d.charAt(0)&&(b=!0,d=d.substr(1)),this.currentObservers[d]!==
!b)switch(this.currentObservers[d]=!b,a=function(){var a,d,e;a=1<=arguments.length?__slice.call(arguments,0):[];return b?(d=h.adapter).stopObserving.apply(d,a):(e=h.adapter).observe.apply(e,a)},d){case "showing":l=this.hideTriggers;f=0;for(m=l.length;f<m;f++)d=l[f],a(d.element,d.event,this.bound.prepareToHide);a(null!=document.onresize?document:window,"resize",this.bound.reposition);a(window,"scroll",this.bound.reposition);break;case "visible":l=this.showTriggersWhenVisible;f=0;for(m=l.length;f<m;f++)d=
l[f],a(d.element,d.event,this.bound.prepareToShow);break;case "hiding":l=this.showTriggers;f=0;for(m=l.length;f<m;f++)d=l[f],a(d.element,d.event,this.bound.prepareToShow);break;case "hidden":break;default:throw Error("Unknown state: "+d);}return null};c.prototype.prepareToShow=function(){this._abortHiding();this._abortShowing();if(!this.visible)return this.debug("Showing in "+this.options.delay+"s."),null==this.container&&this._setup(),this.options.group&&c._abortShowingGroup(this.options.group,this),
this.preparingToShow=!0,this._setupObservers("-hidden","-hiding","showing"),this._followMousePosition(),this.options.fixed&&!this.options.target&&(this.initialMousePosition=mousePosition),this.reposition(),this._showTimeoutId=this.setTimeout(this.bound.show,this.options.delay||0)};c.prototype.show=function(){var a=this;this._abortHiding();if(!this.visible){this._clearTimeouts();if(!this._triggerElementExists())return this.deactivate();this.debug("Showing now.");null==this.container&&this._setup();
this.options.group&&c._hideGroup(this.options.group,this);this.visible=!0;this.preparingToShow=!1;null==this.tooltipElement&&this._buildElements();this._updateElementContent();!this.options.ajax||this.loaded&&this.options.cache||this._loadAjax();this._searchAndActivateCloseButtons();this._startEnsureTriggerElement();this.adapter.css(this.container,{zIndex:c.lastZIndex++});this._setupObservers("-hidden","-hiding","-showing","-visible","showing","visible");this.options.fixed&&!this.options.target&&
(this.initialMousePosition=mousePosition);this.reposition();this.adapter.removeClass(this.container,this["class"].hiding);this.adapter.removeClass(this.container,this["class"].hidden);this.adapter.addClass(this.container,this["class"].goingToShow);this.setCss3Style(this.container,{transitionDuration:"0s"});this.defer(function(){var b;if(a.visible&&!a.preparingToHide)return a.adapter.removeClass(a.container,a["class"].goingToShow),a.adapter.addClass(a.container,a["class"].showing),b=0,a.options.showEffect&&
a.options.showEffectDuration&&(b=a.options.showEffectDuration),a.setCss3Style(a.container,{transitionDuration:""+b+"s"}),a._visibilityStateTimeoutId=a.setTimeout(function(){a.adapter.removeClass(a.container,a["class"].showing);return a.adapter.addClass(a.container,a["class"].visible)},b),a._activateFirstInput()});return this._draw()}};c.prototype._abortShowing=function(){if(this.preparingToShow)return this.debug("Aborting showing."),this._clearTimeouts(),this._stopFollowingMousePosition(),this.preparingToShow=
!1,this._setupObservers("-showing","-visible","hiding","hidden")};c.prototype.prepareToHide=function(){this._abortShowing();this._abortHiding();if(this.visible)return this.debug("Hiding in "+this.options.hideDelay+"s"),this.preparingToHide=!0,this._setupObservers("-showing","visible","-hidden","hiding"),this._hideTimeoutId=this.setTimeout(this.bound.hide,this.options.hideDelay)};c.prototype.hide=function(){var a=this;this._abortShowing();if(this.visible&&(this._clearTimeouts(),this.debug("Hiding!"),
this.preparingToHide=this.visible=!1,this._stopEnsureTriggerElement(),this._setupObservers("-showing","-visible","-hiding","-hidden","hiding","hidden"),this.options.fixed||this._stopFollowingMousePosition(),this.container))return this.adapter.removeClass(this.container,this["class"].visible),this.adapter.removeClass(this.container,this["class"].showing),this.adapter.addClass(this.container,this["class"].goingToHide),this.setCss3Style(this.container,{transitionDuration:"0s"}),this.defer(function(){var b;
a.adapter.removeClass(a.container,a["class"].goingToHide);a.adapter.addClass(a.container,a["class"].hiding);b=0;a.options.hideEffect&&a.options.hideEffectDuration&&(b=a.options.hideEffectDuration);a.setCss3Style(a.container,{transitionDuration:""+b+"s"});return a._visibilityStateTimeoutId=a.setTimeout(function(){a.adapter.removeClass(a.container,a["class"].hiding);a.adapter.addClass(a.container,a["class"].hidden);a.setCss3Style(a.container,{transitionDuration:"0s"});if(a.options.removeElementsOnHide)return a.debug("Removing HTML elements."),
a.adapter.remove(a.container),delete a.container,delete a.tooltipElement},b)})};c.prototype._abortHiding=function(){if(this.preparingToHide)return this.debug("Aborting hiding."),this._clearTimeouts(),this.preparingToHide=!1,this._setupObservers("-hiding","showing","visible")};c.prototype.reposition=function(){var a,b,d=this;a=this.getPosition();if(null!=a&&(b=this.options.stem,this.options.containInViewport&&(b=this._ensureViewportContainment(a),a=b.position,b=b.stem),!this._positionsEqual(a,this.currentPosition)))return this.options.stem&&
!b.eql(this.currentStem)&&(this.redraw=!0),this.currentPosition=a,this.currentStem=b,this._draw(),this.adapter.css(this.container,{left:""+a.left+"px",top:""+a.top+"px"}),this.defer(function(){var a;a=d.adapter.unwrap(d.container);a.style.visibility="hidden";return a.style.visibility="visible"})};c.prototype.getPosition=function(a,b,d){var e,c,f,g;if(this.container)return null==a&&(a=this.options.tipJoint),null==b&&(b=this.options.targetJoint),this.options.target?(f=this.adapter.offset(this.options.target),
e=this.adapter.dimensions(this.options.target),b.right?(g=this.adapter.unwrap(this.options.target),f.left=null!=g.getBoundingClientRect?g.getBoundingClientRect().right+(null!=(c=window.pageXOffset)?c:document.body.scrollLeft):f.left+e.width):b.center&&(f.left+=Math.round(e.width/2)),b.bottom?f.top+=e.height:b.middle&&(f.top+=Math.round(e.height/2)),this.options.borderWidth&&(this.options.tipJoint.left&&(f.left+=this.options.borderWidth),this.options.tipJoint.right&&(f.left-=this.options.borderWidth),
this.options.tipJoint.top?f.top+=this.options.borderWidth:this.options.tipJoint.bottom&&(f.top-=this.options.borderWidth))):f=this.initialMousePosition?{top:this.initialMousePosition.y,left:this.initialMousePosition.x}:{top:mousePosition.y,left:mousePosition.x},this.options.autoOffset&&(c=(g=this.options.stem?this.options.stemLength:0)&&this.options.fixed?2:10,b=a.middle&&!this.options.fixed?15:0,e=a.center&&!this.options.fixed?15:0,a.right?f.left-=c+b:a.left&&(f.left+=c+b),a.bottom?f.top-=c+e:a.top&&
(f.top+=c+e),g&&(null==d&&(d=this.options.stem),d.right?f.left-=g:d.left&&(f.left+=g),d.bottom?f.top-=g:d.top&&(f.top+=g))),f.left+=this.options.offset[0],f.top+=this.options.offset[1],a.right?f.left-=this.dimensions.width:a.center&&(f.left-=Math.round(this.dimensions.width/2)),a.bottom?f.top-=this.dimensions.height:a.middle&&(f.top-=Math.round(this.dimensions.height/2)),f};c.prototype._ensureViewportContainment=function(a){var b,d,e,k,f,g,m,l;f=this.options.stem;d={position:a,stem:f};if(!this.visible||
!a)return d;g=this._sticksOut(a);if(!g[0]&&!g[1])return d;l=new c.Joint(this.options.tipJoint);this.options.targetJoint&&(m=new c.Joint(this.options.targetJoint));this.adapter.scrollOffset();b=this.adapter.viewportDimensions();a=!1;if(b.width>=this.dimensions.width&&g[0])switch(a=!0,g[0]){case this.STICKS_OUT_LEFT:l.setHorizontal("left");this.options.targetJoint&&m.setHorizontal("right");break;case this.STICKS_OUT_RIGHT:l.setHorizontal("right"),this.options.targetJoint&&m.setHorizontal("left")}if(b.height>=
this.dimensions.height&&g[1])switch(a=!0,g[1]){case this.STICKS_OUT_TOP:l.setVertical("top");this.options.targetJoint&&m.setVertical("bottom");break;case this.STICKS_OUT_BOTTOM:l.setVertical("bottom"),this.options.targetJoint&&m.setVertical("top")}if(!a)return d;this.options.stem&&(f=l);a=this.getPosition(l,m,f);b=this._sticksOut(a);k=e=!1;b[0]&&b[0]!==g[0]&&(e=!0,l.setHorizontal(this.options.tipJoint.horizontal),this.options.targetJoint&&m.setHorizontal(this.options.targetJoint.horizontal));b[1]&&
b[1]!==g[1]&&(k=!0,l.setVertical(this.options.tipJoint.vertical),this.options.targetJoint&&m.setVertical(this.options.targetJoint.vertical));if(e&&k)return d;if(e||k)this.options.stem&&(f=l),a=this.getPosition(l,m,f);return{position:a,stem:f}};c.prototype._sticksOut=function(a){var b,d;b=this.adapter.scrollOffset();d=this.adapter.viewportDimensions();a=[a.left-b[0],a.top-b[1]];b=[!1,!1];0>a[0]?b[0]=this.STICKS_OUT_LEFT:a[0]+this.dimensions.width>d.width&&(b[0]=this.STICKS_OUT_RIGHT);0>a[1]?b[1]=this.STICKS_OUT_TOP:
a[1]+this.dimensions.height>d.height&&(b[1]=this.STICKS_OUT_BOTTOM);return b};c.prototype._draw=function(){var a,b,d,e,k,f,g,m,l,h,s,t,n,q,r,p=this;if(this.backgroundCanvas&&this.redraw){this.debug("Drawing background.");this.redraw=!1;if(this.currentStem){b=["top","right","bottom","left"];k=0;for(a=b.length;k<a;k++)g=b[k],this.adapter.removeClass(this.container,"stem-"+g);this.adapter.addClass(this.container,"stem-"+this.currentStem.horizontal);this.adapter.addClass(this.container,"stem-"+this.currentStem.vertical)}l=
[0,0];k=[0,0];0<=__indexOf.call(this.options.hideTriggers,"closeButton")&&(m=new c.Joint("top right"===(null!=(f=this.currentStem)?f.toString():void 0)?"top left":"top right"),l=[this.options.closeButtonRadius+this.options.closeButtonOffset[0],this.options.closeButtonRadius+this.options.closeButtonOffset[1]],k=[this.options.closeButtonRadius-this.options.closeButtonOffset[0],this.options.closeButtonRadius-this.options.closeButtonOffset[1]]);f=this.adapter.clone(this.dimensions);g=[0,0];this.options.borderWidth&&
(f.width+=2*this.options.borderWidth,f.height+=2*this.options.borderWidth,g[0]-=this.options.borderWidth,g[1]-=this.options.borderWidth);this.options.shadow&&(f.width+=2*this.options.shadowBlur,f.width+=Math.max(0,this.options.shadowOffset[0]-2*this.options.shadowBlur),f.height+=2*this.options.shadowBlur,f.height+=Math.max(0,this.options.shadowOffset[1]-2*this.options.shadowBlur),g[0]-=Math.max(0,this.options.shadowBlur-this.options.shadowOffset[0]),g[1]-=Math.max(0,this.options.shadowBlur-this.options.shadowOffset[1]));
e=d=b=a=0;this.currentStem&&(this.currentStem.left?a=this.options.stemLength:this.currentStem.right&&(b=this.options.stemLength),this.currentStem.top?d=this.options.stemLength:this.currentStem.bottom&&(e=this.options.stemLength));m&&(m.left?a=Math.max(a,k[0]):m.right&&(b=Math.max(b,k[0])),m.top?d=Math.max(d,k[1]):m.bottom&&(e=Math.max(e,k[1])));f.width+=a+b;f.height+=d+e;g[0]-=a;g[1]-=d;this.currentStem&&this.options.borderWidth&&(k=this._getPathStemMeasures(this.options.stemBase,this.options.stemLength,
this.options.borderWidth),r=k.stemLength,q=k.stemBase);k=this.adapter.unwrap(this.backgroundCanvas);k.width=f.width;k.height=f.height;this.adapter.css(this.backgroundCanvas,{width:""+k.width+"px",height:""+k.height+"px",left:""+g[0]+"px",top:""+g[1]+"px"});h=k.getContext("2d");h.setTransform(1,0,0,1,0,0);h.clearRect(0,0,k.width,k.height);h.beginPath();h.fillStyle=this._getColor(h,this.dimensions,this.options.background,this.options.backgroundGradientHorizontal);h.lineJoin="miter";h.miterLimit=500;
n=this.options.borderWidth/2;this.options.borderWidth?(h.strokeStyle=this.options.borderColor,h.lineWidth=this.options.borderWidth):(r=this.options.stemLength,q=this.options.stemBase);null==q&&(q=0);t=function(a,d,b){b&&h.moveTo(Math.max(q,p.options.borderRadius,l[0])+1-n,-n);if(d)return h.lineTo(a/2-q/2,-n),h.lineTo(a/2,-r-n),h.lineTo(a/2+q/2,-n)};s=function(a,d,b){var e;if(a)return h.lineTo(-q+n,0-n),h.lineTo(r+n,-r-n),h.lineTo(n,q-n);if(d)return d=p.options.closeButtonOffset,a=l[0],0!==b%2&&(d=
[d[1],d[0]],a=l[1]),b=Math.acos(d[1]/p.options.closeButtonRadius),e=Math.acos(d[0]/p.options.closeButtonRadius),h.lineTo(-a+n,-n),h.arc(n-d[0],-n+d[1],p.options.closeButtonRadius,-(Math.PI/2+b),e,!1);h.lineTo(-p.options.borderRadius+n,-n);return h.quadraticCurveTo(n,-n,n,p.options.borderRadius-n)};h.translate(-g[0],-g[1]);h.save();(function(){var a,d,b,e,f,k,g,l,n,q;q=[];d=l=0;for(n=c.positions.length/2;0<=n?l<n:l>n;d=0<=n?++l:--l)a=2*d,f=0===d||3===d?0:p.dimensions.width,k=2>d?0:p.dimensions.height,
g=Math.PI/2*d,b=0===d%2?p.dimensions.width:p.dimensions.height,e=new c.Joint(c.positions[a]),a=new c.Joint(c.positions[a+1]),h.save(),h.translate(f,k),h.rotate(g),t(b,e.eql(p.currentStem),0===d),h.translate(b,0),s(a.eql(p.currentStem),a.eql(m),d),q.push(h.restore());return q})();h.closePath();h.save();this.options.shadow&&(h.shadowColor=this.options.shadowColor,h.shadowBlur=this.options.shadowBlur,h.shadowOffsetX=this.options.shadowOffset[0],h.shadowOffsetY=this.options.shadowOffset[1]);h.fill();
h.restore();this.options.borderWidth&&h.stroke();h.restore();if(m)return function(){var a,d;"top right"===m.toString()?(d=[p.dimensions.width-p.options.closeButtonOffset[0],p.options.closeButtonOffset[1]],a=[d[0]+n,d[1]-n]):(d=[p.options.closeButtonOffset[0],p.options.closeButtonOffset[1]],a=[d[0]-n,d[1]-n]);h.translate(a[0],a[1]);a=p.options.closeButtonCrossSize/2;h.save();h.beginPath();h.strokeStyle=p.options.closeButtonCrossColor;h.lineWidth=p.options.closeButtonCrossLineWidth;h.lineCap="round";
h.moveTo(-a,-a);h.lineTo(a,a);h.stroke();h.beginPath();h.moveTo(a,-a);h.lineTo(-a,a);h.stroke();h.restore();return p.adapter.css(p.closeButtonElement,{left:""+(d[0]-a-p.options.closeButtonLinkOverscan)+"px",top:""+(d[1]-a-p.options.closeButtonLinkOverscan)+"px",width:""+(p.options.closeButtonCrossSize+2*p.options.closeButtonLinkOverscan)+"px",height:""+(p.options.closeButtonCrossSize+2*p.options.closeButtonLinkOverscan)+"px"})}()}};c.prototype._getPathStemMeasures=function(a,b,d){var e;d/=2;a=Math.atan(a/
2/b);e=2*(d/Math.sin(2*a))*Math.cos(a);b=d+b-e;if(0>b)throw Error("Sorry but your stemLength / stemBase ratio is strange.");a=Math.tan(a)*b*2;return{stemLength:b,stemBase:a}};c.prototype._getColor=function(a,b,d,e){var c;null==e&&(e=!1);if("string"===typeof d)return d;a=e?a.createLinearGradient(0,0,b.width,0):a.createLinearGradient(0,0,0,b.height);c=b=0;for(e=d.length;b<e;c=++b)c=d[c],a.addColorStop(c[0],c[1]);return a};c.prototype._searchAndActivateCloseButtons=function(){var a,b,d,e;e=this.adapter.findAll(this.container,
"."+this["class"].close);b=0;for(d=e.length;b<d;b++)a=e[b],this.hideTriggers.push({element:this.adapter.wrap(a),event:"click"});this.currentObservers.showing&&this._setupObservers("-showing","showing");if(this.currentObservers.visible)return this._setupObservers("-visible","visible")};c.prototype._activateFirstInput=function(){var a;a=this.adapter.unwrap(this.adapter.find(this.container,"input, textarea"));return null!=a?"function"===typeof a.focus?a.focus():void 0:void 0};c.prototype._followMousePosition=
function(){if(!this.options.fixed)return c._observeMousePosition(this.bound.reposition)};c.prototype._stopFollowingMousePosition=function(){if(!this.options.fixed)return c._stopObservingMousePosition(this.bound.reposition)};c.prototype._clearShowTimeout=function(){return clearTimeout(this._showTimeoutId)};c.prototype._clearHideTimeout=function(){return clearTimeout(this._hideTimeoutId)};c.prototype._clearTimeouts=function(){clearTimeout(this._visibilityStateTimeoutId);this._clearShowTimeout();return this._clearHideTimeout()};
c.prototype._triggerElementExists=function(){var a;for(a=this.adapter.unwrap(this.triggerElement);a.parentNode;){if("BODY"===a.parentNode.tagName)return!0;a=a.parentNode}return!1};c.prototype._loadAjax=function(){var a=this;if(!this.loading)return this.loaded=!1,this.loading=!0,this.adapter.addClass(this.container,this["class"].loading),this.setContent(""),this.debug("Loading content from "+this.options.ajax),this.adapter.ajax({url:this.options.ajax,method:this.options.ajaxMethod,onSuccess:function(b){a.debug("Loading successful.");
a.adapter.removeClass(a.container,a["class"].loading);return a.setContent(b)},onError:function(b){var d;d=a.options.ajaxErrorMessage;a.debug(d,b);a.setContent(d);return a.adapter.addClass(a.container,a["class"].ajaxError)},onComplete:function(){a.adapter.removeClass(a.container,a["class"].loading);a.loading=!1;a.loaded=!0;a._searchAndActivateCloseButtons();a._activateFirstInput();return a.reposition()}})};c.prototype._ensureTriggerElement=function(){if(!this._triggerElementExists())return this.deactivate(),
this._stopEnsureTriggerElement()};c.prototype._ensureTriggerElementInterval=1E3;c.prototype._startEnsureTriggerElement=function(){var a=this;return this._ensureTriggerElementTimeoutId=setInterval(function(){return a._ensureTriggerElement()},this._ensureTriggerElementInterval)};c.prototype._stopEnsureTriggerElement=function(){return clearInterval(this._ensureTriggerElementTimeoutId)};return c}();vendors=["khtml","ms","o","moz","webkit"];
Opentip.prototype.setCss3Style=function(c,a){var b,d,e,k,f;c=this.adapter.unwrap(c);f=[];for(b in a)__hasProp.call(a,b)&&(d=a[b],null!=c.style[b]?f.push(c.style[b]=d):f.push(function(){var a,f,l;l=[];a=0;for(f=vendors.length;a<f;a++)e=vendors[a],k=""+this.ucfirst(e)+this.ucfirst(b),null!=c.style[k]?l.push(c.style[k]=d):l.push(void 0);return l}.call(this)));return f};Opentip.prototype.defer=function(c){return setTimeout(c,0)};Opentip.prototype.setTimeout=function(c,a){return setTimeout(c,a?1E3*a:0)};
Opentip.prototype.ucfirst=function(c){return null==c?"":c.charAt(0).toUpperCase()+c.slice(1)};Opentip.prototype.dasherize=function(c){return c.replace(/([A-Z])/g,function(a,b){return"-"+b.toLowerCase()})};mousePositionObservers=[];mousePosition={x:0,y:0};mouseMoved=function(c){var a,b,d;mousePosition=Opentip.adapter.mousePosition(c);d=[];a=0;for(b=mousePositionObservers.length;a<b;a++)c=mousePositionObservers[a],d.push(c());return d};
Opentip.followMousePosition=function(){return Opentip.adapter.observe(document.body,"mousemove",mouseMoved)};Opentip._observeMousePosition=function(c){return mousePositionObservers.push(c)};Opentip._stopObservingMousePosition=function(c){var a,b,d,e;e=[];b=0;for(d=mousePositionObservers.length;b<d;b++)a=mousePositionObservers[b],a!==c&&e.push(a);return mousePositionObservers=e};
Opentip.Joint=function(){function c(a){null!=a&&(a instanceof Opentip.Joint&&(a=a.toString()),this.set(a),this)}c.prototype.set=function(a){a=a.toLowerCase();this.setHorizontal(a);this.setVertical(a);return this};c.prototype.setHorizontal=function(a){var b,d,e,c;d=["left","center","right"];e=0;for(c=d.length;e<c;e++)b=d[e],~a.indexOf(b)&&(this.horizontal=b.toLowerCase());null==this.horizontal&&(this.horizontal="center");c=[];a=0;for(e=d.length;a<e;a++)b=d[a],c.push(this[b]=this.horizontal===b?b:void 0);
return c};c.prototype.setVertical=function(a){var b,d,e,c;d=["top","middle","bottom"];e=0;for(c=d.length;e<c;e++)b=d[e],~a.indexOf(b)&&(this.vertical=b.toLowerCase());null==this.vertical&&(this.vertical="middle");c=[];a=0;for(e=d.length;a<e;a++)b=d[a],c.push(this[b]=this.vertical===b?b:void 0);return c};c.prototype.eql=function(a){return null!=a&&this.horizontal===a.horizontal&&this.vertical===a.vertical};c.prototype.flip=function(){var a;a=Opentip.position[this.toString(!0)];this.set(Opentip.positions[(a+
4)%8]);return this};c.prototype.toString=function(a){var b,d;null==a&&(a=!1);d="middle"===this.vertical?"":this.vertical;b="center"===this.horizontal?"":this.horizontal;d&&b&&(b=a?Opentip.prototype.ucfirst(b):" "+b);return""+d+b};return c}();Opentip.prototype._positionsEqual=function(c,a){return null!=c&&null!=a&&c.left===a.left&&c.top===a.top};Opentip.prototype._dimensionsEqual=function(c,a){return null!=c&&null!=a&&c.width===a.width&&c.height===a.height};
Opentip.prototype.debug=function(){var c;c=1<=arguments.length?__slice.call(arguments,0):[];if(Opentip.debug&&null!=("undefined"!==typeof console&&null!==console?console.debug:void 0))return c.unshift("#"+this.id+" |"),console.debug.apply(console,c)};
Opentip.findElements=function(){var c,a,b,d,e,k,f,g,m,l;c=Opentip.adapter;m=c.findAll(document.body,"[data-ot]");l=[];f=0;for(g=m.length;f<g;f++){b=m[f];k={};a=c.data(b,"ot");if(""===a||"true"===a||"yes"===a)a=c.attr(b,"title"),c.attr(b,"title","");a=a||"";for(d in Opentip.styles.standard)if(e=c.data(b,"ot"+Opentip.prototype.ucfirst(d)),null!=e){if("yes"===e||"true"===e||"on"===e)e=!0;else if("no"===e||"false"===e||"off"===e)e=!1;k[d]=e}l.push(new Opentip(b,a,k))}return l};Opentip.version="2.4.6";
Opentip.debug=!1;Opentip.lastId=0;Opentip.lastZIndex=100;Opentip.tips=[];Opentip._abortShowingGroup=function(c,a){var b,d,e,k,f;k=Opentip.tips;f=[];d=0;for(e=k.length;d<e;d++)b=k[d],b!==a&&b.options.group===c?f.push(b._abortShowing()):f.push(void 0);return f};Opentip._hideGroup=function(c,a){var b,d,e,k,f;k=Opentip.tips;f=[];d=0;for(e=k.length;d<e;d++)b=k[d],b!==a&&b.options.group===c?f.push(b.hide()):f.push(void 0);return f};Opentip.adapters={};Opentip.adapter=null;firstAdapter=!0;
Opentip.addAdapter=function(c){Opentip.adapters[c.name]=c;if(firstAdapter)return Opentip.adapter=c,c.domReady(Opentip.findElements),c.domReady(Opentip.followMousePosition),firstAdapter=!1};Opentip.positions="top topRight right bottomRight bottom bottomLeft left topLeft".split(" ");Opentip.position={};_ref=Opentip.positions;i=_i=0;for(_len=_ref.length;_i<_len;i=++_i)position=_ref[i],Opentip.position[position]=i;
Opentip.styles={standard:{"extends":null,title:void 0,escapeTitle:!0,escapeContent:!1,className:"standard",stem:!0,delay:null,hideDelay:0.1,fixed:!1,showOn:"mouseover",hideTrigger:"trigger",hideTriggers:[],hideOn:null,removeElementsOnHide:!1,offset:[0,0],containInViewport:!0,autoOffset:!0,showEffect:"appear",hideEffect:"fade",showEffectDuration:0.3,hideEffectDuration:0.2,stemLength:5,stemBase:8,tipJoint:"top left",target:null,targetJoint:null,cache:!0,ajax:!1,ajaxMethod:"GET",ajaxErrorMessage:"There was a problem downloading the content.",
group:null,style:null,background:"#fff18f",backgroundGradientHorizontal:!1,closeButtonOffset:[5,5],closeButtonRadius:7,closeButtonCrossSize:4,closeButtonCrossColor:"#d2c35b",closeButtonCrossLineWidth:1.5,closeButtonLinkOverscan:6,borderRadius:5,borderWidth:1,borderColor:"#f2e37b",shadow:!0,shadowBlur:10,shadowOffset:[3,3],shadowColor:"rgba(0, 0, 0, 0.1)"},glass:{"extends":"standard",className:"glass",background:[[0,"rgba(252, 252, 252, 0.8)"],[0.5,"rgba(255, 255, 255, 0.8)"],[0.5,"rgba(250, 250, 250, 0.9)"],
[1,"rgba(245, 245, 245, 0.9)"]],borderColor:"#eee",closeButtonCrossColor:"rgba(0, 0, 0, 0.2)",borderRadius:15,closeButtonRadius:10,closeButtonOffset:[8,8]},dark:{"extends":"standard",className:"dark",borderRadius:13,borderColor:"#444",closeButtonCrossColor:"rgba(240, 240, 240, 1)",shadowColor:"rgba(0, 0, 0, 0.3)",shadowOffset:[2,2],background:[[0,"rgba(30, 30, 30, 0.7)"],[0.5,"rgba(30, 30, 30, 0.8)"],[0.5,"rgba(10, 10, 10, 0.8)"],[1,"rgba(10, 10, 10, 0.9)"]]},alert:{"extends":"standard",className:"alert",
borderRadius:1,borderColor:"#AE0D11",closeButtonCrossColor:"rgba(255, 255, 255, 1)",shadowColor:"rgba(0, 0, 0, 0.3)",shadowOffset:[2,2],background:[[0,"rgba(203, 15, 19, 0.7)"],[0.5,"rgba(203, 15, 19, 0.8)"],[0.5,"rgba(189, 14, 18, 0.8)"],[1,"rgba(179, 14, 17, 0.9)"]]}};Opentip.defaultStyle="standard";"undefined"!==typeof module&&null!==module?module.exports=Opentip:window.Opentip=Opentip;__slice=[].slice;
(function(){var c,a;Element.addMethods({addTip:function(a,d,e,c){return new Opentip(a,d,e,c)}});a=function(a){return a instanceof Array||null!=a&&"number"===typeof a.length&&"function"===typeof a.item&&"function"===typeof a.nextNode&&"function"===typeof a.reset?!0:!1};c=function(){function b(){}b.prototype.name="prototype";b.prototype.domReady=function(a){return document.loaded?a():$(document).observe("dom:loaded",a)};b.prototype.create=function(a){return(new Element("div")).update(a).childElements()};
b.prototype.wrap=function(d){if(a(d)){if(1<d.length)throw Error("Multiple elements provided.");d=this.unwrap(d)}else"string"===typeof d&&(d=$$(d)[0]);return $(d)};b.prototype.unwrap=function(d){return a(d)?d[0]:d};b.prototype.tagName=function(a){return this.unwrap(a).tagName};b.prototype.attr=function(){var a,b,c;b=arguments[0];a=2<=arguments.length?__slice.call(arguments,1):[];return 1===a.length?this.wrap(b).readAttribute(a[0]):(c=this.wrap(b)).writeAttribute.apply(c,a)};b.prototype.data=function(a,
b,c){var f;this.wrap(a);if(2<arguments.length)return a.store(b,c);f=a.readAttribute("data-"+b.underscore().dasherize());return null!=f?f:a.retrieve(b)};b.prototype.find=function(a,b){return this.wrap(a).select(b)[0]};b.prototype.findAll=function(a,b){return this.wrap(a).select(b)};b.prototype.update=function(a,b,c){return this.wrap(a).update(c?b.escapeHTML():b)};b.prototype.append=function(a,b){return this.wrap(a).insert(this.wrap(b))};b.prototype.remove=function(a){return this.wrap(a).remove()};
b.prototype.addClass=function(a,b){return this.wrap(a).addClassName(b)};b.prototype.removeClass=function(a,b){return this.wrap(a).removeClassName(b)};b.prototype.css=function(a,b){return this.wrap(a).setStyle(b)};b.prototype.dimensions=function(a){return this.wrap(a).getDimensions()};b.prototype.scrollOffset=function(){var a;a=document.viewport.getScrollOffsets();return[a.left,a.top]};b.prototype.viewportDimensions=function(){return document.viewport.getDimensions()};b.prototype.mousePosition=function(a){return null==
a?null:{x:Event.pointerX(a),y:Event.pointerY(a)}};b.prototype.offset=function(a){a=this.wrap(a).cumulativeOffset();return{left:a.left,top:a.top}};b.prototype.observe=function(a,b,c){return Event.observe(this.wrap(a),b,c)};b.prototype.stopObserving=function(a,b,c){return Event.stopObserving(this.wrap(a),b,c)};b.prototype.ajax=function(a){var b,c;if(null==a.url)throw Error("No url provided");return new Ajax.Request(a.url,{method:null!=(b=null!=(c=a.method)?c.toUpperCase():void 0)?b:"GET",onSuccess:function(b){return"function"===
typeof a.onSuccess?a.onSuccess(b.responseText):void 0},onFailure:function(b){return"function"===typeof a.onError?a.onError("Server responded with status "+b.status):void 0},onComplete:function(){return"function"===typeof a.onComplete?a.onComplete():void 0}})};b.prototype.clone=function(a){return Object.clone(a)};b.prototype.extend=function(){var a,b,c,f,g;c=arguments[0];b=2<=arguments.length?__slice.call(arguments,1):[];f=0;for(g=b.length;f<g;f++)a=b[f],Object.extend(c,a);return c};return b}();return Opentip.addAdapter(new c)})();