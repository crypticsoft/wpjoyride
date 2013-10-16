<div id="tourPanel">

<div id="tourTab"><i class="icon-cog icon-light icon-large"></i></div>

<div id="tours">
  <span class="title">All Tours</span>
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

<div id="form-ui" class="hide"><form>
  <div class="steps">
  <!-- tips form -->
  <div id="tips-form">

    <div class="input-wrap">
      <p class="label"><label for="parent_id">
      Parent ID / Selector
      </label></p>
      <input type="text" id="parent_id" name="parent_id" class="text" />
      <button id="selector_gadget">Selector Gadget</button>
    </div>

    <div class="input-wrap">
      <p class="label"><label for="tip_title">
      Tip Title
      </label></p>
      <input type="text" id="tip_title" name="tip_title" class="text" />
    </div>

    <div class="input-wrap">
      <p class="label"><label for="tip_text">
      Tip Text
      </label></p>
      <input type="text" id="tip_text" name="tip_text" class="text" />
    </div>

    <div class="input-wrap">
      <p class="label"><label for="tip_location">
      Tip Location
      </label></p>
      <ul class="checkbox-list">
        <li><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="top" /> Top</li>
        <li><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="bottom" /> Bottom</li>
        <li><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="left" /> Left</li>
        <li><input type="checkbox" id="tip_location" name="tip_location" class="checkbox" value="right" /> Right</li>     
      </ul>
    </div>

    <div class="input-wrap">
      <p class="label"><label for="tip_animation">
      Tip Animation
      </label></p>
      <ul class="checkbox-list">
        <li><input type="checkbox" id="tip_animation" name="tip_animation" class="checkbox" value="pop" /> Pop</li>
        <li><input type="checkbox" id="tip_animation" name="tip_animation" class="checkbox" value="fade" /> Fade</li>    
      </ul>
    </div>

    <div class="input-wrap">
      <p class="label"><label for="button_text">
      Button Text
      </label></p>
      <input type="text" id="button_text" name="button_text" class="text" />
    </div>
    </div><!--//#tips-form -->
  </div><!--//#steps -->


  <!--p><input type="button" id="preview_tip" value="Preview" class="button button-primary button-small"></p-->    
  <p><input type="submit" value="Save" class="button button-primary button-small"></p>    
</form>
</div><!--//#form-ui -->


</div>

</div>

<style type="text/css">
#tourPanel {
  position: absolute;
  right: -270px;
  top: 25px;
  width:250px;
  height: auto;
  background: #333;
  color: #fff;
  padding: 10px;
  border-radius: 0 0 0 4px;  
}
#tours {
  margin-bottom: 10px;
}
#tourTab {
  width: 25px;
  height: 25px;
  border-radius: 4px 0 0 4px;
  background: #333;
  text-align: center;
  padding: 8px 5px 0;
  position: absolute;
  top: 0;
  left: -35px;
  cursor: pointer;
}
#tourInputs p {
  margin: 1em 0 0.5em;
}
#tourInputs span.title:last-child {
  border-top: 1px dashed #999;
  border-bottom: 1px dashed #999;
  display: block;
  padding: 5px 0;
}
#tourInputs label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    color: #ccc;
    margin: 5px 0;
}
#tourInputs button {
  margin-top: 10px;
}

#form-ui label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    color: #ccc;
}

#tourInputs input, #tipInputs input[type=text] {
  width: 100%;
}
#tourInputs button {
  display: block;
}
.hide { display: none; }
#tipInputs div.input-wrap {
  margin-bottom: 10px;
}
#tipInputs div.input-wrap label {
  text-transform: normal;
  display: inline-block;
}

.highlighter {
-moz-box-shadow: inset 0 0 1em gold;
-webkit-box-shadow: inset 0 0 1em gold;
box-shadow: inset 0 0 1em gold; 
}
</style>