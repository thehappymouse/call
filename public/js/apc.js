/*
apc文件上传代码
*/

ApcFile = function(){
	var that = this;
	this.url;
	this.progress_key;

	var xmlHttp;  
	var json_current;  
	var json_total;  
	var precent;  
	 
	var loop = 0;  
	var last = 0;  
	var Try = {  
	    these: function() {  
	        var returnValue;  
	        for (var i = 0; i < arguments.length; i++) {  
	            var lambda = arguments[i];  
	            try {  
	                returnValue = lambda();  
	                break;  
	            } catch (e) {}  
	        }  
	        return returnValue;  
	    }  
	}  
	  
	this.createXHR = function () {  
	    return Try.these (  
	        function() {return new XMLHttpRequest()},  
	        function() {return new ActiveXObject('Msxml2.XMLHTTP')},  
	        function() {return new ActiveXObject('Microsoft.XMLHTTP')}  
	    ) || false;  
	}  
	  
	var xmlHttp;  
	  
	this.sendURL = function () {  
	    xmlHttp = that.createXHR();  
	    var url = that.url+"?progress_key="+that.progress_key;
	    xmlHttp.onreadystatechange = that.doHttpReadyStateChange;  
	    xmlHttp.open("GET", url, true);  
	    xmlHttp.send(null);     
	}  
	  
	this.doHttpReadyStateChange = function () 
	{  
	    if (xmlHttp.readyState == 4) {  
	  
	        // status为getprogress中json_encode传过来的  
	        var status = xmlHttp.responseText;  
	        // 解析status,这样，获取对象就可以用json.something获得  
	        var json = eval("(" + status + ")");  
	        json_current = parseInt(json.current);  
	        json_total = parseInt(json.total);  
	        precent = parseInt(json_current/json_total * 100); 
	  
	        document.getElementById("progressinner").style.width = precent+"%";  
	        document.getElementById("showNum").innerHTML = "文件已上传"+precent+"%";  
	       // document.getElementById("showInfo").innerHTML = status;  
	  		//console.log(precent);
	        if ( precent < 100) {  
	            setTimeout(that.getProgress, 100);  
	        } else {
	        	document.getElementById("showNum").innerHTML = "文件上传完成"; 
	        	//document.getElementById("progressouter").style.display="none";
	        }
	        	

	        
	        
	    }  
	}  
	  
	this.getProgress = function () 
	{  
	    that.sendURL();
	}  
	  
	var interval;  
	this.startProgress = function () 
	{  
	
	    document.getElementById("progressouter").style.display="block";  
	    setTimeout(that.getProgress, 100);  
	}
}

