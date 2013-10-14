<div id="tourPanel">

<div id="tours">
  <span class="title">All Tours</span><br>
</div>
<div id="tourInputs">

  <div class="form">
    <span class="title">Create New Tour</span>
    <label>Tour Title</label>
    <input type="text" id="tour_title" />
    <label for="tour_url">URL</label>
    <input type="text" id="tour_url" />
    <label for="tour_hashtag">Hashtag</label>
    <input type="text" id="tour_hashtag" />
    <input type="hidden" id="tour_id" />
    <button class="button button-small button-primary">Add Tour</button>
  </div>

</div>

<div id="tipCollection"></div>

<div id="tipInputs" class="hide">
  <p>Enter the Tips below</p>
</div>

<!--p>
  <input name="save" type="submit" class="button button-primary button-small" value="Save all">
</p-->
</div>
<!--script>
  window.wpTour = {};
  var wpt = window.wpTour;
  wpt.tips = <?= $tips ?>;
  <?php /* wpt.tips.correct = <?= $correct ?>; */ ?>
  wpt.tipSelect = '#tipSelect';
  wpt.tipInput = '#tipInputs';
  wpt.tourInput = '#tourInputs';
  wpt.inputTempl = '#inputTemplate';
  wpt.tourTempl = '#tourTemplate';
  <?php /* wpt.post_id = <?= $post->ID ?>; */ ?>
</script-->
<style type="text/css">
#tourPanel {
  position: absolute;
  right: 0;
  top: 25px;
  width:250px;
  height: auto;
  background: #333;
  color: #fff;
  padding: 10px;
}
#tourInputs label, #form-ui label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    color: #ccc;
}
#tourInputs input {
  margin-bottom: 10px;
  width: 100%;
}
#tourInputs button {
  display: block;
}
#tipInputs {}

.hide { display: none; }

.highlighter {
-moz-box-shadow: inset 0 0 1em gold;
-webkit-box-shadow: inset 0 0 1em gold;
box-shadow: inset 0 0 1em gold; 
}
</style>
