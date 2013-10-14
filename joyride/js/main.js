(function($){

  var Tour = { Views:{} },
      wpt  = window.wpTour;

  /**
   * Model
   */
  Tour.Model = Backbone.Model.extend({
    defaults : {
      'tour_url' : null,
      'tour_hashtag' : location.pathname,
      'tour_id' : null,
      'tour_title' : null
    },
    url : ajaxurl+'?action=save_tour',
    toJSON : function(){
      var attrs = _.clone(this.attributes);
      //attrs.post_id = wpt.post_id;
      return attrs;
    },
    initialize : function(){
      alert("Oh hey!"); 
/*      if(this.get('tour_id') === wpt.tips.correct){
        this.set('correct', true)
      }
*/      
    }
  });

  /**
   * Collection
   */
  Tour.Collection = Backbone.Collection.extend({
    model : Tour.Model
  });

/* Tour Inputs */

  Tour.Views.TourInputs = Backbone.View.extend({
    //tagName : 'p',
    //  id : 'tourInputs',
    // Get the template from the DOM
    template :_.template($(wpt.tourTempl).html(), { 
      tour_url: location.pathname, 
      tour_hashtag: null,
      tour_title: null,
      tour_id: null
    }),

    // When a model is saved, return the button to the disabled state
    initialize : function () {

    _.bindAll(this, "render");
    //this.model.bind("change", this.render);
    this.render();
    },

    // Attach events
    events : {
      'click button' : 'save'
    },

    // Perform the Save
    save : function(e){
      e.preventDefault();
      $(e.target).text('wait');
      var item = {
        tour_url: $("#tour_url").val(),
        tour_hashtag: $("#tour_hashtag").val(),
        tour_title: $("#tour_title").val(),
        tour_id: $("#tour_id").val() ? $("#tour_id").val() : null
      }
      this.model.save(item, {
    success: function (model, response) {
      //app.navigate('items/'+model.id, {trigger: true, replace: true})
      console.log('success');
      console.log(model);
    },
    error: function (model, response) {
      //new Bootstrap.alert({ el: $('#page') }).render(response, 'error')
      console.log(response);
    }
    });

    },
    // Render the single input - include an index.
    render : function () {
      this.$el.html(this.template);
      //this.$el.html(this.template(this.model.toJSON()));
      return this;
    }
  });


  /**
   * init
   */
  var tips  = new Tour.Collection(wpt.tips),
//    selectElem = new Tour.Views.Select({collection:tips, el :wpt.tipSelect}),
//    inputs     = new Tour.Views.Inputs({collection:tips, el:wpt.tipInput}),
    tourInputs = new Tour.Views.TourInputs({model:tips, el:wpt.tourInput});

}(jQuery));
