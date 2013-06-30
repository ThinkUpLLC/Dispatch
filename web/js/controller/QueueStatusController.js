/**
 * Loads our Queue & Crawl status models and proper views
 */

// out queue status model instance
var queue_status = new QueueStatus();

// our fetch object with success response
var queue_status_fetch_object = {
    success: function (queue_status) { 
        if(queue_status.get('gearman_ok') == false) {
            if(! queue_status.get('gearman_status')) {
                queue_error_view = new QueueErrorView();
                queue_error_view.render();
            } else {
                worker_error_view = new WorkerErrorView();
                if(! queue_status.get('gearman_status').operations) {
                    worker_error_view.render(0, queue_status.get('workers_wanted'));
                } else {
                    workers = 0;
                    if(queue_status.get('gearman_status').operations.crawl) {
                       workers = queue_status.get('gearman_status').operations.crawl.connectedWorkers;
                    }
                    worker_error_view.render(workers, queue_status.get('workers_wanted'));
                }
            }
        } else {
            workers = queue_status.get('gearman_status').operations.crawl.connectedWorkers;
            running = queue_status.get('gearman_status').operations.crawl.running;
            // render queue status
            queue_ok_view = new QueueOKView();
            queue_ok_view.render(workers, running);
            // render crawl statuses
            console.log(queue_status.get('crawl_data'));
            crawl_states_view = new CrawlStatusesView();
            crawl_states_view.render(queue_status.get('crawl_data'));
        }
    }
};

$(document).ready(function() {

    // fetch our initailqueue and cralw statuses
    queue_status.fetch( queue_status_fetch_object );

    // event on form for filtering cralw statuses by install name
    $('#install-filter').submit( function(ev) {
        name = $('#install-name').val();
        if(name && name != '') {
            queue_status.urlRoot = root_url + '&install_name=' + name;
            queue_status.fetch( queue_status_fetch_object );
        }
        return false;
    });

});

