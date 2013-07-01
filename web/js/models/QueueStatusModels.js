/**
 * QueueStatus model, fetches status info about our Queue
 */
QueueStatus = Backbone.Model.extend({
    urlRoot: root_url,
    initialize: function() {
        this.bind("error", function(model, error) {
            error_view = new ErrorView();
            error_view.render();
        })
    }
});

/**
 * QueueStatus model, fetches status info about our Queue
 */
CrawlLogModel = Backbone.Model.extend({
    urlRoot: root_url,
    initialize: function() {
        this.bind("error", function(model, error) {
            error_view = new ErrorView();
            error_view.render();
        })
    }
});