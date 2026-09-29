// NDA EMPIRE — small progressive enhancements; every page works without this file.

// Mobile menu
const toggle = document.querySelector('.nav-toggle');
toggle?.addEventListener('click', () => {
  const open = document.getElementById('nav').classList.toggle('open');
  toggle.setAttribute('aria-expanded', open);
});

// Confirm destructive or committing actions
document.querySelectorAll('form[data-confirm]').forEach((form) => {
  form.addEventListener('submit', (e) => { if (!confirm(form.dataset.confirm)) e.preventDefault(); });
});

// Selfie: send as soon as a photo is picked
document.querySelectorAll('[data-autosubmit]').forEach((input) => {
  input.addEventListener('change', () => input.files.length && input.form.submit());
});

// Try-on: the mirror shimmers while the AI works (takes 10–30 s)
document.querySelectorAll('form[data-tryon]').forEach((form) => {
  form.addEventListener('submit', () => {
    const mirror = document.querySelector('[data-mirror]');
    mirror?.classList.add('busy');
    mirror?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.querySelectorAll('form[data-tryon] button').forEach((b) => { b.disabled = true; });
    form.querySelector('button').textContent = form.dataset.busy;
  });
});

// Wig cards: play the video on hover (desktop) or tap (phone)
document.querySelectorAll('.media[data-video]').forEach((media) => {
  const video = media.querySelector('video');
  const play = () => { media.classList.add('playing'); video.play().catch(() => {}); };
  const stop = () => { media.classList.remove('playing'); video.pause(); };
  media.addEventListener('mouseenter', play);
  media.addEventListener('mouseleave', stop);
  media.addEventListener('click', () => (video.paused ? play() : stop()));
});

// Admin: take the catalogue photo from the wig video when no photo was chosen
const wigForm = document.querySelector('[data-wig-form]');
if (wigForm) {
  const videoInput = wigForm.querySelector('[data-video-input]');
  const imageInput = wigForm.querySelector('[data-image-input]');
  const preview = wigForm.querySelector('[data-preview]');
  const show = (file) => { preview.src = URL.createObjectURL(file); preview.style.display = 'block'; };

  imageInput.addEventListener('change', () => imageInput.files[0] && show(imageInput.files[0]));
  videoInput.addEventListener('change', () => {
    const file = videoInput.files[0];
    if (!file || imageInput.files.length) return;
    const video = document.createElement('video');
    video.muted = true; video.playsInline = true; video.src = URL.createObjectURL(file);
    video.addEventListener('loadeddata', () => { video.currentTime = Math.min(1, video.duration / 2); });
    video.addEventListener('seeked', () => {
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth; canvas.height = video.videoHeight;
      canvas.getContext('2d').drawImage(video, 0, 0);
      canvas.toBlob((blob) => {
        const photo = new File([blob], 'perruque.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer(); dt.items.add(photo); imageInput.files = dt.files;
        show(photo);
      }, 'image/jpeg', 0.9);
    }, { once: true });
  });
}
