<?php
// Calculate relative base url path dynamically to support inclusion in nested directories
$sdg_base_url = '';
$request_uri = $_SERVER['REQUEST_URI'];
$project_name = 'gloabl-rise';
$pos = strpos($request_uri, $project_name);
if ($pos !== false) {
    $sdg_base_url = substr($request_uri, 0, $pos + strlen($project_name)) . '/';
} else {
    $sdg_base_url = '/';
}
?>
<!-- SDG Goals Component -->
<section class="sdg-section" id="sdgSection" aria-label="Sustainable Development Goals">
  <div class="header-container">
    
    <!-- Title Area -->
    <div class="sdg-header">
      <span class="sdg-subtitle">Towards Achieving</span>
      <h2 class="sdg-title">Sustainable Development Goals</h2>
    </div>

    <!-- Goals Grid -->
    <div class="sdg-grid">
      
      <!-- Goal 3: Good Health & Well Being -->
      <a href="#" class="sdg-card" aria-label="Goal 3: Good Health and Well Being">
        <svg class="sdg-card-bg" viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="grad-purple" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#bd9de3" />
              <stop offset="100%" stop-color="#8c60c5" />
            </linearGradient>
          </defs>
          <path d="M 100 10 C 112 10 185 49 190 58 C 195 67 195 163 190 172 C 185 181 112 220 100 220 C 88 220 15 181 10 172 C 5 163 5 67 10 58 C 15 49 88 10 100 10 Z" fill="url(#grad-purple)" />
        </svg>
        <div class="sdg-card-content">
          <div class="sdg-icon">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
              <mask id="heart-mask">
                <rect width="64" height="64" fill="white"/>
                <path d="M12 32H23.5L27 22L32 44L36.5 16L40.5 37L43.5 29L46.5 32H52" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
              </mask>
              <path d="M32 58C32 58 6 40.6 6 24.8C6 16.6 12.6 10 20.8 10C26 10 29.6 13 32 16.4C34.4 13 38 10 43.2 10C51.4 10 58 16.6 58 24.8C58 40.6 32 58 32 58Z" fill="white" mask="url(#heart-mask)"/>
            </svg>
          </div>
          <div class="sdg-divider"></div>
          <div class="sdg-label">Good Health &<br>Well Being</div>
          <div class="sdg-number">#3</div>
        </div>
      </a>

      <!-- Goal 5: Gender Equality -->
      <a href="#" class="sdg-card" aria-label="Goal 5: Gender Equality">
        <svg class="sdg-card-bg" viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="grad-pink" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#f8749d" />
              <stop offset="100%" stop-color="#e93368" />
            </linearGradient>
          </defs>
          <path d="M 100 10 C 112 10 185 49 190 58 C 195 67 195 163 190 172 C 185 181 112 220 100 220 C 88 220 15 181 10 172 C 5 163 5 67 10 58 C 15 49 88 10 100 10 Z" fill="url(#grad-pink)" />
        </svg>
        <div class="sdg-card-content">
          <div class="sdg-icon">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
              <path d="M42 16H52V26M52 16L39 29" stroke="white" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
              <path d="M32 44V56M26 50H38" stroke="white" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
              <circle cx="32" cy="32" r="12" stroke="white" stroke-width="4.5" fill="none"/>
              <path d="M28 30H36M28 34H36" stroke="white" stroke-width="4" stroke-linecap="round" fill="none"/>
            </svg>
          </div>
          <div class="sdg-divider"></div>
          <div class="sdg-label">Gender<br>Equality</div>
          <div class="sdg-number">#5</div>
        </div>
      </a>

      <!-- Goal 4: Quality Education -->
      <a href="#" class="sdg-card" aria-label="Goal 4: Quality Education">
        <svg class="sdg-card-bg" viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="grad-yellow" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#ffdb4d" />
              <stop offset="100%" stop-color="#f5a623" />
            </linearGradient>
          </defs>
          <path d="M 100 10 C 112 10 185 49 190 58 C 195 67 195 163 190 172 C 185 181 112 220 100 220 C 88 220 15 181 10 172 C 5 163 5 67 10 58 C 15 49 88 10 100 10 Z" fill="url(#grad-yellow)" />
        </svg>
        <div class="sdg-card-content">
          <div class="sdg-icon">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
              <path d="M32 14L56 24L32 34L8 24Z" fill="white"/>
              <path d="M18 29V39C18 43 24 46 32 46C40 46 46 43 46 39V29" fill="white"/>
              <path d="M32 24L51 29V42" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
              <circle cx="51" cy="43" r="1.5" fill="white"/>
            </svg>
          </div>
          <div class="sdg-divider"></div>
          <div class="sdg-label">Quality<br>Education</div>
          <div class="sdg-number">#4</div>
        </div>
      </a>

      <!-- Goal 8: Decent Work & Economic Growth (Using requested WebP image asset) -->
      <a href="#" class="sdg-card-img-wrapper" aria-label="Goal 8: Decent Work and Economic Growth">
        <img src="<?php echo $sdg_base_url; ?>assets/economic-growth.png.webp" alt="Decent Work & Economic Growth #8 Hexagon Card" loading="lazy">
      </a>

      <!-- Goal 10: Reduced Inequalities -->
      <a href="#" class="sdg-card" aria-label="Goal 10: Reduced Inequalities">
        <svg class="sdg-card-bg" viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="grad-blue" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#5d81f2" />
              <stop offset="100%" stop-color="#2d50c7" />
            </linearGradient>
          </defs>
          <path d="M 100 10 C 112 10 185 49 190 58 C 195 67 195 163 190 172 C 185 181 112 220 100 220 C 88 220 15 181 10 172 C 5 163 5 67 10 58 C 15 49 88 10 100 10 Z" fill="url(#grad-blue)" />
        </svg>
        <div class="sdg-card-content">
          <div class="sdg-icon">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
              <path d="M32 10C44.15 10 54 19.85 54 32C54 44.15 44.15 54 32 54C19.85 54 10 44.15 10 32C10 25.5 12.8 19.6 17.3 15.6" stroke="white" stroke-width="4.5" stroke-linecap="round" fill="none"/>
              <path d="M24 28.5H40M24 35.5H40" stroke="white" stroke-width="4.5" stroke-linecap="round" fill="none"/>
            </svg>
          </div>
          <div class="sdg-divider"></div>
          <div class="sdg-label">Reduced<br>Inequalities</div>
          <div class="sdg-number">#10</div>
        </div>
      </a>

      <!-- Goal 17: Partnerships for the Goals -->
      <a href="#" class="sdg-card" aria-label="Goal 17: Partnerships for the Goals">
        <svg class="sdg-card-bg" viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="grad-green" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#83d445" />
              <stop offset="100%" stop-color="#4d9c22" />
            </linearGradient>
          </defs>
          <path d="M 100 10 C 112 10 185 49 190 58 C 195 67 195 163 190 172 C 185 181 112 220 100 220 C 88 220 15 181 10 172 C 5 163 5 67 10 58 C 15 49 88 10 100 10 Z" fill="url(#grad-green)" />
        </svg>
        <div class="sdg-card-content">
          <div class="sdg-icon">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
              <g stroke="white" stroke-width="3" fill="none" opacity="0.95">
                <circle cx="32" cy="22" r="10"/>
                <circle cx="41.5" cy="29" r="10"/>
                <circle cx="38" cy="40" r="10"/>
                <circle cx="26" cy="40" r="10"/>
                <circle cx="22.5" cy="29" r="10"/>
              </g>
              <circle cx="32" cy="32" r="3" fill="white" opacity="0.9"/>
            </svg>
          </div>
          <div class="sdg-divider"></div>
          <div class="sdg-label">Partnerships<br>For The Goals</div>
          <div class="sdg-number">#17</div>
        </div>
      </a>

    </div>

  </div>
</section>
