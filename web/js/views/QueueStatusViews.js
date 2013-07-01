
/**
 * View for general queue status errors
 */
var ErrorView = Backbone.View.extend( {
    el: $('#error-container'),
    initialize: function() {
        this.template = _.template($('#request-fail').html(), {});
    },
    render: function() {
        this.$el.html(this.template);
        return this;
    }
});

/**
 * View for dispatch queue errors (probably queue not running)
 */
var QueueErrorView = Backbone.View.extend( {
    el: $('#error-container'),
    initialize: function() {
        this.template = _.template($('#queue-fail').html(), {});
    },
    render: function() {
        this.$el.html(this.template);
        return this;
    }
});

/**
 * View for dispatch queue worker errors (probably proper amount of workers not running)
 */
var WorkerErrorView = Backbone.View.extend( {
    el: $('#error-container'),
    render: function(running, wanted) {
        template = _.template($('#worker-fail').html(), {running: running, wanted: wanted});
        this.$el.html(template);
        return this;
    }
});

/**
 * View for queue ok status
 */
var QueueOKView = Backbone.View.extend( {
    el: $('#queue-status'),
    render: function(workers, running) {
        //console.log(workers);
        template = _.template($('#queue-ok').html(), {running: running, workers: workers});
        this.$el.html(template);
        return this;
    }
});

/**
 * View for crawl status Header
 */
var CrawlStatusHeader = Backbone.View.extend( {
    tagName: 'tr',
    render: function() {
        //console.log(workers);
        template = _.template($('#crawl-status-header').html(), {});
        this.$el.html(template);
        return this;
    }
});

/**
 * View for crawl status
 */
var CrawlStatus = Backbone.View.extend( {
    tagName: 'tr',
    render: function(crawl_status) {
        template = _.template($('#crawl-status').html(), crawl_status);
        this.$el.html(template);
        return this;
    }
});

/**
 * View for crawl status lists
 */
var CrawlStatusesView = Backbone.View.extend( {
    el: $('#crawl-statuses'),
    render: function(craw_statuses) {
        var self = this;
        self.$el.html('');
        self.$el.append((new CrawlStatusHeader()).render().$el);
        _.each(craw_statuses, function(crawl_status, i) {
             self.$el.append((new CrawlStatus()).render(crawl_status).$el);
         });

         // event for log click
         $('.log-link').click( function(ev) {
             $(this).attr('href');
             crawl_log_view = new CrawlLogView();
             crawl_log_view.render();             
             $('#log').css('top',$(document).scrollTop() + 'px');
             return false;
         });

         // close log window with escape
         $(document).keyup(function(e) {
             if (e.keyCode == 27) {
                 $('#log').hide();
             }
         });

        return this;
    }
});


/**
 * View for crawl log
 */
var CrawlLogView = Backbone.View.extend( {
    el: $('#log'),
    render: function(log) {
        var self = this;
        self.$el.html('');
        template = _.template($('#log-template').html(), {});
        this.$el.html(template);
        $('#log').show();
        return this;
    }
});