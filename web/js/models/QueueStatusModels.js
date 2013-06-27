/**
 * QueueStatus model, fetches status info about our Queue
 */
QueueStatus = Backbone.Model.extend({
    urlRoot: '/monitor.php?auth_token=0000000000',
    initialize: function() {
        this.bind("error", function(model, error) {
            error_view = new ErrorView();
            error_view.render();
        })
    }
});
