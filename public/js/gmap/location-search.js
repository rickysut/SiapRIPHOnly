// Define form submission handler function
function handleFormSubmit(event) {
	event.preventDefault();
	const input = document.getElementById("searchBox");
	const location = input.value;
	geocode(location);
}

// Define geocoding function
function geocode(location) {

	const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${location}&key=${apiKey}`;
	fetch(url)
		.then((response) => response.json())
		.then((data) => {
			const { lat, lng } = data.results[0].geometry.location;
			centerMap(lat, lng, 12);
		})
		.catch((error) => console.log(error));
}

// Define map centering function
function centerMap(lat, lng, zoom) {
	const center = new google.maps.LatLng(lat, lng);
	myMap.setCenter(center);
	myMap.setZoom(zoom);
}

// Add event listener to form
const form = document.getElementById("location-search-form");
form.addEventListener("submit", handleFormSubmit);
