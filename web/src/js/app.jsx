$(() => {
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
