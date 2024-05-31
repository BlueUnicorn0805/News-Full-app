<!DOCTYPE html>
<html lang="en" data-layout-mode="dark">
    <head>
        <title>Dashboard | <?= ($app_name) ? $app_name[0]->message : '' ?></title>        
        <?php base_url() . include 'include.php'; ?>  
    </head>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">

            <?php base_url() . include 'header.php'; ?>  
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                            <?php if ($this->session->getFlashdata('error')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="error_msg" class="alert alert-danger">
                                                        <?php echo $this->session->getFlashdata('error'); ?>
                                                    </p>
                                                </div>
                                            <?php } ?>
                                            <?php if ($this->session->getFlashdata('success')) { ?>
                                                <div class="col-sm-6 offset-2">
                                                    <p id="success_msg" class="alert alert-success">
                                                        <?php echo $this->session->getFlashdata('success'); ?>
                                                    </p>
                                                </div>
                                            <?php } ?>
                                <h1 class="m-0 text-dark">Dashboard</h1>
                            </div>        
                        </div>
                    </div>
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                        <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= $count_featured_sections; ?></h3>
                                        <p>Featured Sections</p>
                                    </div>
                                    <div class="icon">
                                        <em class="fas fa-layer-group"></em>
                                    </div>
                                    <a href="<?= APP_URL ?>featured_sections" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                </div>
                            </div>  
                            <?php if (is_category_enabled() == 1) { ?>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><?= $count_category; ?></h3>
                                            <p>Total Category</p>
                                        </div>
                                        <div class="icon">
                                            <em class="fas fa-cube"></em>
                                        </div>
                                        <a href="<?= APP_URL ?>category" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= $count_news; ?></h3>
                                        <p>Total News</p>
                                    </div>
                                    <div class="icon">
                                        <em class="fas fa-newspaper"></em>
                                    </div>
                                    <a href="<?= APP_URL ?>news" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                </div>
                            </div>

                            <?php if (is_breaking_news_enabled() == 1) { ?>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3><?= $count_breaking_news; ?></h3>
                                            <p>Total Breaking News</p>
                                        </div>
                                        <div class="icon">
                                            <em class="fas fa-newspaper"></em>
                                        </div>
                                        <a href="<?= APP_URL ?>breaking_news" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-lg-6 col-6">
                                <div class="small-box">
                                    <div id="NewsPieChart" style="height: 310px; width: 100%"></div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                            <div class="row">
                                <div class="col-lg-6 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3><?= $count_users; ?></h3>
                                            <p>Users</p>
                                        </div>
                                        <div class="icon">
                                            <em class="fas fa-users"></em>
                                        </div>
                                        <a href="<?= APP_URL ?>users" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><?= $count_user_roles; ?></h3>
                                            <p>User Roles</p>
                                        </div>
                                        <div class="icon">
                                            <em class="fas fa-user"></em>
                                        </div>
                                        <a href="<?= APP_URL ?>user_roles" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                    </div>
                                </div>
                            </div>
                                <div class="row">
                                <div class="col-md-6 col-3">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3><?= $count_pages; ?></h3>
                                            <p>Pages</p>
                                        </div>
                                        <div class="icon">
                                            <em class="fas fa-file"></em>
                                        </div>
                                        <a href="<?= APP_URL ?>pages" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-3">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><?= $count_ad_spaces; ?></h3>
                                            <p>Ad Spaces</p>
                                        </div>
                                        <div class="icon">
                                            <em class="fas fa-ad"></em>
                                        </div>
                                        <a href="<?= APP_URL ?>ad_spaces" class="small-box-footer">More info <em class="fas fa-arrow-circle-right"></em></a>
                                    </div>
                                </div>
                            </div>
                            
                            </div> 
                            <div class="col-lg-6 col-6">
                                <div class="small-box">
                                    <div id="LanguageColumnChart" style="height: 400px; width: 100%"></div>
                                </div>
                            </div> 
                            <div class="col-lg-6 col-6">
                                <div class="small-box">
                                    <div id="SurveyLineChart" style="height: 400px; width: 100%"></div>
                                </div>
                            </div>                           

                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script>
			google.charts.load('visualization', "1", {
				packages: ['corechart']
			});
			google.charts.setOnLoadCallback(drawBarChart);
			
			// Pie Chart
			google.charts.setOnLoadCallback(showBarChart);
			function drawBarChart() {
				var data = google.visualization.arrayToDataTable([
					['Category', 'News Count'], 
						<?php 
							foreach ($news_per_category as $row){
							   echo "['".$row['category']."',".$row['news']."],";
							}
						?>
				]);
				var options = {
					title: 'Category wise news',
					is3D: true,
				};
				var chart = new google.visualization.PieChart(document.getElementById('NewsPieChart'));
				chart.draw(data, options);
			}
		</script>
        <script>
			google.charts.load('visualization', "1", {
				packages: ['corechart']
			});
			function showChart() {
				var data = google.visualization.arrayToDataTable([
					['Language', 'News Count'], 
					<?php foreach($news_per_language as $row) {
						echo "['".$row['language']."',".$row['news']."],";
					} ?>
				]);
				var options = {
					title: 'Language wise News',
					isStacked: true
				};
				var chart = new google.visualization.ColumnChart(document.getElementById('LanguageColumnChart'));
				chart.draw(data, options);
			}
			google.charts.setOnLoadCallback(showChart);
		</script>
        <script>
			google.charts.load('current', {'packages':['corechart', 'bar']});
			google.charts.setOnLoadCallback(drawLineChart);
			google.charts.setOnLoadCallback(drawBarChart);
            // Line Chart
			function drawLineChart() {
				var data = google.visualization.arrayToDataTable([
					['Language', 'Surveys Count'],
						<?php 
							foreach ($surveys_per_language as $row){
							   echo "['".$row['language']."',".$row['surveys']."],";
						} ?>
				]);
				var options = {
					title: 'Language wise surveys',
					curveType: 'function',
					legend: {
						position: 'top'
					}
				};
				var chart = new google.visualization.LineChart(document.getElementById('SurveyLineChart'));
				chart.draw(data, options);
			}

            // Bar Chart
			google.charts.setOnLoadCallback(showBarChart);
			function drawBarChart() {
				var data = google.visualization.arrayToDataTable([
					['Language', 'Surveys Count'], 
						<?php 
							foreach ($surveys_per_language as $row){
                                echo "['".$row['language']."',".$row['surveys']."],";
							}
						?>
				]);
				var options = {
					title: 'Language wise surveys',
					is3D: true,
				};
				var chart = new google.visualization.BarChart(document.getElementById('SurveyLineChart'));
				chart.draw(data, options);
			}
            </script>
            <?php base_url() . include 'footer.php'; ?>  
        </div>
        <!-- ./wrapper -->
    </body>
</html>