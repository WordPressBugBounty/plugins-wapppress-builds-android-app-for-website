if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
	navigator.serviceWorker.register(INSTANTAPPY_PWA_service_worker.url)
	.then(function(registration) { console.log('INSTANTAPPY service worker ready'); registration.update(); })
	.catch(function(error) { console.log('Registration failed with ' + error); });
  });
}