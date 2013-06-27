
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
