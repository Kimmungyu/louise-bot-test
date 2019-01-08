<div class="dapos-calculator-sm" style="left:25%" id="indexCalculatorPopup">
  <div class="popup-head">
    <button type="button" class="close" aria-label="Close" onclick="closeIndexCalculator()">
      <i class="material-icons">close</i>
    </button>
  </div>
  <div class="col-md-12">
    <div class="">
      <input type="text" class="popup-input-number input-lg" id="calculator-index-value" readonly>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(1)">1</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(2)">2</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(3)">3</button>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(4)">4</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(5)">5</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(6)">6</button>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(7)">7</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(8)">8</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(9)">9</button>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue('del')">Del</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue(0)">0</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setIndexCalculatorValue('clear')">Clear</button>
    </div>
    <div class="block-bottom">
      <button class="btn btn-lg btn-primary btn-enter" id="insert-index-val">Enter</button>
    </div>
  </div>
</div>
