/**
 * Description: Navigation Bar
 * Author:Aaron
 * Email:yunlong.ye@longshine.com
 * Date:2006-4-10
 */

function NaviBar(id,tabNum){
	this.id = id;
	NaviBar.allbars[id]=this;
	this.activeTab = null;
	this.activeItem = null;
	this.lastHighlightTab = null;
	this.lastShowMenu = null;
	this.isOverMenu = false;
	this.isOverTab = false;
	this.tabNum = tabNum;
	this.text = "test";
	this.loadNodeURL = "";
	this.imageBase = "";
	this.loadOnClick = true;
	this.selectedItem = null;
	
	this.scrollType = "auto";
	this.curVisibleTab = 0;
	this.showTabNum = 0;
	this.oBar = document.getElementById(this.id+"_NaviTabs");
	this.tabWidth = this.oBar.firstChild.offsetWidth;
	
	this.menuScrollType = this.scrollType;
	this.startVisibleMenu = 0;
	this.endVisibleMenu = 0;

	
	var oThis = this;
	this._onHoverTab = function(e){
		oThis.onHoverTab(e);
	}
	this._onMouseOutTab = function(e){
		oThis.onMouseOutTab(e);
	}
	this._onHoverMenu = function(e){
		oThis.onHoverMenu(e);
	}
	this._onMouseOutMenu = function(e){
		oThis.onMouseOutMenu(e);
	}
	this._onHoverMenuBg = function(e){
		oThis.onHoverMenuBg(e);
	}
	this._onMouseOutMenuBg = function(e){
		oThis.onMouseOutMenuBg(e);
	}
	this._onLeftScrollTab = function(e){
		oThis.leftScrollTab(e);
	}
	this._onRightScrollTab = function(e){
		oThis.rightScrollTab(e);
	}
	this._onLeftScrollMenu = function(e){
		oThis.leftScrollMenu(e);
	}
	this._onRightScrollMenu = function(e){
		oThis.rightScrollMenu(e);
	}
	this._onSetTab = function(e){
		oThis.setTab(e.srcElement.uid);
	}
	this._initScrollTabState = function(){
		oThis.initScrollTabState();
	}
	this._initScrollMenuState = function(){
		oThis.initScrollMenuState();
	}
	this._refreshScrollState = function(){
		oThis.refreshScrollState();
	}
  var win = document.defaultView || document.parentWindow;
	this._onunload = function () {
		oThis.destroy();
	};
	if (win && typeof win.attachEvent != "undefined") {
		win.attachEvent("onunload", this._onunload);
	}	
}
NaviBar.allbars={};
NaviBar.prototype.destroy = function (){
	//this.detachScrollEvent();
	this.detachTabEvent();
	var win = document.parentWindow;
	if (win && typeof win.detachEvent != "undefined") {
		win.detachEvent("onunload", this._onunload);
	}
	this.lastHighlightTab = null;
	this.lastShowMenu = null;
	this._onHoverTab = null;
	this._onMouseOutTab = null;
	this._onHoverMenu = null;
	this._onMouseOutMenu = null;
	this._onHoverMenuBg = null;
	this._onMouseOutMenuBg = null;
	this._onLeftScrollTab = null;
	this._onRightScrollTab = null;
	this._onLeftScrollMenu = null;
	this._onRightScrollMenu = null;
	this._onSetTab = null;
	this._initScrollTabState = null;
	this._initScrollMenuState = null;	
}
NaviBar.prototype.setTab = function setTab(id){
	var tabobj = document.getElementById(this.id+"_tab_"+ id);
	if(this.activeTab != null && tabobj){
		if(this.activeTab == id){
			var oldActiveItem = document.getElementById(this.id+"_item_"+this.activeTab+"_"+this.activeItem);
			if(oldActiveItem){
				oldActiveItem.className = "";
			}
			return;
		}
		var oldActiveTab = document.getElementById(this.id+"_tab_"+ this.activeTab);
		var oldActiveMenu = document.getElementById(this.id+"_menu_"+this.activeTab);
		var oldActiveItem = document.getElementById(this.id+"_item_"+this.activeTab+"_"+this.activeItem);
		if (oldActiveTab){
			oldActiveTab.className = "";
		}
		if(oldActiveMenu){
			oldActiveMenu.className = "";
		}
		if(oldActiveItem){
			oldActiveItem.className = "";
		}
	}
	if (tabobj) {
		tabobj.className="active";
		this.activeTab = id; 
	}else{
		this.activeTab = ""; 
	}

	var menuobj = document.getElementById(this.id+"_menu_"+id);
	if (menuobj){
		menuobj.className="active";
	}
	this.selectedItem = document.getElementById(this.id+"_item_"+this.activeTab+"_"+this.activeItem);
	this.attachTabEvent(); 
}

