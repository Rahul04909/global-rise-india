<!-- Hero Slider Component -->
<section class="hero-slider-container" id="heroSlider" aria-label="Featured Stories Slider">
  <!-- Slides Track -->
  <div class="hero-slides-track">
    
    <!-- Slide 1 -->
    <div class="hero-slide active">
      <img src="https://www.akshayapatra.org/includefiles/banners/MDM-Header_banner-web-1583x550.jpg" alt="Mid-Day Meal Banner" class="hero-slide-image">
    </div>

    <!-- Slide 2 -->
    <div class="hero-slide">
      <img src="https://www.akshayapatra.org/includefiles/banners/HB_Special_Occasion-web.jpg" alt="Special Occasion Banner" class="hero-slide-image">
    </div>

    <!-- Slide 3 -->
    <div class="hero-slide">
      <img src="https://www.akshayapatra.org/includefiles/banners/HB-HM-Banner-1583x550-v2_(1).jpg" alt="The Global Rise Foundation Banner" class="hero-slide-image">
    </div>

  </div>

  <!-- Navigation Arrows -->
  <button class="hero-arrow hero-arrow-left" id="prevSlideBtn" aria-label="Previous Slide">
    <i class="fa-solid fa-chevron-left"></i>
  </button>
  <button class="hero-arrow hero-arrow-right" id="nextSlideBtn" aria-label="Next Slide">
    <i class="fa-solid fa-chevron-right"></i>
  </button>
</section>

<!-- Slider JavaScript functionality -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    const prevBtn = document.getElementById('prevSlideBtn');
    const nextBtn = document.getElementById('nextSlideBtn');
    const totalSlides = slides.length;
    
    let currentSlide = 0;
    let autoplayTimer = null;
    const autoplayInterval = 5000; // 5 seconds autoplay

    // Show slide by index
    function showSlide(index) {
      // Remove active class from current elements
      slides[currentSlide].classList.remove('active');

      // Calculate new index with wrap-around
      currentSlide = (index + totalSlides) % totalSlides;

      // Add active class to new elements
      slides[currentSlide].classList.add('active');
    }

    // Go to next slide
    function nextSlide() {
      showSlide(currentSlide + 1);
    }

    // Go to previous slide
    function prevSlide() {
      showSlide(currentSlide - 1);
    }

    // Autoplay Management
    function startAutoplay() {
      stopAutoplay();
      autoplayTimer = setInterval(nextSlide, autoplayInterval);
    }

    function stopAutoplay() {
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    }

    // Arrow Button Handlers
    if (prevBtn) {
      prevBtn.addEventListener('click', function() {
        prevSlide();
        startAutoplay(); // Reset timer on user interaction
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        nextSlide();
        startAutoplay(); // Reset timer on user interaction
      });
    }

    // Pause Autoplay on Mouse Enter, Resume on Mouse Leave (Desktop)
    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);

    // ----------------------------------------------------
    // MOBILE SWIPE GESTURE SUPPORT
    // ----------------------------------------------------
    let touchStartX = 0;
    let touchEndX = 0;
    const swipeThreshold = 50; // minimum pixels to swipe

    slider.addEventListener('touchstart', function(e) {
      touchStartX = e.changedTouches[0].screenX;
      stopAutoplay();
    }, { passive: true });

    slider.addEventListener('touchend', function(e) {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
      startAutoplay();
    }, { passive: true });

    function handleSwipe() {
      const diffX = touchStartX - touchEndX;
      
      if (Math.abs(diffX) > swipeThreshold) {
        if (diffX > 0) {
          // Swiped Left -> Next Slide
          nextSlide();
        } else {
          // Swiped Right -> Previous Slide
          prevSlide();
        }
      }
    }

    // Start autoplay initially
    startAutoplay();
  });
</script>
