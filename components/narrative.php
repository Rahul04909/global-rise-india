<?php
// Calculate relative base url path dynamically to support inclusion in nested directories
$narrative_base_url = '';
$request_uri = $_SERVER['REQUEST_URI'];
$project_name = 'gloabl-rise';
$pos = strpos($request_uri, $project_name);
if ($pos !== false) {
    $narrative_base_url = substr($request_uri, 0, $pos + strlen($project_name)) . '/';
} else {
    $narrative_base_url = '/';
}
?>
<!-- Narrative Component -->
<section class="narrative-section" id="narrativeSection" aria-label="Our Core Social Narrative">
  <div class="header-container">
    
    <!-- Title Section -->
    <h2 class="narrative-title">The Global Rise Foundation <span class="title-narrative-highlight">Narrative</span></h2>
    <div class="narrative-badge">Primary cause</div>

    <!-- Narrative Infographic Grid -->
    <div class="narrative-grid">
      
      <!-- Left Column: Primary Cause (1) -->
      <div class="narrative-col narrative-col-left">
        <div class="narrative-block cause-blue">
          <div class="narrative-block-header">
            <span class="narrative-circle-num">1</span>
            <h3 class="narrative-block-title">Food for education (primary cause)</h3>
          </div>
          <ul class="narrative-list">
            <li>
              <strong>Core Mid Day Meal program</strong> including kitchen facilities (vehicles, salaries, trainings etc.)
            </li>
            <li>
              <strong>Other supporting programs</strong>
              <ul class="narrative-sub-list">
                <li>ICDS: Anganwadi feeding program (children in formative age group 3-6Y)</li>
                <li>Other meal programs: Milk distribution (in association with Govt. of Karnataka), breakfast feeding</li>
                <li>Infrastructure: Minimal upgrades supplementing core MDM program (seating areas with roof, infrastructure to serve (trolleys), utensils)</li>
              </ul>
            </li>
          </ul>
        </div>
      </div>

      <!-- Center Column: Infographic Key Circle Image -->
      <div class="narrative-center">
        <img src="<?php echo $narrative_base_url; ?>assets/key-circle.png" alt="Social Causes supported by TGRF Infographic" class="key-circle-img" loading="lazy">
      </div>

      <!-- Right Column: Ancillary Causes (2 & 3) -->
      <div class="narrative-col narrative-col-right">
        
        <!-- Cause 2: Sustainability -->
        <div class="narrative-block cause-green">
          <div class="narrative-block-header">
            <span class="narrative-circle-num">2</span>
            <h3 class="narrative-block-title">Sustainability (ancillary cause)</h3>
          </div>
          <ul class="narrative-list">
            <li><strong>Energy:</strong> Shift to renewable sources of energy</li>
            <li><strong>Water:</strong> Minimize non-cooking freshwater usage</li>
            <li><strong>Waste:</strong> Effective waste management</li>
          </ul>
        </div>

        <!-- Cause 3: Education beyond MDM -->
        <div class="narrative-block cause-orange">
          <div class="narrative-block-header">
            <span class="narrative-circle-num">3</span>
            <h3 class="narrative-block-title">Education beyond MDM (ancillary cause)</h3>
          </div>
          <ul class="narrative-list">
            <li>
              <strong>Scholarship program:</strong> Financial aid for government school children and MDM beneficiaries seeking higher education / specialized skills training
            </li>
          </ul>
        </div>

      </div>

    </div>

  </div>
</section>
