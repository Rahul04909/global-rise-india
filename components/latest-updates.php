<?php
/**
 * Latest Updates / Blog Slider Component
 * The Global Rise Foundation
 *
 * Displays recent foundation news, achievements & field updates
 * in a responsive 3-card sliding carousel.
 */

$latest_updates = [
  [
    'date'    => 'June 20, 2026',
    'title'   => 'TGRF Distributes 500+ School Supply Kits to Urban Slum Children Ahead of New Academic Year',
    'image'   => 'assets/images/slum_education.png',
    'alt'     => 'Volunteers distributing school bags and stationery to slum children',
    'link'    => '#',
  ],
  [
    'date'    => 'May 14, 2026',
    'title'   => 'Free Veterinary Medical Camp Vaccinates Over 800 Community Strays in Three Cities',
    'image'   => 'assets/images/animal_welfare.png',
    'alt'     => 'Veterinary volunteers administering anti-rabies vaccines to street dogs',
    'link'    => '#',
  ],
  [
    'date'    => 'April 07, 2026',
    'title'   => 'Women Empowerment SHG Graduates 120 Rural Women in Tailoring & Digital Literacy',
    'image'   => 'assets/images/women_empowerment.png',
    'alt'     => 'Women at a TGRF vocational skills graduation ceremony',
    'link'    => '#',
  ],
  [
    'date'    => 'March 22, 2026',
    'title'   => 'TGRF Partners with District Administration for Cyclone Relief — 10,000 Families Supported',
    'image'   => 'assets/images/disaster_relief.png',
    'alt'     => 'Relief volunteers distributing emergency kits to flood-affected families',
    'link'    => '#',
  ],
  [
    'date'    => 'February 28, 2026',
    'title'   => 'Senior Citizen Care Drive: Monthly Meal & Medicine Kits Reach 350 Destitute Elders',
    'image'   => 'assets/images/senior_care.png',
    'alt'     => 'Senior citizens receiving meals and care support from TGRF team',
    'link'    => '#',
  ],
  [
    'date'    => 'January 15, 2026',
    'title'   => '1,200 Wheelchair & Mobility Kits Distributed to Differently-Abled Persons Across Rural India',
    'image'   => 'assets/images/disabled_persons.png',
    'alt'     => 'A person with disability receiving a wheelchair from TGRF volunteers',
    'link'    => '#',
  ],
];
?>

<!-- =====================================================
     LATEST UPDATES — BLOG SLIDER SECTION
     ===================================================== -->
<section class="latest-updates-section" aria-label="Latest Updates from The Global Rise Foundation">
  <div class="header-container">

    <!-- Section Heading -->
    <h2 class="latest-updates-title">Latest Updates</h2>

    <!-- Slider Wrapper -->
    <div class="updates-slider-wrapper" id="updatesSliderWrapper">

      <!-- Prev Arrow -->
      <button class="updates-arrow updates-arrow-prev" id="updatesPrevBtn" aria-label="Previous updates">
        <i class="fa-solid fa-angle-left"></i>
      </button>

      <!-- Sliding Track Viewport -->
      <div class="updates-track-viewport">
        <div class="updates-track" id="updatesTrack">

          <?php foreach ($latest_updates as $update): ?>
          <article class="update-card">
            <!-- Image -->
            <div class="update-card-img-wrapper">
              <img
                src="<?php echo htmlspecialchars($update['image']); ?>"
                alt="<?php echo htmlspecialchars($update['alt']); ?>"
                class="update-card-img"
                loading="lazy"
              >
            </div>
            <!-- Body -->
            <div class="update-card-body">
              <time class="update-card-date" datetime="<?php echo date('Y-m-d', strtotime($update['date'])); ?>">
                <?php echo htmlspecialchars($update['date']); ?>
              </time>
              <h3 class="update-card-title"><?php echo htmlspecialchars($update['title']); ?></h3>
              <a href="<?php echo htmlspecialchars($update['link']); ?>" class="update-card-link" aria-label="Read more about <?php echo htmlspecialchars($update['title']); ?>">
                Read More <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </article>
          <?php endforeach; ?>

        </div><!-- /.updates-track -->
      </div><!-- /.updates-track-viewport -->

      <!-- Next Arrow -->
      <button class="updates-arrow updates-arrow-next" id="updatesNextBtn" aria-label="Next updates">
        <i class="fa-solid fa-angle-right"></i>
      </button>

    </div><!-- /.updates-slider-wrapper -->

    <!-- Dot Indicators -->
    <div class="updates-dots" id="updatesDots" aria-label="Slider navigation"></div>

  </div><!-- /.header-container -->
