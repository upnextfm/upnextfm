

$(() => {
  if ($.fn.sideNav === undefined) {
    return;
  }

  $('#up-nav-toggle').sideNav();

  $('.up-pulse-hover').hover((e) => {
    let target = $(e.target);
    if (target.is('.material-icons')) {
      target = target.parents('.up-pulse-hover');
    }
    target.addClass('pulse');
  }, (e) => {
    let target = $(e.target);
    if (target.is('.material-icons')) {
      target = target.parents('.up-pulse-hover');
    }
    target.removeClass('pulse');
  });
});

$(() => {
  if ($.fn.modal === undefined) {
    return;
  }

  let playID = 0;
  const modal  = $('.modal');
  modal.modal({
    ready: (m, trigger) => {
      playID = $(trigger).parents('.card:first').data('id');
    }
  });

  $('.up-play-modal__btn').on('click', (e) => {
    const $target = $(e.currentTarget);
    const room    = $target.data('room');
    if (room && playID) {
      fetch(`/api/r/${room}/playlist/${playID}`, {
        method: 'PUT'
      })
        .then(() => {
          modal.modal('close');
        })
        .catch((err) => {
          console.error(err);
        });
    }
  });
});

$(() => {
  if ($.fn.dropdown === undefined) {
    return;
  }

  $('.dropdown-button').dropdown({
    inDuration:  100,
    outDuration: 225
  });
});

const DISCOVERY_DOCS = ['https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest'];
const CLIENT_ID      = 'AIzaSyAQObmMYolUyImDomBRDzn76vu85wsWjIw';
const SCOPES         = 'https://www.googleapis.com/auth/youtube';

// Upon loading, the Google APIs JS client automatically invokes this callback.
const googleApiClientReady = () => {
  gapi.client.init({
    discoveryDocs: DISCOVERY_DOCS,
    clientId:      CLIENT_ID,
    scope:         SCOPES
  });
};


// Attempt the immediate OAuth 2.0 client flow as soon as the page loads.
// If the currently logged-in Google Account has previously authorized
// the client specified as the OAUTH2_CLIENT_ID, then the authorization
// succeeds with no user intervention. Otherwise, it fails and the
// user interface that prompts for authorization needs to display.
const checkAuth = () => {
  gapi.auth.authorize({
    client_id: OAUTH2_CLIENT_ID,
    scope:     OAUTH2_SCOPES,
    immediate: true
  }, handleAuthResult);
};

// Handle the result of a gapi.auth.authorize() call.
const handleAuthResult = (authResult) => {
  console.info(authResult);
/*  if (authResult && !authResult.error) {
    $('.pre-auth').hide();
    $('.post-auth').show();
    loadAPIClientInterfaces();
  } else {
    $('#login-link').click(function() {
      gapi.auth.authorize({
        client_id: OAUTH2_CLIENT_ID,
        scope:     OAUTH2_SCOPES,
        immediate: false
      }, handleAuthResult);
    });
  }*/
};

// Load the client interfaces for the YouTube Analytics and Data APIs, which
// are required to use the Google APIs JS client. More info is available at
// https://developers.google.com/api-client-library/javascript/dev/dev_jscript#loading-the-client-library-and-the-api
const loadAPIClientInterfaces = () => {
  gapi.client.load('youtube', 'v3', function() {
    handleAPILoaded();
  });
};

const handleAPILoaded = () => {
  const q = 'grimes';
  const request = gapi.client.youtube.search.list({
    q,
    part: 'snippet'
  });

  request.execute((response) => {
    const str = JSON.stringify(response.result);
    console.info(str);
  });
};

$(() => {
  setTimeout(() => {
    googleApiClientReady();
  }, 1000);

});