NaviBar.prototype.showCurSel = function showCurSel(idtab, id){
	var menuitem=document.getElementById(this.id+"_item_"+idtab+ "_" + id);
	if (menuitem){
		menuitem.className="active";
		this.activeItem = id;
	}
}

NaviBar.prototype.locateTab = function locateTab(path){
	if(path.length>0){
		this.setTab(path[0]); 
		this.showCurSel(path[0], path[1]);
	}
}

NaviBar.prototype.highlightTab = function highlightTab(tab)
{
	if (this.lastHighlightTab != tab)
	{
		if (this.lastHighlightTab)
		{
			if(this.lastHighlightTab.className != "active"){
				this.lastHighlightTab.className = this.lastHighlightTab.savedClassName;
			}
		}
		if (tab.id != this.id+"_tab_"+this.activeTab)
		{
			tab.savedClassName = tab.className;
			tab.className="highlight"; 
			this.lastHighlightTab = tab;
		}
	}
}

NaviBar.prototype.onHighlightTab = function onHighlightTab(oTab){
	return function (){
		highlightTab(oTab);
	}
}

NaviBar.prototype.showMenu = function showMenu(id) 
{
	if (this.lastShowMenu)
	{
		if (this.lastShowMenu.id == id)
		{
			return;
		}
		if ((id==null || id==""))
		{
			if(this.lastHighlightTab) this.lastHighlightTab.className = "";
			if(this.lastShowMenu) this.lastShowMenu.style.display = "none";
			return;
		}
	}
	var menuobj = document.getElementById(this.id+"_menu_"+id);
	var tabobj = document.getElementById(this.id+"_tab_"+id);
	if (menuobj){
		if (this.lastShowMenu)
		{
			this.lastShowMenu.style.display = "none"; 
		}
		menuobj.style.display = "inline";
		this.lastShowMenu = menuobj;
		this.startVisibleMenu = 0;
		this.initScrollMenuState();
		if (tabobj)
		{
			this.highlightTab(tabobj);
		}
	}	

	var bg = document.getElementById(this.id+"_menu"); 
	if (bg)
	{
		if (id == this.activeTab)
		{
//			bg.style.background = "url(images/bg_line.gif) repeat-x";
			bg.onmouseover = null;
			bg.onmouseout = null;
		}
		else {
//			bg.style.background = "url(images/bg_line_hover.gif) repeat-x";
			bg.onmouseover = this._onHoverMenuBg;
			bg.onmouseout = this._onMouseOutMenuBg;
		}
	}
}

NaviBar.prototype.onHoverTab = function onHoverTab(e) 
{
	this.isOverTab = true; 
	this.showMenu(e.srcElement.uid); 
}

NaviBar.prototype.onMouseOutTab = function onMouseOutTab(e)
{
	setTimeout("NaviBar.allbars['"+this.id+"'].delayRecoverMenu()", 400);
}

NaviBar.prototype.onHoverMenu = function onHoverMenu(e)
{
	this.isOverMenu = true; 
}

NaviBar.prototype.onMouseOutMenu = function onMouseOutMenu(e)
{
	this.isOverMenu = false;
}

NaviBar.prototype.delayRecoverMenu = function delayRecoverMenu()
{
	if (this.isOverMenu || this.isOverTab) {
		// don't recover
	}
	else {
		this.showMenu(this.activeTab); 
		this.isOverMenu = false; 
		this.isOverTab = false;
		this.lastHighlightTab = null; 
	}
}

NaviBar.prototype.onHoverMenuBg = function onHoverMenuBg(e)
{
	this.isOverTab = true;
}
NaviBar.prototype.onMouseOutMenuBg = function onMouseOutMenuBg(e)
{
	if(this.isOverTab){
		this.isOverTab = false;
		var sFunc = "NaviBar.allbars['"+this.id+"'].delayRecoverMenu()";
		setTimeout(sFunc, 200);
	}
}

