function clean(text)  {
    return decodeURI(text.replace(/\\\//g, "/"));
}

function searchFacebook(url) {
    // var url = "http://kbhkilder.dk/hack4dk/api/public/1/?type=posts&callback=?";
    $.getJSON(url, function (response) {
        $.each(response.data, function (idx, obj) {
            if (obj.picture && obj.message) {
                var terms= obj.message.split(" ");
                var q = terms[0];
                var li= $("<li><table><tr><td>" +
                     "<img  src=" + clean(obj.picture) + "></img></td></tr><tr><td>" +
                clean(obj.message) + "</td></tr></li>");
                $("#feed").append(li);
                li.on('click', function (evt) {
                    searchNatmus(q);
                })
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

searchFacebook("../testdata.json");

//hack: startup a search to show that it works (unrelated)
searchNatmus("Plakater");
