export function animateScrollTo(element, to, duration) {
  if (duration < 0) return;
  const difference = to - element.scrollTop;
  const perTick    = difference / duration * 2;

  setTimeout(() => {
    const scrollTop = element.scrollTop + perTick;
    if (Number.isFinite(scrollTop)) {
      element.scrollTop = scrollTop;
      animateScrollTo(element, to, duration - 2);
    }
  }, 10);
}
