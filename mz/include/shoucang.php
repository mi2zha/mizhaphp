<script type="text/javascript">
<!--
function addfavorite(){
		var title="mizha";
		var url= "http://www.mizha.org";
		if (window.sidebar){window.sidebar.addPanel(title, url,"");}
		else if( document.all ){window.external.AddFavorite( url, title);}
		else if( window.opera && window.print ){return true;}

}
function sethomepage(){
	if(document.all)
	{
			document.body.style.behavior="url(#default#homepage)";
			document.body.setHomePage("http://www.mizha.org");
	}
	else if(window.sidebar)
	{
			if(window.netscape)
			{
				try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}
				catch(e){alert("该操作被浏览器拒绝，想启用该功能，请在地址栏内输入about:config,然后将项signed.applets.codebase_principal_support值该为true");}
			}
			var prefs=Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref("browser.startup.homepage","http://tz.game5.com");
	}
}
//-->
</script>