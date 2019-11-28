var root_dir = "/";

$("script").each(function(index) {    
    var host = window.location.host;    
    var src = $("script")[index]['src'];
    
    if(src.indexOf(host) >= 0) {
        src = src.split(host)[1];
        
        if(src.indexOf("docRoot.js") >= 0) {            
            var srcSplit = src.split("/admin")[0];
            root_dir = srcSplit + "/";
        }
    }
});