NaviBar.prototype.getSelectedTitlePath = function(seperator){
	var tabobj = document.getElementById(this.id+"_tab_"+ this.activeTab);
	var itemobj = document.getElementById(this.id+"_item_"+this.activeTab+"_"+this.activeItem);
	return tabobj.innerText + seperator + itemobj.innerText;
}
NaviBar.prototype.attachTabEvent = function attachTabEvent()
{
	if (this.activeTab)
	{
		this.showMenu(this.activeTab); 
	}
	for (var i=1 ; i <= this.tabNum; i++ )
	{
		var tab = document.getElementById(this.id+"_tab_" + i); 
		if (tab != null)
		{
			tab.uid = i;
			var barName = this.id;
			if(this.loadOnClick){
				if(tab.loadLater){
					tab.attachEvent("onclick",loadXmlMenuHandler(tab.identification,tab,barName));
				}
				tab.attachEvent("onclick",this._onSetTab);
			}else{
				if(tab.loadLater){
					tab.attachEvent("onmouseover",loadXmlMenuHandler(tab.identification,tab,barName));
				}
				tab.attachEvent("onmouseover", this._onHoverTab);
				tab.attachEvent("onmouseout",this._onMouseOutTab);
			}
		}
		var menu = document.getElementById(this.id+"_menu_" + i); 
		if (menu != null)
		{
			menu.attachEvent("onmouseover",this._onHoverMenu);
			menu.attachEvent("onmouseout",this._onMouseOutMenu);
		}
	}
}

NaviBar.prototype.detachTabEvent = function detachTabEvent()
{
	if (this.activeTab)
	{
		this.showMenu(this.activeTab); 
	}
	for (var i=1 ; i <= this.tabNum; i++ )
	{
		var tab = document.getElementById(this.id+"_tab_" + i); 
		if (tab != null)
		{
			tab.detachEvent("onmouseover", this._onHoverTab);
			tab.detachEvent("onmouseout",this._onMouseOutTab);
		}
		var menu = document.getElementById(this.id+"_menu_" + i); 
		if (menu != null)
		{
			menu.detachEvent("onmouseover",this._onHoverMenu); 
			menu.detachEvent("onmouseout",this._onMouseOutMenu);
		}
	}
}

NaviBar.prototype.init = function(){
	if(this.tabWidth == 0){
		this.tabWidth = this.oBar.childNodes[this.curVisibleTab].offsetWidth;
	}
	this.menuScrollType = this.scrollType;	
	//this.detachScrollEvent();
	this.attachScrollEvent();
	this.refreshScrollState();
}

NaviBar.prototype.refreshScrollState = function(){
	this.initScrollTabState();
	this.initScrollMenuState();	
}

NaviBar.prototype.attachScrollEvent = function(){
	if(this.scrollType != "none"){
		document.getElementById(this.id+"_leftTabBtn").attachEvent("onclick",this._onLeftScrollTab);
		document.getElementById(this.id+"_rightTabBtn").attachEvent("onclick",this._onRightScrollTab);
		document.getElementById(this.id+"_leftMenuBtn").attachEvent("onclick",this._onLeftScrollMenu);
		document.getElementById(this.id+"_rightMenuBtn").attachEvent("onclick",this._onRightScrollMenu);	
	}
}

NaviBar.prototype.detachScrollEvent = function(){
    /*if(this.scrollType != "none"){
		document.getElementById(this.id+"_leftTabBtn").detachEvent("onclick",this._onLeftScrollTab);
		document.getElementById(this.id+"_rightTabBtn").detachEvent("onclick",this._onRightScrollTab);
		document.getElementById(this.id+"_leftMenuBtn").detachEvent("onclick",this._onLeftScrollMenu);
		document.getElementById(this.id+"_rightMenuBtn").detachEvent("onclick",this._onRightScrollMenu);	
	}	*/
}

NaviBar.prototype.leftScrollTab = function(e){
	if(this.curVisibleTab > 0){
		this.curVisibleTab--;
		if(this.curVisibleTab + this.showTabNum < this.oBar.childNodes.length){
			this.oBar.childNodes[this.curVisibleTab + this.showTabNum].style.display = "none";
		}
		this.oBar.childNodes[this.curVisibleTab].style.display = "block";
		this.checkTabBtn();
	}		
}

