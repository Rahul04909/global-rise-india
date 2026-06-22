<?php
// Calculate relative base url path dynamically to support inclusion in nested directories
$form_base_url = '';
$request_uri = $_SERVER['REQUEST_URI'];
$project_name = 'gloabl-rise';
$pos = strpos($request_uri, $project_name);
if ($pos !== false) {
    $form_base_url = substr($request_uri, 0, $pos + strlen($project_name)) . '/';
} else {
    $form_base_url = '/';
}
?>
<!-- Online Donations Form Component -->
<div class="donate-page-section" id="donateFormSection">
  <div class="header-container">

    <!-- Cause Selector Tabs -->
    <div class="donate-tabs" role="tablist" aria-label="Donation Causes">
      <button class="donate-tab-btn active" data-tab="feed" role="tab" aria-selected="true">
        <i class="fa-solid fa-bowl-food"></i>
        <span>Feed a Child</span>
      </button>
      <button class="donate-tab-btn" data-tab="slum" role="tab" aria-selected="false">
        <i class="fa-solid fa-school"></i>
        <span>Educate Slum Children</span>
      </button>
      <button class="donate-tab-btn" data-tab="disaster" role="tab" aria-selected="false">
        <i class="fa-solid fa-house-chimney-crack"></i>
        <span>Disaster Relief</span>
      </button>
      <button class="donate-tab-btn" data-tab="women" role="tab" aria-selected="false">
        <i class="fa-solid fa-person-dress"></i>
        <span>Women Empowerment</span>
      </button>
      <button class="donate-tab-btn" data-tab="disabled" role="tab" aria-selected="false">
        <i class="fa-solid fa-wheelchair"></i>
        <span>Support Disabilities</span>
      </button>
      <button class="donate-tab-btn" data-tab="senior" role="tab" aria-selected="false">
        <i class="fa-solid fa-person-cane"></i>
        <span>Senior Citizen Care</span>
      </button>
    </div>

    <!-- Main Donation Card Form -->
    <div class="donate-form-card">
      
      <!-- Success Panel (Hidden by default) -->
      <div id="donSuccessPanel" style="display: none; text-align: center; padding: 40px 20px;">
          <div class="mb-4">
              <i class="fa-solid fa-circle-check text-success" style="font-size: 5rem; color: #28a745;"></i>
          </div>
          <h2 class="text-success font-weight-bold mb-3" style="color: #28a745; font-family: 'Montserrat', sans-serif;">Thank You for Your Contribution!</h2>
          <p class="lead mb-4" style="font-size: 1.1rem; color: #4a5568; line-height: 1.6;">
              Your payment has been securely processed. An official receipt and confirmation email have been sent to your email address.
          </p>
          <div class="bg-light p-4 rounded border mb-4" style="max-width: 450px; margin: 0 auto; background-color: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
              <span class="d-block text-muted small uppercase font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px; color: #718096;">TRANSACTION REFERENCE</span>
              <strong id="donRefBadge" class="text-primary" style="font-size: 1.3rem; color: #1b5182; font-family: monospace;">DON-00000</strong>
          </div>
          <p class="text-muted small">
              If you requested an 80(G) tax exemption certificate, it will be dispatched within 15 working days.
          </p>
          <div class="mt-4">
              <a href="index.php" class="btn-donate-submit" style="display: inline-flex; width: auto; padding: 12px 30px; text-decoration: none; align-items: center; justify-content: center;">
                  <i class="fa-solid fa-house mr-2" style="margin-right: 8px;"></i> Back to Home
              </a>
          </div>
      </div>

      <form id="donationForm" method="POST" action="#" novalidate>
        
        <!-- Hidden Inputs for Form State tracking -->
        <input type="hidden" name="selected_cause" id="hiddenCause" value="feed">
        <input type="hidden" name="selected_amount" id="hiddenAmount" value="4500">
        <input type="hidden" name="addon_active" id="hiddenAddonActive" value="true">
        <input type="hidden" name="addon_amount" id="hiddenAddonAmount" value="1125">
        <input type="hidden" name="total_donation" id="hiddenTotal" value="5625">

        <!-- SECTION 1: CITIZENSHIP & DONATION TYPE -->
        <div class="donate-form-section-title">
          <i class="fa-solid fa-user-gear"></i> Donor Configuration
        </div>

        <div class="donate-radio-group">
          <!-- Citizenship Selectors -->
          <div class="donate-radio-group-title" style="width: 100%; font-size: 12px; font-weight: 700; color: #718096; margin-bottom: 5px;">Select Your Citizenship:</div>
          
          <label class="donate-selector-label selected" id="citizenIndianLabel">
            <input type="radio" name="citizenship" value="indian" checked>
            <span class="donate-radio-circle"></span>
            <span>Indian Citizen</span>
          </label>
          
          <label class="donate-selector-label" id="citizenNriLabel">
            <input type="radio" name="citizenship" value="nri">
            <span class="donate-radio-circle"></span>
            <span>NRI (Indian Citizen Residing Abroad)</span>
          </label>
          
          <label class="donate-selector-label" id="citizenForeignLabel">
            <input type="radio" name="citizenship" value="foreign">
            <span class="donate-radio-circle"></span>
            <span>Foreign National</span>
          </label>
        </div>

        <div class="donate-radio-group">
          <!-- Donation Interval -->
          <div class="donate-radio-group-title" style="width: 100%; font-size: 12px; font-weight: 700; color: #718096; margin-bottom: 5px;">Select Donation Type:</div>
          
          <label class="donate-selector-label selected" id="typeOnceLabel">
            <input type="radio" name="donation_type" value="once" checked>
            <span class="donate-radio-circle"></span>
            <span>Donate Once</span>
          </label>
          
          <label class="donate-selector-label" id="typeMonthlyLabel">
            <input type="radio" name="donation_type" value="monthly">
            <span class="donate-radio-circle"></span>
            <span>Donate Monthly</span>
          </label>
        </div>

        <!-- SECTION 2: CHOOSE DONATION AMOUNT -->
        <div class="donate-form-section-title">
          <i class="fa-solid fa-hand-holding-dollar"></i> Choose Donation Amount
        </div>

        <!-- Dynamic Calculator Alert Box -->
        <div class="donate-summary-alert" id="donateSummaryPanel">
          <i class="fa-solid fa-circle-info"></i>
          <span id="summaryText">I wish to donate ₹ 4,500 to feed 3 child(ren) with mid-day meals for one academic year.</span>
        </div>

        <!-- Predefined Grid Buttons -->
        <div class="donate-amount-grid" id="amountButtonsGrid">
          <!-- Populated dynamically via JS -->
        </div>

        <!-- Other / Custom Amount Input -->
        <div class="donate-custom-amount-wrapper">
          <input type="number" 
                 id="customAmountInput" 
                 class="donate-custom-amount-input" 
                 placeholder="Other / Custom Amount" 
                 min="1" 
                 aria-label="Enter Custom Donation Amount">
        </div>



        <!-- SECTION 3: PERSONAL DETAILS -->
        <div class="donate-form-section-title">
          <i class="fa-solid fa-address-card"></i> Personal Details
        </div>

        <div class="donate-form-grid">
          <!-- Name Field (Title dropdown inside) -->
          <div class="donate-form-group">
            <label class="donate-form-label" for="donorName">Full Name<span class="required">*</span></label>
            <div class="donate-name-wrapper">
              <select name="donor_title" id="donorTitle" class="donate-input-field donate-title-select" aria-label="Title">
                <option value="Mr.">Mr.</option>
                <option value="Ms.">Ms.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Dr.">Dr.</option>
                <option value="Prof.">Prof.</option>
              </select>
              <input type="text" name="donor_name" id="donorName" class="donate-input-field" placeholder="Full Name" required>
            </div>
            <div class="donate-error-message" id="error-donorName">Please enter your full name.</div>
          </div>

          <!-- Email Field -->
          <div class="donate-form-group">
            <label class="donate-form-label" for="donorEmail">Email ID<span class="required">*</span></label>
            <input type="email" name="donor_email" id="donorEmail" class="donate-input-field" placeholder="Email Address" required>
            <div class="donate-error-message" id="error-donorEmail">Please enter a valid email address.</div>
          </div>

          <!-- WhatsApp Mobile No -->
          <div class="donate-form-group">
            <label class="donate-form-label" for="donorMobile">Mobile No (WhatsApp)<span class="required">*</span></label>
            <input type="tel" name="donor_mobile" id="donorMobile" class="donate-input-field" placeholder="WhatsApp Number" required>
            <div class="donate-error-message" id="error-donorMobile">Please enter a valid 10-digit mobile number.</div>
          </div>

          <!-- Date of Birth -->
          <div class="donate-form-group">
            <label class="donate-form-label" for="donorDob">Date of Birth<span class="required">*</span></label>
            <input type="date" name="donor_dob" id="donorDob" class="donate-input-field" required>
            <div class="donate-error-message" id="error-donorDob">Please enter your date of birth.</div>
          </div>

          <!-- Alternate Mobile -->
          <div class="donate-form-group">
            <label class="donate-form-label" for="donorAltMobile">Alternate Mobile No</label>
            <input type="tel" name="donor_alt_mobile" id="donorAltMobile" class="donate-input-field" placeholder="Alternate Mobile (Optional)">
          </div>

          <!-- 80G Certificate checkbox -->
          <div class="donate-form-group col-span-2">
            <div class="donate-exemption-checkbox-wrapper">
              <input type="checkbox" name="request_80g" id="request80gCheckbox" class="donate-addon-checkbox">
              <label class="donate-form-label" for="request80gCheckbox" style="cursor: pointer;">I would like to receive 80(G) tax exemption certificate (For Indian citizens only)</label>
            </div>
            
            <!-- Sliding PAN Card Form Accordion -->
            <div class="donate-exemption-subform" id="exemptionSubform">
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="donate-form-group">
                  <label class="donate-form-label" for="donorPan">PAN Card Number<span class="required">*</span></label>
                  <input type="text" name="donor_pan" id="donorPan" class="donate-input-field" placeholder="PAN Number (10 characters)" maxlength="10" style="text-transform: uppercase;">
                  <div class="donate-error-message" id="error-donorPan">Please enter a valid 10-digit PAN number.</div>
                </div>
                <div class="donate-form-group col-span-2" style="grid-column: span 2;">
                  <label class="donate-form-label" for="donorAddress">Billing Address<span class="required">*</span></label>
                  <textarea name="donor_address" id="donorAddress" class="donate-input-field" rows="2" placeholder="Full Postal Address" style="resize: vertical;"></textarea>
                  <div class="donate-error-message" id="error-donorAddress">Please enter your billing address.</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 4: CONSENT -->
        <div class="donate-form-section-title">
          <i class="fa-solid fa-circle-check"></i> Consent & Confirmation
        </div>

        <!-- Consent Checklist -->
        <label class="donate-checkbox-consent">
          <input type="checkbox" id="consentCheckbox" checked required>
          <span>I have read through the website <a href="#" target="_blank">Privacy Policy</a> & <a href="#" target="_blank">Terms and Conditions</a> to make a donation to The Global Rise Foundation.</span>
        </label>
        <div class="donate-error-message" id="error-consent" style="margin-top: 5px;">You must consent to the Terms and Conditions to proceed.</div>

        <!-- Submit Panel -->
        <div class="donate-submit-wrapper">
          <button type="submit" class="btn-donate-submit" id="donateSubmitBtn">
            <span>Proceed to Donate ₹<span id="submitTotalSpan">5,625</span></span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
          
          <!-- Trust Badges -->
          <div class="donate-trust-badges">
            <svg class="donate-badge-img" viewBox="0 0 120 30" width="120" height="30" aria-hidden="true">
              <rect x="0" y="0" width="120" height="30" rx="4" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="1"/>
              <path d="M15 11h-1V9a4 4 0 1 0-8 0v2H5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-8a1 1 0 0 0-1-1zM8 9a2 2 0 1 1 4 0v2H8V9zm4 8a1 1 0 0 1-2 0 1 1 0 0 1 2 0z" fill="#475569"/>
              <text x="28" y="19" font-family="'Montserrat', sans-serif" font-size="9" font-weight="700" fill="#475569">SECURE SSL</text>
            </svg>
            <svg class="donate-badge-img" viewBox="0 0 120 30" width="120" height="30" aria-hidden="true">
              <rect x="0" y="0" width="120" height="30" rx="4" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="1"/>
              <text x="12" y="19" font-family="'Montserrat', sans-serif" font-size="9" font-weight="700" fill="#1b5182">PCI COMPLIANT</text>
            </svg>
          </div>
        </div>

      </form>
    </div>

  </div>
