<div class="dapos-calculator-sm" id="smCalculatorCancelPopup">
  <div class="popup-head">
    <button type="button" class="close" aria-label="Close" onclick="closeSmCalculatorC()">
      <i class="material-icons">close</i>
    </button>
  </div>
  <div class="col-md-12">
    <div class="block">
      <input type="text" class="popup-input-number input-lg" id="calculator-sm-cancel-value" style="width: 300px;" readonly>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(1)">1</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(2)">2</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(3)">3</button>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(4)">4</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(5)">5</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(6)">6</button>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(7)">7</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(8)">8</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(9)">9</button>
    </div>
    <div class="block">
      <label class="fs18 popup-label"></label>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue('del')">DEL</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue(0)">0</button>
      <button type="button" class="btn btn-lg btn-default calculator-button" onclick="setSmCalculatorCValue('.')">.</button>
    </div>
    <div class="block-bottom">
      <div class="text-center">
        <button class="btn btn-lg btn-primary"  style="width:300px" onclick="insertSmCalculatorCValue()">Enter</button>
      </div>
    </div>
  </div>
</div>