NaviBar.prototype.rightScrollTab = function(e){
	if(this.curVisibleTab >= 0 && (this.curVisibleTab+this.showTabNum) < this.oBar.childNodes.length){
		this.oBar.childNodes[this.curVisibleTab].style.display = "none";		
		this.oBar.childNodes[this.curVisibleTab+this.showTabNum].style.display = "block";
		this.curVisibleTab++;
		this.checkTabBtn();
	}	
}

NaviBar.prototype.leftScrollMenu = function(e){
	if(this.startVisibleMenu > 0){
		this.startVisibleMenu--;
		this.lastShowMenu.childNodes[this.startVisibleMenu].style.display = "block";
		var menuWidth = 0;
		var oMenu = this.lastShowMenu.parentNode;
		var menuSpace = oMenu.lastChild.offsetLeft - this.lastShowMenu.offsetLeft;
		var newEndMenu = -1;
		for(var i = this.startVisibleMenu; i <= this.endVisibleMenu; i++){
			menuWidth += this.lastShowMenu.childNodes[i].offsetWidth;
			if(menuWidth > menuSpace){
				newEndMenu = i;
				break;
			}
		}
		if(newEndMenu >= 0){
			for(var i = newEndMenu; i <= this.endVisibleMenu; i++){
				this.lastShowMenu.childNodes[i].style.display = "none";
			}
			this.endVisibleMenu = newEndMenu - 1;
		}
		this.checkMenuBtn();
	}
}

NaviBar.prototype.rightScrollMenu = function(e){
	if(this.startVisibleMenu >= 0 && (this.endVisibleMenu < this.lastShowMenu.childNodes.length-1)){
		this.endVisibleMenu++;
		this.lastShowMenu.childNodes[this.endVisibleMenu].style.display = "block";
		var menuWidth = 0;
		var oMenu = this.lastShowMenu.parentNode;
		var menuSpace = oMenu.lastChild.offsetLeft - this.lastShowMenu.offsetLeft;
		var newStartMenu = -1;
		for(var i = this.endVisibleMenu; i >= this.startVisibleMenu; i--){
			menuWidth += this.lastShowMenu.childNodes[i].offsetWidth;
			if(menuWidth > menuSpace){
				newStartMenu = i;
				break;
			}
		}
		if(newStartMenu >= 0){
			for(var i = newStartMenu; i >= this.startVisibleMenu; i--){
				this.lastShowMenu.childNodes[i].style.display = "none";
			}
			this.startVisibleMenu = newStartMenu + 1;
		}else{
			this.startVisibleMen = 0;
		}
		this.checkMenuBtn();
	}
}

NaviBar.prototype.checkMenuBtn = function(){
	var oLeftMenuBtn = document.getElementById(this.id + "_leftMenuBtn");
	var oRightMenuBtn = document.getElementById(this.id + "_rightMenuBtn");
	if(this.startVisibleMenu <= 0){
		oLeftMenuBtn.disabled = true;
	}else{
		oLeftMenuBtn.disabled = false;
	}
	if(this.lastShowMenu){
		if(this.endVisibleMenu >= this.lastShowMenu.childNodes.length-1){
			oRightMenuBtn.disabled = true;
		}else{
			oRightMenuBtn.disabled = false;
		}
	}else{
		oRightMenuBtn.disabled = true;
	}
	if(this.menuScrollType == "none"){
		oLeftMenuBtn.style.visibility = "hidden";
		oRightMenuBtn.style.visibility = "hidden";
	}else	if(this.menuScrollType == "auto"){
		if(oLeftMenuBtn.disabled && oRightMenuBtn.disabled){
			oLeftMenuBtn.style.visibility = "hidden";
			oRightMenuBtn.style.visibility = "hidden";
		}else{
			oLeftMenuBtn.style.visibility = "visible";
			oRightMenuBtn.style.visibility = "visible";			
		}
	}
}

