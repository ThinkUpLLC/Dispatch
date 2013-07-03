<?php
/* include DispatchParent */
require_once substr(dirname(__FILE__), 0, -4) . '/lib/DispatchParent.php';
\thinkup\DispatchParent::init();
$web_path = \thinkup\DispatchParent::config('WEB_PATH');
$root_url = \thinkup\DispatchParent::config('WEB_PATH') . '/monitor.php?auth_token='
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Queue Status/Monitor</title>

  <link rel="stylesheet" href="<?php echo $web_path ?>/css/styles.css" type="text/css">

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
  <script src="<?php echo $web_path ?>/lib/jquery.cookie.js"></script>
  <script src="<?php echo $web_path ?>/lib/underscore-1.4.4.js"></script>
  <script src="<?php echo $web_path ?>/lib/backbone-1.0.js"></script>
  
  <script type="text/javascript">
  // global vars
  root_url = '<?php echo $root_url ?>';
  </script>

</head>
<body>

<!-- ######################## -->
<!-- html shell                   -->
<!-- ######################## -->

<!-- log view -->
<div id="login-form"></div>


<!-- queue status error element -->
<div id="error-container"></div>

<!-- queue status element -->
<div id="queue-status"></div>

<!-- crawl status filter by install name form -->
<div id="crawl-statuses-form"><form id="install-filter">Filter by install name: <input id="install-name" type="text"></form></div>

<!-- crawl statuses element -->
<table class="gridtable" id="crawl-statuses"></table>

<!-- log view -->
<div class="log" id="log"></div>


<!-- ######################## -->
<!-- js                   -->
<!-- ######################## -->

<!-- our queue status views -->
<script src="/js/views/QueueStatusViews.js" type="text/javascript"></script>

<!-- our queue status models  -->
<script src="/js/models/QueueStatusModels.js" type="text/javascript"></script>

<!-- our queue status models  -->
<script src="/js/controller/QueueStatusController.js" type="text/javascript"></script>


<!-- ######################## -->
<!-- templates                -->
<!-- ######################## -->

<!-- queue status error template -->
<script type="text/template" id="request-fail">
    <span class="error">We are unable to process queue status request</span>
</script>

<!-- queue status not running -->
<script type="text/template" id="queue-fail">
    <span class="error">The Dispatch Queue does not appear to be running</span>
</script>

<!-- queue status not enough workers template -->
<script type="text/template" id="worker-fail">
    <span class="error">There are not enough Dispatch Workers running. Currently running '<%= running %>', and should be running '<%= wanted %>'</span>
</script>

<!-- queue status OK template -->
<script type="text/template" id="queue-ok">
    <span id="services-header">Queue Services are Up</span>
    <ul>
    <li>Connected Workers: <%= workers %></li>
    <li>Workers Running: <%= running %></li>
    </ul>
</script>

<!-- crawl status table header template -->
<script type="text/template" id="crawl-status-header">
    <th>Install Name                                 </th>
    <th>ID                                           </th>
    <th>Crawl Status                                 </th>
    <th>Crawl Time In seconds                        </th>
    <th>Start Time                                   </th>
    <th>Finish Time                                  </th>
    <th>Log                                          </th>
</script>

<!-- crawl status template -->
<script type="text/template" id="crawl-status">
    <td><%= install_name %>                          </td>
    <td><%= id %>                                    </td>
    <td><%= crawl_status %>                          </td>
    <td><%= crawl_time %>                            </td>
    <td><%= crawl_start %>                           </td>
    <td><%= crawl_finish %>                          </td>
    <td><a href="#<%= id %>" class="log-link">log</a></td>
</script>

<!-- log template -->
<script type="text/template" id="log-template">
<div id="close-log">X [esc]</div>
<div id="view-log"><%= crawl_log %></div>
</script>

<!-- login template -->
<script type="text/template" id="login-template">
<form id="login">
<div id="login-header">Dispatch Admin Dashboard</div><input type="text" id="token" /><input type="submit" value="login">
<div id="invalid-login">Login Failed</div>
</form>
</script>

</body>
</html>