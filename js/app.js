var divMap = document.getElementById('map');
var map = L.map('map').setView([divMap.dataset.lat, divMap.dataset.lng], divMap.dataset.zoom);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var lines = [];

Array.from(document.querySelectorAll('.js-marker')).forEach((item) => {
  lines.push([item.dataset.lat, item.dataset.lng]);
  L.marker([item.dataset.lat, item.dataset.lng]).addTo(map)
    .bindPopup(
      `<div id="` + item.dataset.id + `" class='popup-container'>
          <div>Souhaitez-vous vous rendre à ` + item.dataset.name + ` ?</div>
          <div>
              <a href="` + (item.dataset.hebergement ? 'hebergementDescription' : 'hebergementVille') + `.php?` + (item.dataset.hebergement ? 'idHebergement=' : 'idVille=') + item.dataset.id + `" class='btn btn-success btn-sm popup-a'>J'y vais</a>
          </div>
      </div>`)
})

// il faudra créer un if (s'il existe des marker de point pour faire une ligne alors ... )

// var pointA = new L.LatLng(47.7632836, -0.3299687);
// var pointB = new L.LatLng(48.7632836, -0.3299687);
// var pointC = new L.LatLng(48.7632836, -0.9299687);
// var pointList = [pointA, pointB, pointC];

// var firstpolyline = new L.Polyline(pointList, {
//     color: 'red',
//     weight: 5,
//     opacity: 0.5,
//     smoothFactor: 1
// });
// firstpolyline.addTo(map);