
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
                li = $('<li><table><tr><td><img src="'+ clean(obj.link) + '"></td></tr><tr><td>' + hack(obj.picture) + '</td></tr></table></li>');
              } else  {
                li = $('<li><table><tr><td>&nbsp;</td></tr><tr><td>' + hack(obj.picture) + '</td></tr></table></li>');
              }
            $("#feed").append(li);
        }
      });
    });
};

function searchNatmus(q) {
   $("#images").html("");
    var results = [];
    var nm = new CIPClient(NatMusConfig);
    nm.session_open(CIP_USERNAME, CIP_PASSWORD,
        function () {
            nm.get_catalogs(function (catalogs) {
                console.log("catalogs:%o", catalogs);
                catalogs[3].get_tables(function (tables) { //FHM
                tables[0].search(q, function (result) {
                    do {
                        asset_objects = result.get(5);
                        var i=0;
                        for (a in asset_objects) {
                            i=i+1;
                            var asset = asset_objects[a];
                            console.log("asset:%o", asset);
                            var url = asset.get_thumbnail_url({ size: 150 });
                            var link = url.replace(/\/CIP\/preview\/thumbnail/, "");
                            link = link.replace(/\?size=150/, "");
                            var li= $("<li><a href=" + link +"><img width='400' src=" +
                                   url + "></img></a></li>");
                            $("#images").append(li);
                           results.push(url);
                        }
                 } while (asset_objects.length >0 && i<5);
             });
          });
      });
    });
};

searchFacebook("http://kbhkilder.dk/hack4dk/api/public/1/?type=posts&callback=?");
//hack: startup a search to show that it works (unrelated)
//searchNatmus("Plakater");
