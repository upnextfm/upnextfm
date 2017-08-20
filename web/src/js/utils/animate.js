export function animateScrollLeft(element, to, duration) {
  if (duration < 0) return;
  const difference = to - element.scrollLeft;
  const perTick    = difference / duration * 2;

  setTimeout(() => {
    const scrollLeft = element.scrollLeft + perTick;
    if (Number.isFinite(scrollLeft)) {
      element.scrollLeft = scrollLeft;
      animateScrollLeft(element, to, duration - 2);
    }
  }, 10);
}
