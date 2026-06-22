<?php
// Calculate relative base url path dynamically to support inclusion in nested directories
$work_base_url = '';
$request_uri = $_SERVER['REQUEST_URI'];
$project_name = 'gloabl-rise';
$pos = strpos($request_uri, $project_name);
if ($pos !== false) {
    $work_base_url = substr($request_uri, 0, $pos + strlen($project_name)) . '/';
} else {
    $work_base_url = '/';
}

// Define the initiatives data array for cleaner rendering
$initiatives = [
    [
        'label' => 'Animal Welfare',
        'image' => 'assets/images/animal_welfare.png',
        'alt' => 'Volunteers caring for street animals',
        'link' => 'pages/animal-welfare.php'
    ],
    [
        'label' => 'Disaster Management',
        'image' => 'assets/images/disaster_relief.png',
        'alt' => 'Disaster relief workers distributing aid',
        'link' => 'pages/disaster-management.php'
    ],
    [
        'label' => 'Educating Slum Children',
        'image' => 'assets/images/slum_education.png',
        'alt' => 'Teacher conducting outdoor classes in urban slum',
        'link' => 'pages/educating-slum-children.php'
    ],
    [
        'label' => 'Health Projects',
        'image' => 'assets/images/health_projects.png',
        'alt' => 'Doctor examining children in free health camp',
        'link' => 'pages/health-projects.php'
    ],
    [
        'label' => 'Persons with Disabilities',
        'image' => 'assets/images/disabled_persons.png',
        'alt' => 'Empowered person in wheelchair working on computer',
        'link' => 'pages/persons-with-disabilities.php'
    ],
    [
        'label' => 'Rural Children Education',
        'image' => 'assets/images/rural_education.png',
        'alt' => 'Happy children sitting in a rural classroom',
        'link' => 'pages/rural-children-education.php'
    ],
    [
        'label' => 'Senior Citizen Care',
        'image' => 'assets/images/senior_care.png',
        'alt' => 'Volunteer caring for elderly lady',
        'link' => 'pages/senior-citizen-care.php'
    ],
    [
        'label' => 'Swacch Bharat Mission',
        'image' => 'assets/images/swachh_bharat.png',
        'alt' => 'Volunteers cleaning public park for hygiene',
        'link' => 'pages/swacch-bharat-mission.php'
    ],
    [
        'label' => 'Women Empowerment',
        'image' => 'assets/images/women_empowerment.png',
        'alt' => 'Women learning vocational skills in workshop',
        'link' => 'pages/women-empowerment.php'
    ]
];
?>
<!-- Our Work Initiatives Component -->
<section class="our-work-section" id="ourWorkSection" aria-label="Our Welfare Initiatives">
  <div class="header-container">
    
    <!-- Title Section -->
    <h2 class="our-work-title">How we work to make a difference</h2>
    
    <!-- Circles Row/Grid -->
    <div class="work-grid">
      <?php foreach ($initiatives as $item): ?>
        <a href="<?php echo $work_base_url . $item['link']; ?>" class="work-item" aria-label="<?php echo htmlspecialchars($item['label']); ?>">
          <div class="work-img-wrapper">
            <img src="<?php echo $work_base_url . $item['image']; ?>" 
                 alt="<?php echo htmlspecialchars($item['alt']); ?>" 
                 class="work-img" 
                 loading="lazy">
          </div>
          <span class="work-label"><?php echo htmlspecialchars($item['label']); ?></span>
        </a>
      <?php endforeach; ?>
    </div>

  </div>
</section>