</section>

<!-- =====================================================
     BLOG SLIDER JAVASCRIPT
     ===================================================== -->
<script>
(function () {
  'use strict';

  const track       = document.getElementById('updatesTrack');
  const prevBtn     = document.getElementById('updatesPrevBtn');
  const nextBtn     = document.getElementById('updatesNextBtn');
  const dotsWrap    = document.getElementById('updatesDots');
  const cards       = Array.from(track.querySelectorAll('.update-card'));

  let currentIndex  = 0;
  let visibleCount  = 3;   // updated by getVisible()
  let maxIndex      = 0;   // updated by recalc()
  let autoPlayTimer = null;

  /* ---- Determine how many cards are visible based on viewport ---- */
  function getVisible () {
    const vw = window.innerWidth;
    if (vw <= 600)  return 1;
    if (vw <= 900)  return 2;
    return 3;
  }

  /* ---- Calculate card width + gap for translation ---- */
  function getCardStep () {
    if (cards.length === 0) return 0;
    const cardEl  = cards[0];
    const gapPx   = 25; // must match CSS gap
    return cardEl.offsetWidth + gapPx;
  }

  /* ---- Build / rebuild dot buttons ---- */
  function buildDots () {
    dotsWrap.innerHTML = '';
    for (let i = 0; i <= maxIndex; i++) {
      const dot = document.createElement('button');
      dot.className   = 'updates-dot' + (i === currentIndex ? ' active' : '');
      dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
      dot.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(dot);
    }
  }

  /* ---- Sync active dot ---- */
  function syncDots () {
    dotsWrap.querySelectorAll('.updates-dot').forEach((dot, i) => {
      dot.classList.toggle('active', i === currentIndex);
    });
  }

  /* ---- Apply translation ---- */
  function applySlide () {
    const offset = currentIndex * getCardStep();
    track.style.transform = 'translateX(-' + offset + 'px)';
    syncDots();

    /* Disable / enable arrows at ends */
    prevBtn.style.opacity       = currentIndex === 0        ? '0.35' : '1';
    prevBtn.style.pointerEvents = currentIndex === 0        ? 'none'  : 'auto';
    nextBtn.style.opacity       = currentIndex >= maxIndex  ? '0.35' : '1';
    nextBtn.style.pointerEvents = currentIndex >= maxIndex  ? 'none'  : 'auto';
  }

  /* ---- Navigate to a specific index ---- */
  function goTo (index) {
    currentIndex = Math.max(0, Math.min(index, maxIndex));
    applySlide();
  }

  /* ---- Recalculate on resize ---- */
  function recalc () {
    visibleCount = getVisible();
    maxIndex     = Math.max(0, cards.length - visibleCount);
    if (currentIndex > maxIndex) currentIndex = maxIndex;
    buildDots();
    applySlide();
  }

  /* ---- Arrow click handlers ---- */
  prevBtn.addEventListener('click', () => { goTo(currentIndex - 1); resetAutoPlay(); });
  nextBtn.addEventListener('click', () => { goTo(currentIndex + 1); resetAutoPlay(); });

  /* ---- Auto-play every 5 seconds ---- */
  function startAutoPlay () {
    autoPlayTimer = setInterval(() => {
      const next = currentIndex >= maxIndex ? 0 : currentIndex + 1;
      goTo(next);
    }, 5000);
  }

  function resetAutoPlay () {
    clearInterval(autoPlayTimer);
    startAutoPlay();
  }

  /* ---- Touch / swipe support ---- */
  let touchStartX = 0;
  track.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) {
      diff > 0 ? goTo(currentIndex + 1) : goTo(currentIndex - 1);
      resetAutoPlay();
    }
  }, { passive: true });

  /* ---- Pause auto-play on hover ---- */
  const wrapper = document.getElementById('updatesSliderWrapper');
  wrapper.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
  wrapper.addEventListener('mouseleave', startAutoPlay);

  /* ---- Init ---- */
  recalc();
  startAutoPlay();

  /* ---- Debounced resize listener ---- */
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(recalc, 200);
  });
})();
</script>
