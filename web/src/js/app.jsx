

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
        method:      'PUT',
        credentials: 'same-origin'
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
