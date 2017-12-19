var google_maps;
var companies;
var map_iw_template;

var googleMap;
var infoWindow;
var markerCluster;

initMap = function () {
    googleMap = new google.maps.Map(document.getElementById('google-map'), google_maps['map_options']);
    if (google_maps['single_info_window']) infoWindow = new google.maps.InfoWindow(google_maps['iw_options']);

    markerCluster = new MarkerClusterer(googleMap, [], google_maps['mc_options']);

    if (companies) {
        var bounds = new google.maps.LatLngBounds();
        var iw_template;
        var iw_content = "";
        var hasTemplate = false;

        if (map_iw_template) {
            iw_template = Twig.twig({
                'data': map_iw_template
            });
            hasTemplate = true;
        }

        companies.forEach(function (company) {
            if (hasTemplate) {
                iw_content = iw_template.render({
                    'company': company
                });
            }

            company.marker = addMarker(company.point, iw_content, company.name);
            bounds.extend(company.marker.getPosition());
        });

        if (companies.length > 1) {
            googleMap.fitBounds(bounds);
        } else {
            googleMap.panTo(companies[0].marker.getPosition());
        }
    }
};

function addMarker(position, content, title, label) {
    var marker = new google.maps.Marker({
        position: position,
        map: googleMap,
        title: title,
        label: label
    });
    markerCluster.addMarker(marker, true);

    if (content) {
        var iw;
        if (google_maps['single_info_window']) iw = infoWindow;
        else iw = new google.maps.InfoWindow(google_maps['iw_options']);

        marker.addListener('click', function () {
            iw.setContent(content);
            iw.open(googleMap, marker);
        });
    }

    return marker;
}