</div>

<!-- Razorpay Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- Form Logic Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // 1. Social Cause Config Details
  const tabData = {
    feed: {
      title: "Feed a Child (Mid-Day Meal)",
      unitCost: 1500,
      unitName: "child(ren) with mid-day meals",
      timeFrame: "for one academic year",
      amounts: [4500, 9000, 13500, 18000, 22500, 27000, 31500, 45000, 90000, 135000, 180000, 225000, 270000, 315000],
      addonText: "Support our new initiative - Morning Nutrition Programme [Breakfast] by donating ₹ 375 to support a child along with Mid-Day Meals",
      addonCost: 375
    },
    slum: {
      title: "Educate Slum Children",
      unitCost: 2500,
      unitName: "slum child(ren) with specialized education support",
      timeFrame: "for one academic year",
      amounts: [5000, 10000, 15000, 20000, 25000, 30000, 40000, 50000, 75000, 100000, 150000, 200000, 250000, 300000],
      addonText: "Add a School Supply Kit (uniform, school bag, textbooks, stationary) by donating ₹ 500 per child",
      addonCost: 500
    },
    disaster: {
      title: "Disaster Relief & Mitigation",
      unitCost: 3000,
      unitName: "families with emergency hygiene & food relief kits",
      timeFrame: "during crises",
      amounts: [3000, 6000, 9000, 12000, 15000, 18000, 24000, 30000, 60000, 90000, 120000, 150000, 210000, 300000],
      addonText: "Add a Warm Blanket & Emergency Bedding to the kit by donating ₹ 400 per family kit",
      addonCost: 400
    },
    women: {
      title: "Women Empowerment",
      unitCost: 4000,
      unitName: "woman/women with vocational tailoring & business training",
      timeFrame: "to build self-reliance",
      amounts: [4000, 8000, 12000, 16000, 20000, 24000, 28000, 40000, 80000, 120000, 160000, 200000, 240000, 320000],
      addonText: "Equip a trainee with a brand new Sewing Machine upon graduation by donating ₹ 3000 per unit",
      addonCost: 3000
    },
    disabled: {
      title: "Support Persons with Disabilities",
      unitCost: 5000,
      unitName: "individual(s) with custom-fitted wheelchairs and mobility aids",
      timeFrame: "to restore independence",
      amounts: [5000, 10000, 15000, 20000, 25000, 30000, 35000, 50000, 100000, 150000, 200000, 250000, 300000, 350000],
      addonText: "Add physiotherapy and rehabilitation training support by donating ₹ 800 per individual",
      addonCost: 800
    },
    senior: {
      title: "Senior Citizen Care",
      unitCost: 2000,
      unitName: "senior citizen(s) with monthly nutrition, medicines & health checks",
      timeFrame: "to ensure a life of dignity",
      amounts: [4000, 8000, 12000, 16000, 20000, 24000, 28000, 40000, 80000, 120000, 160000, 200000, 240000, 300000],
      addonText: "Add eye care treatment and free cataract surgery support by donating ₹ 1200 per senior citizen",
      addonCost: 1200
    }
  };

  // State Management variables
  let currentCause = 'feed';
  let baseAmount = 4500;

  // DOM elements cache
  const tabs = document.querySelectorAll('.donate-tab-btn');
  const amountGrid = document.getElementById('amountButtonsGrid');
  const customInput = document.getElementById('customAmountInput');
  const summaryText = document.getElementById('summaryText');
  const submitBtn = document.getElementById('donateSubmitBtn');
  const submitSpan = document.getElementById('submitTotalSpan');
  
  // Exemption certificate elements
  const request80g = document.getElementById('request80gCheckbox');
  const exemptionSubform = document.getElementById('exemptionSubform');
  const panInput = document.getElementById('donorPan');
  const addressInput = document.getElementById('donorAddress');

  // Radio button elements (Citizenship & Interval Types)
  const radioLabels = document.querySelectorAll('.donate-selector-label');

  // 2. Tab Change Logic
  tabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Toggle active classes on tab headers
      tabs.forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      this.classList.add('active');
      this.setAttribute('aria-selected', 'true');

      // Update cause state
      currentCause = this.dataset.tab;
      document.getElementById('hiddenCause').value = currentCause;

      // Select default amount for the new cause (the first item in array)
      const data = tabData[currentCause];
      baseAmount = data.amounts[0];
      customInput.value = ''; // Reset custom input

      // Render Grid Buttons & recalculate
      renderGrid();
      recalculateAll();
    });
  });

  // 3. Render Donation Amount Grid Dynamically
  function renderGrid() {
    amountGrid.innerHTML = '';
    const data = tabData[currentCause];
    
    data.amounts.forEach(amt => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'donate-amount-btn';
      if (amt === baseAmount) {
        btn.className += ' active';
      }
      btn.textContent = '₹ ' + amt.toLocaleString('en-IN');
      btn.dataset.value = amt;

      btn.addEventListener('click', function() {
        // Remove active class from sibling buttons
        const activeBtn = amountGrid.querySelector('.donate-amount-btn.active');
        if (activeBtn) activeBtn.classList.remove('active');

        // Make current active
        this.classList.add('active');
        baseAmount = parseInt(this.dataset.value);
        
        // Reset custom input
        customInput.value = '';

        // Recalculate everything
        recalculateAll();
      });

      amountGrid.appendChild(btn);
    });
  }

  // 4. Custom Amount Input Listeners
  customInput.addEventListener('input', function() {
    const val = parseInt(this.value);
    
    // Deselect all grid buttons
    const activeBtn = amountGrid.querySelector('.donate-amount-btn.active');
    if (activeBtn) activeBtn.classList.remove('active');

    if (!isNaN(val) && val > 0) {
      baseAmount = val;
    } else {
      baseAmount = 0;
    }

    recalculateAll();
  });

  // 5. Recalculation Engine
  function recalculateAll() {
    const data = tabData[currentCause];
    
    // 5a. Calculate units (e.g. number of children supported)
    let calculatedUnits = 0;
    if (baseAmount >= data.unitCost) {
      calculatedUnits = Math.floor(baseAmount / data.unitCost);
    }

    // 5b. Update dynamic alert sentence text
    if (calculatedUnits > 0) {
      summaryText.innerHTML = `I wish to donate <strong>₹ ${baseAmount.toLocaleString('en-IN')}</strong> to support <strong>${calculatedUnits}</strong> ${data.unitName} ${data.timeFrame}.`;
    } else {
      summaryText.innerHTML = `I wish to donate <strong>₹ ${baseAmount.toLocaleString('en-IN')}</strong> to support social welfare initiatives.`;
    }

    const finalDonationTotal = baseAmount;

    // 5e. Update UI variables and hidden states
    document.getElementById('hiddenAmount').value = baseAmount;
    document.getElementById('hiddenAddonActive').value = false;
    document.getElementById('hiddenAddonAmount').value = 0;
    document.getElementById('hiddenTotal').value = finalDonationTotal;

    submitSpan.textContent = finalDonationTotal.toLocaleString('en-IN');
  }

  // 8. Custom Radio Button Click Handlers (Adding active styling)
  radioLabels.forEach(label => {
    const radioInput = label.querySelector('input[type="radio"]');
    if (!radioInput) return;

    // Set listener on click
    label.addEventListener('click', function(e) {
      // Find siblings and clean classes
      const nameGroup = radioInput.getAttribute('name');
      const groupLabels = document.querySelectorAll(`.donate-selector-label input[name="${nameGroup}"]`);
      groupLabels.forEach(inp => {
        inp.closest('.donate-selector-label').classList.remove('selected');
      });

      // Highlight target card label
      this.classList.add('selected');
    });
  });

  // 9. 80(G) Tax Exemption Form Accordion Toggle
  request80g.addEventListener('change', function() {
    if (this.checked) {
      exemptionSubform.classList.add('open');
      panInput.setAttribute('required', 'true');
      addressInput.setAttribute('required', 'true');
    } else {
      exemptionSubform.classList.remove('open');
      panInput.removeAttribute('required');
      addressInput.removeAttribute('required');
      
      // Reset inputs & errors
      panInput.value = '';
      addressInput.value = '';
      panInput.classList.remove('error');
      addressInput.classList.remove('error');
      document.getElementById('error-donorPan').style.display = 'none';
      document.getElementById('error-addressInput')?.remove();
      document.getElementById('error-donorAddress').style.display = 'none';
    }
  });



  // 11. Form Validation Engine on Submit
  const form = document.getElementById('donationForm');
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    let isValid = true;

    // 11a. Full Name Validation
    const nameVal = document.getElementById('donorName').value.trim();
    if (nameVal.length < 2) {
      isValid = false;
      document.getElementById('donorName').classList.add('error');
      document.getElementById('error-donorName').style.display = 'block';
    } else {
      document.getElementById('donorName').classList.remove('error');
      document.getElementById('error-donorName').style.display = 'none';
    }

    // 11b. Email ID Validation
    const emailVal = document.getElementById('donorEmail').value.trim();
    const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailReg.test(emailVal)) {
      isValid = false;
      document.getElementById('donorEmail').classList.add('error');
      document.getElementById('error-donorEmail').style.display = 'block';
    } else {
      document.getElementById('donorEmail').classList.remove('error');
      document.getElementById('error-donorEmail').style.display = 'none';
    }

    // 11c. WhatsApp Mobile Validation
    const mobileVal = document.getElementById('donorMobile').value.trim();
    const mobileReg = /^[0-9]{10}$/; // standard 10 digit check
    if (!mobileReg.test(mobileVal)) {
      isValid = false;
      document.getElementById('donorMobile').classList.add('error');
      document.getElementById('error-donorMobile').style.display = 'block';
    } else {
      document.getElementById('donorMobile').classList.remove('error');
      document.getElementById('error-donorMobile').style.display = 'none';
    }

    // 11d. Date of Birth Validation
    const dobVal = document.getElementById('donorDob').value;
    if (!dobVal) {
      isValid = false;
      document.getElementById('donorDob').classList.add('error');
      document.getElementById('error-donorDob').style.display = 'block';
    } else {
      document.getElementById('donorDob').classList.remove('error');
      document.getElementById('error-donorDob').style.display = 'none';
    }

    // 11e. 80(G) PAN / Address validation (if toggled)
    if (request80g.checked) {
      const panVal = panInput.value.trim().toUpperCase();
      const panReg = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/; // Indian PAN standard pattern
      if (!panReg.test(panVal)) {
        isValid = false;
        panInput.classList.add('error');
        document.getElementById('error-donorPan').style.display = 'block';
      } else {
        panInput.classList.remove('error');
        document.getElementById('error-donorPan').style.display = 'none';
      }

      const addressVal = addressInput.value.trim();
      if (addressVal.length < 8) {
        isValid = false;
        addressInput.classList.add('error');
        document.getElementById('error-donorAddress').style.display = 'block';
      } else {
        addressInput.classList.remove('error');
        document.getElementById('error-donorAddress').style.display = 'none';
      }
    }

    // 11g. Consent Checklist
    const consent = document.getElementById('consentCheckbox').checked;
    if (!consent) {
      isValid = false;
      document.getElementById('error-consent').style.display = 'block';
    } else {
      document.getElementById('error-consent').style.display = 'none';
    }

    // 12. Submit process (Razorpay Integration)
    if (isValid) {
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.75';
      submitBtn.querySelector('span').textContent = 'Initializing Payment...';

      const showSubmitError = (msg) => {
        alert(msg);
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.querySelector('span').textContent = 'Proceed to Donate ₹' + parseInt(document.getElementById('hiddenTotal').value).toLocaleString('en-IN');
      };

      (async () => {
        try {
          // Step 1: Create Razorpay Order
          const fd = new FormData();
          fd.append('amount', document.getElementById('hiddenTotal').value);
          fd.append('selected_cause', currentCause);
          fd.append('donation_type', document.querySelector('[name="donation_type"]:checked').value);

          const orderResp = await fetch('api/create-donation-order.php', {
            method: 'POST',
            body: fd
          });
          const orderData = await orderResp.json();

          if (!orderData.success) {
            showSubmitError(orderData.message || 'Payment Order generation failed.');
            return;
          }

          // Step 2: Open Razorpay Checkout modal
          const options = {
            "key": orderData.key_id,
            "amount": orderData.amount,
            "currency": "INR",
            "name": "The Global Rise Foundation",
            "description": "Donation Support for " + tabData[currentCause].title,
            "image": "assets/logo.png",
            "order_id": orderData.order_id,
            "handler": async function (paymentResp) {
              submitBtn.querySelector('span').textContent = 'Verifying Transaction...';

              try {
                // Step 3: Send signatures and form inputs to backend submission API
                const submitFd = new FormData(form);
                submitFd.append('razorpay_payment_id', paymentResp.razorpay_payment_id);
                submitFd.append('razorpay_order_id', paymentResp.razorpay_order_id);
                submitFd.append('razorpay_signature', paymentResp.razorpay_signature);

                // Add values that might not be raw form inputs
                submitFd.set('selected_cause', currentCause);
                submitFd.set('total_donation', document.getElementById('hiddenTotal').value);

                const regResp = await fetch('api/submit-donation.php', {
                  method: 'POST',
                  body: submitFd
                });
                const regData = await regResp.json();

                if (regData.success) {
                  form.style.display = 'none';
                  document.querySelector('.donate-tabs').style.display = 'none';
                  const successPnl = document.getElementById('donSuccessPanel');
                  successPnl.style.display = 'block';
                  document.getElementById('donRefBadge').textContent = regData.reference || 'DON-00001';
                  successPnl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                  showSubmitError(regData.message || 'Payment verified but donation log failed.');
                }
              } catch (err) {
                showSubmitError('Network connection error during payment validation. Please contact us with Payment ID: ' + paymentResp.razorpay_payment_id);
              }
            },
            "prefill": {
              "name": document.getElementById('donorName').value.trim(),
              "email": document.getElementById('donorEmail').value.trim(),
              "contact": document.getElementById('donorMobile').value.trim()
            },
            "theme": {
              "color": "#1b5182"
            },
            "modal": {
              "ondismiss": function() {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.querySelector('span').textContent = 'Proceed to Donate ₹' + parseInt(document.getElementById('hiddenTotal').value).toLocaleString('en-IN');
              }
            }
          };

          const rzp = new Razorpay(options);
          rzp.open();

        } catch (err) {
          showSubmitError('Failed to communicate with payment server. Please verify your connection.');
        }
      })();
    }
  });

  // Initial Load Trigger
  renderGrid();
  recalculateAll();

});
</script>
