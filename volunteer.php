<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO -->
  <title>Join as a Volunteer | The Global Rise Foundation</title>
  <meta name="description" content="Join The Global Rise Foundation as a volunteer and make a real difference. Apply online to contribute to Animal Welfare, Education, Healthcare, Disaster Relief, Women Empowerment and more across India.">
  <meta name="keywords" content="volunteer India, NGO volunteer, join volunteer, social work, The Global Rise Foundation, community service">
  <meta name="robots" content="index, follow">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="Join as a Volunteer — The Global Rise Foundation">
  <meta property="og:description" content="Be the change. Apply to volunteer with TGRF and help empower communities across India.">
  <meta property="og:url" content="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="assets/css/header.css">
  <link rel="stylesheet" href="assets/css/volunteer.css">
  <link rel="stylesheet" href="assets/css/footer.css">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background-color: #f7fafc;
      font-family: 'Montserrat', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    main { flex: 1; }
  </style>
</head>
<body class="volunteer-page">

  <?php include 'includes/header.php'; ?>

  <main>

    <!-- ═══════════════════════════════════════════════════
         HERO BANNER
    ═══════════════════════════════════════════════════ -->
    <section class="volunteer-hero" aria-label="Volunteer Page Hero">
      <div class="header-container">
        <nav class="vol-breadcrumbs" aria-label="Breadcrumb">
          <a href="index.php">Home</a>
          <span class="sep">›</span>
          <span>Join as a Volunteer</span>
        </nav>
        <h1 class="volunteer-hero-title">Join as a Volunteer</h1>
        <p class="volunteer-hero-sub">
          Be the change you wish to see. Lend your time, skills, and passion to
          uplift communities across India — one life at a time.
        </p>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         WHY VOLUNTEER
    ═══════════════════════════════════════════════════ -->
    <section class="vol-why-section" aria-labelledby="whyVolunteerHeading">
      <div class="header-container">
        <h2 class="vol-section-heading" id="whyVolunteerHeading">Why Volunteer with Us?</h2>
        <div class="vol-why-grid">

          <div class="vol-why-card">
            <div class="vol-why-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <h3>Create Real Impact</h3>
            <p>Your time directly reaches underserved children, women, elderly, and animals in need across India.</p>
          </div>

          <div class="vol-why-card">
            <div class="vol-why-icon"><i class="fa-solid fa-users-gear"></i></div>
            <h3>Grow Your Skills</h3>
            <p>Develop leadership, communication, and project management skills through meaningful on-ground work.</p>
          </div>

          <div class="vol-why-card">
            <div class="vol-why-icon"><i class="fa-solid fa-award"></i></div>
            <h3>Get Recognised</h3>
            <p>Receive an official TGRF Volunteer Certificate, LinkedIn recommendation, and appreciation letter.</p>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         VOLUNTEER REGISTRATION FORM
    ═══════════════════════════════════════════════════ -->
    <section class="vol-form-section" aria-labelledby="volunteerFormHeading">
      <div class="header-container">

        <h2 class="vol-section-heading" id="volunteerFormHeading" style="margin-bottom:35px;">Volunteer Registration</h2>

        <div class="vol-form-card">

          <!-- ── Progress Steps ── -->
          <div class="vol-form-steps" id="volSteps" role="tablist" aria-label="Form progress">
            <div class="vol-step active" id="step-ind-1" role="tab" aria-selected="true">
              <div class="vol-step-number"><span>1</span></div>
              <div class="vol-step-label">Personal Info</div>
            </div>
            <div class="vol-step-connector" id="conn-1-2"></div>
            <div class="vol-step" id="step-ind-2" role="tab" aria-selected="false">
              <div class="vol-step-number"><span>2</span></div>
              <div class="vol-step-label">Volunteer Details</div>
            </div>
            <div class="vol-step-connector" id="conn-2-3"></div>
            <div class="vol-step" id="step-ind-3" role="tab" aria-selected="false">
              <div class="vol-step-number"><span>3</span></div>
              <div class="vol-step-label">Background</div>
            </div>
            <div class="vol-step-connector" id="conn-3-4"></div>
            <div class="vol-step" id="step-ind-4" role="tab" aria-selected="false">
              <div class="vol-step-number"><span>4</span></div>
              <div class="vol-step-label">Confirm</div>
            </div>
          </div><!-- /.vol-form-steps -->

          <!-- ── Form ── -->
          <form id="volunteerForm" method="POST" action="api/submit-volunteer.php" novalidate>

            <!-- ────────────────────────────────────
                 STEP 1 — Personal Information
            ──────────────────────────────────── -->
            <fieldset class="vol-fieldset active" id="vol-step-1" aria-labelledby="step1-title">
              <div class="vol-fieldset-title" id="step1-title">
                <i class="fa-solid fa-user"></i> Personal Information
              </div>

              <div class="vol-form-row">
                <div class="vol-field">
                  <label class="vol-label" for="full_name">Full Name <span class="req">*</span></label>
                  <input class="vol-input" type="text" id="full_name" name="full_name"
                    placeholder="e.g. Priya Sharma" autocomplete="name" required>
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="email">Email Address <span class="req">*</span></label>
                  <input class="vol-input" type="email" id="email" name="email"
                    placeholder="you@example.com" autocomplete="email" required>
                </div>
              </div>

              <div class="vol-form-row">
                <div class="vol-field">
                  <label class="vol-label" for="phone">Mobile Number <span class="req">*</span></label>
                  <input class="vol-input" type="tel" id="phone" name="phone"
                    placeholder="10-digit number" maxlength="10" required>
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="dob">Date of Birth <span class="req">*</span></label>
                  <input class="vol-input" type="date" id="dob" name="dob" required>
                </div>
              </div>

              <div class="vol-form-row">
                <div class="vol-field">
                  <label class="vol-label" for="gender">Gender <span class="req">*</span></label>
                  <select class="vol-select" id="gender" name="gender" required>
                    <option value="">— Select Gender —</option>
                    <option value="female">Female</option>
                    <option value="male">Male</option>
                    <option value="non-binary">Non-Binary</option>
                    <option value="prefer-not-to-say">Prefer not to say</option>
                  </select>
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="education">Highest Education</label>
                  <select class="vol-select" id="education" name="education">
                    <option value="">— Select —</option>
                    <option value="high-school">High School (10th / 12th)</option>
                    <option value="diploma">Diploma / ITI</option>
                    <option value="graduate">Graduate (B.A. / B.Sc. / B.Com etc.)</option>
                    <option value="postgraduate">Post Graduate (M.A. / M.Sc. / MBA etc.)</option>
                    <option value="doctorate">Doctorate / PhD</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div class="vol-form-row thirds">
                <div class="vol-field">
                  <label class="vol-label" for="city">City <span class="req">*</span></label>
                  <input class="vol-input" type="text" id="city" name="city"
                    placeholder="e.g. Mumbai" required>
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="state">State <span class="req">*</span></label>
                  <select class="vol-select" id="state" name="state" required>
                    <option value="">— State —</option>
                    <option>Andhra Pradesh</option><option>Arunachal Pradesh</option>
                    <option>Assam</option><option>Bihar</option><option>Chhattisgarh</option>
                    <option>Delhi</option><option>Goa</option><option>Gujarat</option>
                    <option>Haryana</option><option>Himachal Pradesh</option>
                    <option>Jharkhand</option><option>Karnataka</option><option>Kerala</option>
                    <option>Madhya Pradesh</option><option>Maharashtra</option>
                    <option>Manipur</option><option>Meghalaya</option><option>Mizoram</option>
                    <option>Nagaland</option><option>Odisha</option><option>Punjab</option>
                    <option>Rajasthan</option><option>Sikkim</option><option>Tamil Nadu</option>
                    <option>Telangana</option><option>Tripura</option>
                    <option>Uttar Pradesh</option><option>Uttarakhand</option>
                    <option>West Bengal</option>
                  </select>
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="pincode">PIN Code</label>
                  <input class="vol-input" type="text" id="pincode" name="pincode"
                    placeholder="6-digit PIN" maxlength="6">
                </div>
              </div>

            </fieldset>

            <!-- ────────────────────────────────────
                 STEP 2 — Volunteer Details
            ──────────────────────────────────── -->
            <fieldset class="vol-fieldset" id="vol-step-2" aria-labelledby="step2-title">
              <div class="vol-fieldset-title" id="step2-title">
                <i class="fa-solid fa-hands-helping"></i> Volunteer Details
              </div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <label class="vol-label">Area of Interest <span class="req">*</span></label>
                  <div class="vol-radio-group" id="areaGroup" role="radiogroup">
                    <?php
                    $areas = [
                      'Animal Welfare'             => 'fa-paw',
                      'Disaster Management'        => 'fa-house-chimney-crack',
                      'Educating Slum Children'    => 'fa-school',
                      'Health Projects'            => 'fa-kit-medical',
                      'Persons with Disabilities'  => 'fa-wheelchair',
                      'Rural Children Education'   => 'fa-chalkboard-teacher',
                      'Senior Citizen Care'        => 'fa-person-cane',
                      'Swachh Bharat Mission'      => 'fa-broom',
                      'Women Empowerment'          => 'fa-person-dress',
                    ];
                    foreach ($areas as $label => $icon):
                      $val = strtolower(str_replace([' ', '\''], ['-', ''], $label));
                    ?>
                    <div class="vol-option-pill">
                      <input type="radio" name="area_of_interest" id="area-<?php echo $val; ?>"
                        value="<?php echo htmlspecialchars($label); ?>">
                      <label for="area-<?php echo $val; ?>">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                        <?php echo htmlspecialchars($label); ?>
                      </label>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <div class="vol-form-row">
                <div class="vol-field">
                  <label class="vol-label">Availability <span class="req">*</span></label>
                  <div class="vol-radio-group" role="radiogroup">
                    <?php foreach (['Weekdays', 'Weekends', 'Both', 'Flexible'] as $avail): ?>
                    <div class="vol-option-pill">
                      <input type="radio" name="availability" id="avail-<?php echo strtolower($avail); ?>"
                        value="<?php echo $avail; ?>">
                      <label for="avail-<?php echo strtolower($avail); ?>"><?php echo $avail; ?></label>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="hours_range">
                    Hours per Week <span class="req">*</span>
                  </label>
                  <div class="vol-range-wrap">
                    <input class="vol-range-input" type="range" id="hours_range"
                      min="1" max="40" value="5" step="1"
                      oninput="document.getElementById('hours_display').textContent=this.value+' hrs';
                               document.getElementById('hours_per_week').value=this.value;
                               this.style.setProperty('--pct',(((this.value-1)/39)*100)+'%')">
                    <div class="vol-range-value" id="hours_display">5 hrs</div>
                  </div>
                  <input type="hidden" name="hours_per_week" id="hours_per_week" value="5">
                </div>
              </div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <label class="vol-label" for="occupation">Current Occupation</label>
                  <select class="vol-select" id="occupation" name="occupation">
                    <option value="">— Select —</option>
                    <option value="student">Student</option>
                    <option value="employed-private">Employed (Private Sector)</option>
                    <option value="employed-govt">Employed (Government)</option>
                    <option value="self-employed">Self-Employed / Business</option>
                    <option value="homemaker">Homemaker</option>
                    <option value="retired">Retired</option>
                    <option value="freelancer">Freelancer / Consultant</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <label class="vol-label" for="skills">Relevant Skills</label>
                  <input class="vol-input" type="text" id="skills" name="skills"
                    placeholder="e.g. Teaching, First Aid, Photography, Data Entry, Social Media…">
                  <span class="vol-field-hint">Comma-separated list of skills relevant to your chosen area.</span>
                </div>
              </div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <label class="vol-label" for="motivation">
                    Why do you want to volunteer with TGRF? <span class="req">*</span>
                  </label>
                  <textarea class="vol-textarea" id="motivation" name="motivation" rows="4"
                    placeholder="Share your motivation and what impact you hope to create… (min 30 characters)" required></textarea>
                  <span class="vol-field-hint" id="motivChars">0 / 30 characters minimum</span>
                </div>
              </div>

            </fieldset>

            <!-- ────────────────────────────────────
                 STEP 3 — Background & Emergency
            ──────────────────────────────────── -->
            <fieldset class="vol-fieldset" id="vol-step-3" aria-labelledby="step3-title">
              <div class="vol-fieldset-title" id="step3-title">
                <i class="fa-solid fa-file-lines"></i> Background & Emergency Contact
              </div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <label class="vol-label">Do you have prior volunteer experience? <span class="req">*</span></label>
                  <div class="vol-radio-group" role="radiogroup">
                    <div class="vol-option-pill">
                      <input type="radio" name="prior_experience" id="exp-yes" value="yes">
                      <label for="exp-yes"><i class="fa-solid fa-circle-check"></i> Yes</label>
                    </div>
                    <div class="vol-option-pill">
                      <input type="radio" name="prior_experience" id="exp-no" value="no" checked>
                      <label for="exp-no"><i class="fa-solid fa-circle-xmark"></i> No, first time</label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="vol-form-row full" id="expDetailsRow" style="display:none;">
                <div class="vol-field">
                  <label class="vol-label" for="experience_details">Briefly describe your prior experience</label>
                  <textarea class="vol-textarea" id="experience_details" name="experience_details"
                    rows="3" placeholder="Organisation name, duration, type of work…" style="min-height:80px;"></textarea>
                </div>
              </div>

              <div class="vol-fieldset-title" style="margin-top:30px;">
                <i class="fa-solid fa-phone-volume"></i> Emergency Contact
              </div>

              <div class="vol-form-row">
                <div class="vol-field">
                  <label class="vol-label" for="emergency_name">Contact Person Name</label>
                  <input class="vol-input" type="text" id="emergency_name" name="emergency_name"
                    placeholder="Full name of your emergency contact">
                </div>
                <div class="vol-field">
                  <label class="vol-label" for="emergency_phone">Contact Phone Number</label>
                  <input class="vol-input" type="tel" id="emergency_phone" name="emergency_phone"
                    placeholder="10-digit mobile" maxlength="10">
                </div>
              </div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <div class="vol-terms-row">
                    <input type="checkbox" class="vol-terms-check" id="health_declaration"
                      name="health_declaration" value="1">
                    <label class="vol-terms-text" for="health_declaration">
                      I confirm that I am in good health and physically capable of participating
                      in volunteer activities as selected above.
                    </label>
                  </div>
                </div>
              </div>

            </fieldset>

            <!-- ────────────────────────────────────
                 STEP 4 — Review & Submit
            ──────────────────────────────────── -->
            <fieldset class="vol-fieldset" id="vol-step-4" aria-labelledby="step4-title">
              <div class="vol-fieldset-title" id="step4-title">
                <i class="fa-solid fa-clipboard-check"></i> Review &amp; Confirm
              </div>

              <!-- Summary table populated by JS -->
              <div id="volSummaryTable" style="margin-bottom:28px;"></div>

              <div class="vol-form-row full">
                <div class="vol-field">
                  <div class="vol-terms-row">
                    <input type="checkbox" class="vol-terms-check" id="agree_terms"
                      name="agree_terms" value="1" required>
                    <label class="vol-terms-text" for="agree_terms">
                      I have read and agree to the
                      <a href="#" target="_blank">Volunteer Terms &amp; Conditions</a>
                      and the <a href="#" target="_blank">Code of Conduct</a> of
                      The Global Rise Foundation. I understand that my application is
                      subject to review and approval.
                    </label>
                  </div>
                </div>
              </div>

              <!-- Error / Info banner -->
              <div id="volErrorBanner" style="display:none;background:#fff5f5;border:1px solid #fed7d7;
                border-radius:6px;padding:14px 18px;margin-bottom:10px;color:#c53030;font-size:13px;
                font-weight:600;"></div>

            </fieldset>

            <!-- ── Navigation Buttons ── -->
            <div class="vol-form-nav">
              <button type="button" class="vol-btn vol-btn-prev" id="volPrevBtn"
                style="display:none;" aria-label="Go to previous step">
                <i class="fa-solid fa-arrow-left"></i> Back
              </button>
              <button type="button" class="vol-btn vol-btn-next" id="volNextBtn"
                aria-label="Go to next step">
                Next <i class="fa-solid fa-arrow-right"></i>
              </button>
              <button type="submit" class="vol-btn vol-btn-submit" id="volSubmitBtn"
                style="display:none;" aria-label="Submit volunteer application">
                <i class="fa-solid fa-paper-plane"></i> Submit Application
              </button>
            </div>

          </form><!-- /#volunteerForm -->

          <!-- ── Success Panel (shown after submission) ── -->
          <div class="vol-success-panel" id="volSuccessPanel" aria-live="polite">
            <div class="vol-success-icon"><i class="fa-solid fa-check"></i></div>
            <h2>Application Submitted!</h2>
            <p>Thank you for joining The Global Rise Foundation volunteer team.</p>
            <p>A confirmation email has been sent to your registered address.</p>
            <div class="vol-ref-badge" id="volRefBadge">Reference: Loading…</div>
            <p style="margin-top:10px;">Our Volunteer Coordination team will contact you within 3–5 business days.</p>
            <a href="index.php" class="vol-btn vol-btn-next" style="margin:25px auto 0;display:inline-flex;">
              <i class="fa-solid fa-house"></i> Back to Home
            </a>
          </div>

        </div><!-- /.vol-form-card -->
      </div><!-- /.header-container -->
    </section>

  </main><!-- /main -->

  <?php include 'includes/footer.php'; ?>

  <!-- ═══════════════════════════════════════════════════
       VOLUNTEER FORM — MULTI-STEP JAVASCRIPT
  ═══════════════════════════════════════════════════ -->
  <script>
  (function () {
    'use strict';

    /* ── State ── */
    const TOTAL_STEPS = 4;
    let currentStep  = 1;

    /* ── Element References ── */
    const form       = document.getElementById('volunteerForm');
    const prevBtn    = document.getElementById('volPrevBtn');
    const nextBtn    = document.getElementById('volNextBtn');
    const submitBtn  = document.getElementById('volSubmitBtn');
    const errBanner  = document.getElementById('volErrorBanner');
    const successPnl = document.getElementById('volSuccessPanel');
    const motivTa    = document.getElementById('motivation');
    const motivChars = document.getElementById('motivChars');

    /* ── Prior experience toggle ── */
    document.querySelectorAll('[name="prior_experience"]').forEach(r => {
      r.addEventListener('change', () => {
        document.getElementById('expDetailsRow').style.display =
          r.value === 'yes' ? '' : 'none';
      });
    });

    /* ── Motivation character counter ── */
    if (motivTa) {
      motivTa.addEventListener('input', () => {
        const len = motivTa.value.length;
        motivChars.textContent = len + ' / 30 characters minimum';
        motivChars.style.color = len < 30 ? '#e53e3e' : '#38a169';
      });
    }

    /* ── Range slider gradient ── */
    const rangeEl = document.getElementById('hours_range');
    if (rangeEl) rangeEl.style.setProperty('--pct', '10.26%'); // initial 5/40

    /* ── Step navigation helpers ── */
    function showStep(n) {
      // Hide all fieldsets
      document.querySelectorAll('.vol-fieldset').forEach((f, i) => {
        f.classList.toggle('active', i + 1 === n);
      });

      // Update step indicators
      for (let i = 1; i <= TOTAL_STEPS; i++) {
        const ind  = document.getElementById('step-ind-' + i);
        ind.classList.remove('active', 'done');
        if (i < n)       ind.classList.add('done');
        else if (i === n) ind.classList.add('active');
        ind.setAttribute('aria-selected', i === n ? 'true' : 'false');
      }

      // Connectors
      for (let i = 1; i < TOTAL_STEPS; i++) {
        const conn = document.getElementById('conn-' + i + '-' + (i + 1));
        if (conn) conn.classList.toggle('done', i < n);
      }

      // Buttons
      prevBtn.style.display   = n === 1 ? 'none' : 'inline-flex';
      nextBtn.style.display   = n < TOTAL_STEPS ? 'inline-flex' : 'none';
      submitBtn.style.display = n === TOTAL_STEPS ? 'inline-flex' : 'none';

      // Build summary on step 4
      if (n === 4) buildSummary();

      errBanner.style.display = 'none';
      currentStep = n;
    }

    /* ── Field Validation per step ── */
    function validateStep(n) {
      const messages = [];

      if (n === 1) {
        const name  = document.getElementById('full_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const dob   = document.getElementById('dob').value;
        const gender= document.getElementById('gender').value;
        const city  = document.getElementById('city').value.trim();
        const state = document.getElementById('state').value;

        if (name.length < 3)               messages.push('Full name must be at least 3 characters.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
                                            messages.push('Enter a valid email address.');
        if (!/^[6-9]\d{9}$/.test(phone))   messages.push('Enter a valid 10-digit mobile number.');
        if (!dob)                           messages.push('Date of birth is required.');
        else {
          const age = Math.floor((Date.now() - new Date(dob)) / (365.25 * 86400000));
          if (age < 18 || age > 75)         messages.push('Age must be between 18 and 75 years.');
        }
        if (!gender)                        messages.push('Please select your gender.');
        if (!city)                          messages.push('City is required.');
        if (!state)                         messages.push('Please select your state.');
      }

      if (n === 2) {
        const area  = document.querySelector('[name="area_of_interest"]:checked');
        const avail = document.querySelector('[name="availability"]:checked');
        const motiv = motivTa ? motivTa.value.trim() : '';

        if (!area)                          messages.push('Please select an area of interest.');
        if (!avail)                         messages.push('Please select your availability.');
        if (motiv.length < 30)             messages.push('Motivation must be at least 30 characters.');
      }

      if (n === 3) {
        // No hard-required fields in step 3, so just pass
      }

      if (n === 4) {
        if (!document.getElementById('agree_terms').checked) {
          messages.push('You must agree to the Terms & Conditions to proceed.');
        }
      }

      return messages;
    }

    /* ── Build Summary Table ── */
    function buildSummary() {
      const get = id => (document.getElementById(id) || {}).value || '';
      const radio = name => {
        const el = document.querySelector('[name="' + name + '"]:checked');
        return el ? el.value : '—';
      };

      const rows = [
        ['Full Name',          get('full_name')],
        ['Email',              get('email')],
        ['Phone',              get('phone')],
        ['Date of Birth',      get('dob')],
        ['Gender',             get('gender')],
        ['City / State',       get('city') + ', ' + get('state')],
        ['Area of Interest',   radio('area_of_interest')],
        ['Availability',       radio('availability')],
        ['Hours / Week',       get('hours_per_week') + ' hrs'],
        ['Skills',             get('skills') || '—'],
        ['Prior Experience',   radio('prior_experience')],
      ];

      let html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">';
      rows.forEach(([label, value]) => {
        html += `<tr>
          <td style="padding:9px 12px;border-bottom:1px solid #edf2f7;color:#718096;font-weight:600;white-space:nowrap;">${label}</td>
          <td style="padding:9px 12px;border-bottom:1px solid #edf2f7;color:#2d3748;">${value || '—'}</td>
        </tr>`;
      });
      html += '</table>';
      document.getElementById('volSummaryTable').innerHTML = html;
    }

    /* ── Show Error Banner ── */
    function showError(msgs) {
      errBanner.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>'
        + msgs.join('<br>');
      errBanner.style.display = 'block';
      errBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ── Next Button ── */
    nextBtn.addEventListener('click', () => {
      const errors = validateStep(currentStep);
      if (errors.length) { showError(errors); return; }
      if (currentStep < TOTAL_STEPS) showStep(currentStep + 1);
    });

    /* ── Prev Button ── */
    prevBtn.addEventListener('click', () => {
      if (currentStep > 1) showStep(currentStep - 1);
    });

    /* ── Form Submit ── */
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const errors = validateStep(4);
      if (errors.length) { showError(errors); return; }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting…';

      try {
        const fd = new FormData(form);
        const resp = await fetch('api/submit-volunteer.php', { method: 'POST', body: fd });
        const data = await resp.json();

        if (data.success) {
          // Hide form, show success
          form.style.display         = 'none';
          document.querySelector('.vol-form-steps').style.display = 'none';
          document.querySelector('.vol-form-nav').style.display   = 'none';
          successPnl.style.display   = 'block';
          document.getElementById('volRefBadge').textContent = 'Reference: #' + (data.reference || 'VOL-00001');
        } else {
          showError([data.message || 'Submission failed. Please try again.']);
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Application';
        }
      } catch (err) {
        showError(['Network error. Please check your connection and try again.']);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Application';
      }
    });

    /* ── Init ── */
    showStep(1);

  })();
  </script>

</body>
</html>
