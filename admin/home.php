<?php include('db_connect.php'); ?>
<?php
function ordinal_suffix1($num)
{
  $num = $num % 100; // protect against large numbers
  if ($num < 11 || $num > 13) {
    switch ($num % 10) {
      case 1:
        return $num . 'st';
      case 2:
        return $num . 'nd';
      case 3:
        return $num . 'rd';
    }
  }
  return $num . 'th';
}
$astat = array("Not Yet Started", "On-going", "Closed");
?>
<?php
include('db_connect.php'); // Include your database connection script

$questionCounts = array(); // Array to store question counts

// Fetch question counts from evaluation_answers
$questionQuery = $conn->query("SELECT question_list.id, question_list.question, COUNT(evaluation_answers.evaluation_id) AS count FROM question_list LEFT JOIN evaluation_answers ON question_list.id = evaluation_answers.question_id GROUP BY question_list.id, question_list.question");
while ($row = $questionQuery->fetch_assoc()) {
  $questionCounts[$row['question']] = $row['count'];
}
?>

<?php
$questionLabels = array();
$answerData = array();

// Combine question counts and evaluation counts
foreach ($questionCounts as $question => $count) {
  $questionLabels[] = $question;
  $answerData[] = $count;
}

// Convert the arrays to JSON for JavaScript
$questionLabelsJSON = json_encode($questionLabels);
$answerDataJSON = json_encode($answerData);
?>


<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<!-- Your HTML code -->
<div class="col-12">
  <div class="card">
    <div class="card-body">
      Welcome <?php echo $_SESSION['login_name'] ?>!
      <br>
      <div class="col-md-5">
        <div class="callout callout-info">
          <h5><b>Academic Year: <?php echo $_SESSION['academic']['year'] . ' ' . (ordinal_suffix1($_SESSION['academic']['semester'])) ?> Semester</b></h5>
          <h6><b>Evaluation Status: <?php echo $astat[$_SESSION['academic']['status']] ?></b></h6>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-sm-6 col-md-4">
    <div class="small-box bg-light shadow-sm border">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM faculty_list ")->num_rows; ?></h3>

        <p>Total Faculties</p>
      </div>
      <div class="icon">
        <i class="fa fa-user-friends"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-4">
    <div class="small-box bg-light shadow-sm border">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM student_list")->num_rows; ?></h3>

        <p>Total Students</p>
      </div>
      <div class="icon">
        <i class="fa ion-ios-people-outline"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-4">
    <div class="small-box bg-light shadow-sm border">
      <div class "inner">
        <h3><?php echo $conn->query("SELECT * FROM users")->num_rows; ?></h3>

        <p>Total Users</p>
      </div>
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-4">
    <div class="small-box bg-light shadow-sm border">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM class_list")->num_rows; ?></h3>

        <p>Total Classes</p>
      </div>
      <div class="icon">
        <i class="fa fa-list-alt"></i>
      </div>
    </div>
  </div>
</div>

<!-- Faculty Select Element -->
<div class="col-12">
  <div class="card">
    <div class="card-body">
      <label for="facultySelect">Select Faculty:</label>
      <select id="facultySelect" name="faculty_id">
        <option value="0">Select a Faculty</option>
        <?php
        // Fetch the list of faculties from your database
        $facultyQuery = $conn->query("SELECT * FROM faculty_list");
        while ($faculty = $facultyQuery->fetch_assoc()) {
          echo '<option value="' . $faculty['id'] . '">' . $faculty['firstname'] . ' ' . $faculty['lastname'] . '</option>';
        }
        ?>
      </select>
    </div>
  </div>
</div>

<!-- Chart -->
<div class="col-12">
  <div class="card">
    <div class="card-body">
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <canvas id="answerChart" width="100" height="40"></canvas>
    </div>
  </div>
</div>

<script>
  var ctx = document.getElementById('answerChart').getContext('2d');
  var answerChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo $questionLabelsJSON; ?>,
      datasets: [{
        label: 'Answer Count',
        data: <?php echo $answerDataJSON; ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        x: [{
          scaleLabel: {
            display: true,
            labelString: 'Questions'
          }
        }],
        y: [{
          scaleLabel: {
            display: true,
            labelString: 'Answer Count'
          }
        }]
      }
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Get references to the select element and chart canvas
  const facultySelect = document.getElementById('facultySelect');
  const ctx = document.getElementById('answerChart').getContext('2d');

  // Initialize the chart data
  var answerChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [],
      datasets: [{
        label: 'Answer Count',
        data: [],
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        x: [{
          scaleLabel: {
            display: true,
            labelString: 'Questions'
          }
        }],
        y: [{
          scaleLabel: {
            display: true,
            labelString: 'Answer Count'
          }
        }]
      }
    }
  });

  // Add an event listener to the faculty select element
  facultySelect.addEventListener('change', function () {
    const facultyId = this.value;
    if (facultyId === '0') {
      // Reset the chart if no faculty is selected
      answerChart.data.labels = [];
      answerChart.data.datasets[0].data = [];
      answerChart.update();
    } else {
      // Fetch data for the selected faculty using AJAX
      // Replace 'fetchChartData.php' with the actual server-side script to fetch data
      fetch('fetchChartData.php?faculty_id=' + facultyId)
        .then(response => response.json())
        .then(data => {
          // Update the chart data with the received data
          answerChart.data.labels = data.questionLabels;
          answerChart.data.datasets[0].data = data.answerData;
          answerChart.update();
        })
        .catch(error => {
          console.error('Error fetching data: ', error);
        });
    }
  });
</script>
<div class="col-12">
  <div class="card">
    <div class="card-body">
      <!-- Line Chart -->
      <canvas id="lineChart" width="100" height="40"></canvas>
    </div>
  </div>
</div>
<script>
  var lineCtx = document.getElementById('lineChart').getContext('2d');
  var lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: <?php echo $questionLabelsJSON; ?>,
      datasets: [{
        label: 'Evaluation Rate',
        data: <?php echo $answerDataJSON; ?>,
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 2,
        fill: false, // Make sure the line chart doesn't fill the area below the line
      }]
    },
    options: {
      scales: {
        x: [{
          scaleLabel: {
            display: true,
            labelString: 'Question List'
          }
        }],
        y: [{
          scaleLabel: {
            display: true,
            labelString: 'Evaluation Rate'
          }
        }]
      }
    }
  });
</script>
