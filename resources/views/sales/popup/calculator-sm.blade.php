<div class="dapos-calculator-sm" id="smCalculatorPopup">
  <div class="popup-head">
    <button type="button" class="close" aria-label="Close" onclick="closeSmCalculator()">
      <i class="material-icons">close</i>
    </button>
  </div>
  <div class="block">
    <input type="text" class="popup-input-number input-lg" id="calculator-sm-value" style="width: 300px;" readonly>
  </div>
  <div class="block">
    <label class="fs18 popup-label"></label>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(1)">1</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(2)">2</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(3)">3</button>
  </div>
  <div class="block">
    <label class="fs18 popup-label"></label>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(4)">4</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(5)">5</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(6)">6</button>
  </div>
  <div class="block">
    <label class="fs18 popup-label"></label>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(7)">7</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(8)">8</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(9)">9</button>
  </div>
  <div class="block">
    <label class="fs18 popup-label"></label>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue('del')">DEL</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue(0)">0</button>
    <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorValue('clear')">Clear</button>
  </div>
  <div class="block-bottom">
    <div class="text-center">
      <button class="btn btn-lg btn-primary"  style="width:300px" onclick="insertSmCalculatorValue()">Enter</button>
    </div>
  </div>
</div>
