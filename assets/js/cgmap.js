var cgmapLoaded =false;
class Cgmap{
    constructor(map,lat,lng,fieldName) {

        this.marker =false;
        this.map = map;
        this.lat =lat;
        this.lng =lng;
        this.fieldName =fieldName;

        // google map init scripts
        this.input = document.getElementById('cgmapsearchInput');
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(this.input);
        this.autocomplete = new google.maps.places.Autocomplete(this.input);
        this.setMarker();
        this.infowindow = new google.maps.InfoWindow();
        
        this.setAutoComplete(this.autocomplete,this.infowindow,this.map,this.marker,this.fieldName);

        // even listner ketika peta diklik
        var infowindow =this.infowindow;
        var marker =this.marker;
        var fieldName =this.fieldName;
        google.maps.event.addListener(map, 'click', function(event) {
            cgmapMarker(this, event.latLng,infowindow,marker,fieldName);
        });
    }

    setMarker(){
        if (this.marker) {
            // pindahkan marker
            this.marker.setPosition(new google.maps.Point(0, -29));
        } else {
            // buat marker baru
            this.marker = new google.maps.Marker({
                position: {
                    lat : parseFloat( this.lat ),
                    lng : parseFloat( this.lng)
                },
                map: this.map,
            });
        }
    }

    setAutoComplete(autocomplete,infowindow,map,marker,fieldName){
        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
    
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            
            var address = '';
            if (place.address_components) {
                address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
                ].join('');
            }
            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
            $("input[name='"+fieldName+"']").val(place.geometry.location.lat()+","+place.geometry.location.lng());
        });
    }
}

function cgmapMarker(peta, posisiTitik,infowindow,marker,fieldName) {
    infowindow.close();
    if (marker) {
        marker.setPosition(posisiTitik);
    } else {
        marker = new google.maps.Marker({
            position: posisiTitik,
            map: peta,
        });
    }

    marker.setAnimation(google.maps.Animation.BOUNCE);
    setTimeout(function() {
        marker.setAnimation(null);
    }, 750);

    $("input[name='"+fieldName+"']").val(posisiTitik.lat()+","+posisiTitik.lng());
}