NaviBar.prototype.initScrollMenuState = function(){
	if(this.lastShowMenu){
		var oMenu = this.lastShowMenu.parentNode;		
		var menuSpace = oMenu.lastChild.offsetLeft - this.lastShowMenu.offsetLeft-6;
		var showMwnu = this.lastShowMenu.childNodes;		
		for(var i = this.startVisibleMenu; i < showMwnu.length; i++){
			showMwnu[i].style.display = "block";		
		}	
		var menuWidth = 0;
		var newEndMenu = -1;
		for(var i = this.startVisibleMenu; i < this.lastShowMenu.childNodes.length; i++){
			menuWidth += this.lastShowMenu.childNodes[i].offsetWidth;
			if(menuWidth >= menuSpace){
				newEndMenu = i;
				break;
			}
		}
		if(newEndMenu >= 0){
			for(var i = newEndMenu; i < this.lastShowMenu.childNodes.length; i++){
				this.lastShowMenu.childNodes[i].style.display = "none";
			}
			this.endVisibleMenu = newEndMenu-1;
		}else{
			this.endVisibleMenu = this.lastShowMenu.childNodes.length-1;
		}
		this.lastShowMenu.style.display = "none";//to solve IE bug
		this.lastShowMenu.style.display = "block";
	}
	this.checkMenuBtn();
	
}

NaviBar.prototype.checkTabBtn = function(){
	var oLeftBtn = document.getElementById(this.id + "_leftTabBtn");
	var oRightBtn = document.getElementById(this.id + "_rightTabBtn");
	if(this.curVisibleTab + this.showTabNum >= this.oBar.childNodes.length){
		oRightBtn.disabled = true;
	}else{
		oRightBtn.disabled = false;
	}
	if(this.curVisibleTab <= 0){
		oLeftBtn.disabled = true;
	}else{
		oLeftBtn.disabled = false;
	}
	if(this.scrollType == "none"){
		oLeftBtn.style.display = "none";
		oRightBtn.style.display = "none";				
	}
	if(this.scrollType == "auto"){
		if(oLeftBtn.disabled && oRightBtn.disabled){
			oLeftBtn.style.display = "none";
			oRightBtn.style.display = "none";		
		}else{
			oLeftBtn.style.display = "block";
			oRightBtn.style.display = "block";		
		}
	}
}

NaviBar.prototype.initScrollTabState = function(){
	for(var i = this.curVisibleTab; i < this.oBar.childNodes.length; i++){
		this.oBar.childNodes[i].style.display = "block";		
	}	
	var barWidth = this.oBar.nextSibling.offsetLeft-this.oBar.offsetLeft;
	if(this.tabWidth != 0){
		this.showTabNum = Math.floor(barWidth/this.tabWidth);
	}else{
		this.showTabNum = 1;
	}
	for(var i = this.showTabNum + this.curVisibleTab; i < this.oBar.childNodes.length; i ++){
		this.oBar.childNodes[i].style.display = "none";
	}	
	this.checkTabBtn();
}

var loadXmlMenuHandler = function(sSrc, oTab, sNaviBarId){
	return function (){
		_startLoadXmlMenu(sSrc, oTab, sNaviBarId);
	}
}

function _startLoadXmlMenu(sSrc, oTab, sNaviBarId) {
  if (oTab.loading || oTab.loaded)
    return;
  oTab.loading = true;
  setJsrContextParams("getSubMenuItem","naviBarId",sNaviBarId);
  setJsrContextParams("getSubMenuItem","index",oTab.uid);
  setJsrContextParams("getSubMenuItem","imageBase",NaviBar.allbars[sNaviBarId].imageBase);
  id = jsrsExecute(NaviBar.allbars[sNaviBarId].loadNodeURL
    ,null,"getSubMenuItem",[sSrc,'jsParentTab']);
	clearJsrContextParams("getSubMenuItem");
  if(!id) return;
  var contextObj = jsrsContextPool[id];
  contextObj.callback = function(returnScript){
      _xmlMenuLoaded(returnScript,oTab,sNaviBarId);
  };
  contextObj.errorhandler =  function(error,contextPath){
  	oTab.loading = false;
    contextObj._errorhandler(error,contextPath);
  };
}

function _xmlMenuLoaded(sCreateNodeScript,oParentTab,sNaviBarId) {
  if (oParentTab.loaded)
    return;
  oParentTab.loaded = true;
  oParentTab.loading = false;
  if( sCreateNodeScript == null) {
    alert("Load failure");
    return;
  }
	var contNode = document.getElementById(sNaviBarId+"_menu_"+oParentTab.uid);   
  if(sCreateNodeScript.indexOf("javascript:") == 0){
  	eval(" var func = function(){"+sCreateNodeScript+"}");
  	oParentTab.attachEvent("onclick",func);
  	contNode.innerHTML = "";
  	eval(sCreateNodeScript);
  	return;
  }else{
  	contNode.innerHTML = sCreateNodeScript;
	}
}