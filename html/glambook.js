
function clean(text)  {
    return decodeURI(text.replace(/\\\//g, "/"));
}

function hack(x) {
  var r = /\\u([\d\w]{4})/gi;
  x = x.replace(r, function (match, grp) {
      return String.fromCharCode(parseInt(grp, 16)); }  );
  x = unescape(x);
  return x;
}

function searchFacebook(url) {
    $.getJSON(url, function (response) {
        $.each(response, function (idx, obj) {
          if (obj.picture && obj.link) {
              var li;
              if (obj.link) {
                console.log(obj);
                li = $('<div class="panel panel-default"><div class="panel-body">'+
                  '<img src="'+ clean(obj.link) + '" style="margin-right:50px; width:100px; display:inline" class="img-responsive" alt="Responsive image"> <span style="font-size:60%">' + hack(obj.picture).substring(0,50) + '...</span><button type="button" class="btn btn-success pull-right"><a href="index.html">Vælg</a></button> </div></div>');
              } else  {
                //li = $('<li><table><tr><td>&nbsp;</td></tr><tr><td>' + hack(obj.picture) + '</td></tr></table></li>');
              }
            $("#fblist").append(li);
        }
      });
    });
};
