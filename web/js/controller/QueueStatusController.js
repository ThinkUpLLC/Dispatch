/**
 * Loads our Queue & Crawl status models and proper views
 */

// out queue status model instance
var queue_status = new QueueStatus();

// our fetch crawl status object with success response
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
            $('#error-container').show();
        } else {
            // render queue status
            workers = queue_status.get('gearman_status').operations.crawl.connectedWorkers;
            running = queue_status.get('gearman_status').operations.crawl.running;
            queue_ok_view = new QueueOKView();
            queue_ok_view.render(workers, running);
            $('#queue-status').show();
        }
        // render crawl statuses
        $('#crawl-statuses-form').show();
        crawl_states_view = new CrawlStatusesView();
        crawl_states_view.render(queue_status.get('crawl_data'));
    }
};

// our fetch log object with success response
var crawl_log_fetch_object = {
    success: function (log) {
        crawl_log_view = new CrawlLogView();
        crawl_log_view.render(log);
        newtop = $(document).scrollTop() + 20;
        $('#log').css('top',newtop + 'px');

    }
};

// our login object with success response
var login_fetch_object = {
    success: function (login) {
        $('#login-form').hide();
        auth_token = login.get('auth_token');
        //console.log(auth_token);
        root_url += auth_token;
        $.cookie('auth_token', auth_token, { expires: 30 });
        //console.log(root_url);
        queue_status.urlRoot = root_url;
        queue_status.fetch( queue_status_fetch_object );
    }
}

$(document).ready(function() {

    auth_token = $.cookie("auth_token");
    // fetch our initailqueue and cralw statuses
    if(! auth_token || auth_token == '') {
        login_view = new LoginView();
        login_view.render();
        $('#login-form').show();
        $('#login-form').submit( function(ev) {
            login_token = $('#token').val();
            if(login_token && login_token != '') {
                login_model = new LoginModel();
                login_model.urlRoot = root_url + '&login=' + login_token;
                login_model.fetch(login_fetch_object);
            }
            return false;
        });
        
    } else {
        root_url += auth_token;
        queue_status.urlRoot = root_url;
        queue_status.fetch( queue_status_fetch_object );
    }

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

