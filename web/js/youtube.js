function start() {
  // Initializes the client with the API key and the Translate API.
  gapi.client.init({
    'apiKey': 'AIzaSyAQObmMYolUyImDomBRDzn76vu85wsWjIw',
    'discoveryDocs': ['https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest'],
  }).then(function() {
    console.info('request');
    return gapi.client.youtube.search.list({
      q: 'grimes',
      part: 'snippet',
      type: 'video',
      maxResults: 20
    });
  }).then(function(response) {
    console.log(response.result.items);
  }, function(reason) {
    console.error('Error: ' + reason.result.error.message);
  });
};

// Loads the JavaScript client library and invokes `start` afterwards.
gapi.load('client', start);

