<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Job Feed</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="top-panel">
  Trabawho
</header>

<div class="main-container">

  <!-- Left: Job Cards + Search -->
  <div class="left-panel">
    <h1>Hiring</h1>     

    <!-- Search bar -->
    <input type="text" id="job-search" placeholder="Search by title, location or type..." onkeyup="filterJobs()">

    <!-- Scrollable Job Feed -->
    <div class="job-feed-scrollable">
      <?php
      // Database connection
      $conn = new mysqli("localhost", "root", "", "trabawho");
      if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

      $sql = "SELECT * FROM jobs ORDER BY id DESC";
      $result = $conn->query($sql);

      $jobsArray = [];
      $firstJob = null;

      if ($result->num_rows > 0) {
          while($job = $result->fetch_assoc()) {
              $jobsArray[] = $job;
              if (!$firstJob) $firstJob = $job;

              $shortDesc = strlen($job['description']) > 100 ? substr($job['description'],0,100).'...' : $job['description'];

              echo "
              <div class='job-card' data-title='{$job['title']}' data-location='{$job['location']}' data-type='{$job['type_work']}' onclick='showDetails({$job['id']})'>
                  <h3>{$job['title']}</h3>
                  <p><strong>Location:</strong> {$job['location']}</p>
                  <p><strong>Work Type:</strong> {$job['type_work']}</p>
                  <p><strong>Salary:</strong> {$job['salary']}</p>
                  <p class='job-preview-desc'>{$shortDesc}</p>
              </div>
              ";
          }
      } else {
          echo "<p>No jobs found.</p>";
      }

      $conn->close();
      ?>
    </div>
  </div>

  <!-- Right: Job Details -->
  <div class="job-details-panel" id="job-details-panel">
    <?php if (!empty($firstJob)) { ?>
      <h2><?php echo $firstJob['title']; ?></h2>
      <p><strong>Location:</strong> <?php echo $firstJob['location']; ?></p>
      <p><strong>Type of Work:</strong> <?php echo $firstJob['type_work']; ?></p>
      <p><strong>Salary:</strong> <?php echo $firstJob['salary']; ?></p>
      <p><strong>Hours per Week:</strong> <?php echo $firstJob['hours']; ?></p>
      <p><strong>Education:</strong> <?php echo $firstJob['education']; ?></p>
      <p><strong>Status:</strong> <?php echo $firstJob['status']; ?></p>
      <p><strong>Description:</strong> <?php echo $firstJob['description']; ?></p>
      <p><strong>Skills:</strong> <?php echo $firstJob['skills']; ?></p>
    <?php } else { ?>
      <p>Click a job card to view full details here.</p>
    <?php } ?>
  </div>

</div>

<script>
const jobs = <?php echo json_encode($jobsArray); ?>;

function showDetails(id) {
    const job = jobs.find(j => j.id == id);
    const panel = document.getElementById('job-details-panel');
    panel.innerHTML = `
        <h2>${job.title}</h2>
        <p><strong>Location:</strong> ${job.location}</p>
        <p><strong>Type of Work:</strong> ${job.type_work}</p>
        <p><strong>Salary:</strong> ${job.salary}</p>
        <p><strong>Hours per Week:</strong> ${job.hours}</p>
        <p><strong>Education:</strong> ${job.education}</p>
        <p><strong>Status:</strong> ${job.status}</p>
        <p><strong>Description:</strong> ${job.description}</p>
        <p><strong>Skills:</strong> ${job.skills}</p>
    `;
}

function filterJobs() {
    const input = document.getElementById('job-search').value.toLowerCase();
    const cards = document.querySelectorAll('.job-card');
    cards.forEach(card => {
        const title = card.getAttribute('data-title').toLowerCase();
        const location = card.getAttribute('data-location').toLowerCase();
        const type = card.getAttribute('data-type').toLowerCase();
        card.style.display = (title.includes(input) || location.includes(input) || type.includes(input)) ? 'flex' : 'none';
    });
}
</script>

</body>
</html>
