(() => {
 document.querySelectorAll('[data-location-partners]').forEach(section => {
  const track = section.querySelector('.koala-location-partners__track');
  const items = [...track.children];
  const move = direction => {
   const step = items[0].getBoundingClientRect().width;
   const current = Math.round(track.scrollLeft / step);
   const next = (current + direction + items.length) % items.length;
   track.scrollTo({left:next * step,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'});
  };
  section.querySelector('[data-partners-prev]')?.addEventListener('click', () => move(-1));
  section.querySelector('[data-partners-next]')?.addEventListener('click', () => move(1));
 });
})();
