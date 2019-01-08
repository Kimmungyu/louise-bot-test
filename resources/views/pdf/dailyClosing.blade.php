<!DOCTYPE html>
<html lang="en">
	<head>
    <style>
      .table-ac {text-align:center;}
      .sum-fr {float:right}
			.dotted-line {border-color:#b2b9c4;border-top: dotted 1px}
      .mt10 {margin-top:10px;}
      .mt15 {margin-top:15px;}
			.mt20 {margin-top:20px;}
      .mt30 {margin-top:30px;}
      .ml100 {margin-left:100px;}
			.page {
				page-break-after: always;
				page-break-inside: avoid;
			}
    </style>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<title>{{ $date }} Daily Closing</title>
	</head>
	<body style="background-color: #f9f9fb; padding:15px; width:700px;">
		<div class="page">
			<div style="width:500px; margin:auto;">
				<h3 align="center">CREWSHOP AIRPORT GmbH</h3>
				<h4 align="center">Am Luftbrueckendenkmal 1, 60549 Frankfurt am Main</h4>
      	<h4 class="mt15" align="center">TAG ABSCHULUSS</h4>
			</div>

			<div style="margin-bottom:10px;">
				<span style="font-size:12pt; float:left;"><strong>Transaktionen : {{ empty($data['dailyNum']->DOC_NUM) ? '-' : $data['dailyNum']->DOC_NUM }}</strong></span>
				<span style="font-size:12pt; float:right;">Issued Date : {{ $date }}</span>
			</div>
			<div class="mt15"></div>
			<div style="clear:both"></div>

			<div style="margin:10px;">
      	<hr style="border-color:black"/>
				<div style="font-size:20px;">
					<strong>Gesamtumsatz </strong>
					<span class="sum-fr"><strong>{{ number_format($data['paymentTotal'][0]->total, 2, ',', '.') }}</strong></span>
				</div>
      	<div class="mt15"></div>
      	<div style="font-size:14px;clear:both">Kunden<span class="sum-fr">{{ $data['paymentTotal'][0]->customers }}</span></div>
      	<div style="font-size:14px;clear:both">KV / Lade offen<span class="sum-fr">{{ $data['cashierOpened'][0]->openedCnt }}</span></div>
      	<div style="font-size:14px;clear:both">Umsatz pro Kunden<span class="sum-fr">{{ number_format(round($data['paymentTotal'][0]->total / $data['paymentTotal'][0]->customers, 2), 2, ',', '.') }}</span></div>

      	<div class="mt15">
        	<div style="font-size:14px;">Steuern</div>
        	<hr class="dotted-line" style="margin:5px 0px;"/>
					@php
						$bruttoSum = 0;
					@endphp
        	@foreach($data['taxTotal'] as $taxTotal)
          	<div style="font-size:14px;clear:both">Umsatz {{ $taxTotal->tax }} % exkl. <span class="sum-fr">{{ number_format($taxTotal->bruttoSum - $taxTotal->taxSum , 2, ',', '.') }}</span></div>
          	<div style="font-size:14px;clear:both">MwSt {{ $taxTotal->tax }} %         <span class="sum-fr">{{ number_format($taxTotal->taxSum, 2, ',', '.') }}</span></div>
          	<div style="font-size:14px;clear:both">Umsatz {{ $taxTotal->tax }} % inkl. <span class="sum-fr">{{ number_format($taxTotal->bruttoSum, 2, ',', '.') }}</span></div>
						<div class="mt10"></div>
						@php
							$bruttoSum += $taxTotal->bruttoSum;
						@endphp
        	@endforeach
					@if($data['paymentTotal'][0]->delivery != 0)
						<div style="font-size:14px;clear:both">Umsatz 0 % <span class="sum-fr">{{ number_format($data['paymentTotal'][0]->delivery, 2, ',', '.') }}</span></div>
					@endif
					<div class="mt10"></div>
        	<div style="font-size:14px;clear:both">
						<strong>Summe({{$data['paymentTotal'][0]->delivery != 0 ? '0/' : ''}}7/19%)</strong>
						<span class="sum-fr"><strong>{{ number_format($bruttoSum + $data['paymentTotal'][0]->delivery, 2, ',', '.') }}</strong></span>
					</div>
      	</div>
				@if(count($data['cancelledTaxTotal']) > 0)
      		<div class="mt15">
        		<div style="font-size:14px;clear:both">Storno</div>
        		<hr class="dotted-line" style="margin:5px 0px;"/>
						@php
							$bruttoSum = 0;
						@endphp
	        	@foreach($data['cancelledTaxTotal'] as $cTaxTotal)
	          	<div style="font-size:14px;clear:both">Umsatz {{ $cTaxTotal->tax }} % exkl. <span class="sum-fr">- {{ number_format($cTaxTotal->bruttoSum - $cTaxTotal->taxSum, 2, ',', '.') }}</span></div>
	          	<div style="font-size:14px;clear:both">MwSt {{ $cTaxTotal->tax }} %         <span class="sum-fr">- {{ number_format($cTaxTotal->taxSum, 2, ',', '.') }}</span></div>
	          	<div style="font-size:14px;clear:both">Umsatz {{ $cTaxTotal->tax }} % inkl. <span class="sum-fr">- {{ number_format($cTaxTotal->bruttoSum, 2, ',', '.') }}</span></div>
							<div class="mt10"></div>
							@php
								$bruttoSum += $cTaxTotal->bruttoSum;
							@endphp
	        	@endforeach
						@if($data['paymentTotal'][0]->cancelledDelivery != 0)
							<div style="font-size:14px;clear:both">Umsatz 0 % <span class="sum-fr">- {{ number_format($data['paymentTotal'][0]->cancelledDelivery, 2, ',', '.') }}</span></div>
						@endif
						<div class="mt10"></div>
	        	<div style="font-size:14px;clear:both">
							<strong>Summe({{$data['paymentTotal'][0]->cancelledDelivery != 0 ? '0/' : ''}}7/19%)</strong>
							<span class="sum-fr"><strong>- {{ number_format($bruttoSum + $data['paymentTotal'][0]->cancelledDelivery, 2, ',', '.') }}</strong></span>
						</div>
      		</div>
				@endif
      	<div class="mt15">
        	<div style="font-size:14px;clear:both">Finanzwege</div>
        	<hr class="dotted-line" style="margin:5px 0px;"/>
			  	<div style="font-size:14px;clear:both">Credit Card Summe<span class="sum-fr">{{ number_format($data['paymentTotal'][0]->creditTotal, 2, ',', '.') }}</span></div>
			  	<div style="font-size:14px;clear:both">EC Card Summe<span class="sum-fr">{{ number_format($data['paymentTotal'][0]->ecTotal, 2, ',', '.') }}</span></div>
			  	<div style="font-size:14px;clear:both">Cash Summe<span class="sum-fr">{{ number_format($data['paymentTotal'][0]->cashTotal, 2, ',', '.') }}</span></div>
      	</div>
			</div>
		</div>
		<div class="{{ empty($data['cancelledProducts']) ? '' : 'page'}}">
      <h4 align="center">Artikel</h4>
			<table class="table table-bordered table-condensed">
			  <thead>
			    <tr>
						<th class="success table-ac">No</th>
				    <th class="success table-ac">Product Code</th>
						<th class="success table-ac" width="50px">Tax</th>
				    <th class="success">Product Name</th>
				    <th class="success table-ac" width="50px">Discount</th>
				    <th class="success table-ac">Qty</th>
				    <th class="success table-ac">Total</th>
			    </tr>
			  </thead>
        <tbody>
        @php
					$i = 1;
					$productCnt = 0;
					$productSum = 0;
				@endphp
			  @foreach($data['productTotal'] as $productTotal)
          @foreach($productTotal as $idx => $pt)
            <tr>
              @if($idx == 0)
                <td rowspan="{{ count($productTotal) }}" class="table-ac" style="vertical-align:middle">{{ $i }}</td>
                <td rowspan="{{ count($productTotal) }}" class="table-ac" style="vertical-align:middle">{{ $productTotal[0]->품목코드 }}</td>
								<td rowspan="{{ count($productTotal) }}" class="table-ac" style="vertical-align:middle">{{ $productTotal[0]->세율 }} %</td>
                <td rowspan="{{ count($productTotal) }}">{{ $productTotal[0]->품목명 }}</td>
              @endif
              <td class="table-ac" style="vertical-align:middle">{{ $pt->discounted }} %</td>
              <td class="table-ac" style="vertical-align:middle">{{ $pt->qty }}</td>
              <td style="vertical-align:middle;text-align:right">{{ number_format($pt->discountTotal, 2, ',', '.') }}</td>
			      </tr>
						@php
							$productCnt += $pt->qty;
							$productSum += $pt->discountTotal;
						@endphp
          @endforeach
          @php ($i++)
					@endphp
			  @endforeach
					@if($data['paymentTotal'][0]->delivery > 0)
						<tr>
							<td class="table-ac" style="vertical-align:middle">{{ $i }}</td>
							<td class="table-ac" style="vertical-align:middle">{{ 'ADD_DLV_CHG' }}</td>
							<td class="table-ac" style="vertical-align:middle">-</td>
							<td style="vertical-align:middle">Delivery Cost</td>
							<td class="table-ac" style="vertical-align:middle">-</td>
							<td class="table-ac" style="vertical-align:middle">-</td>
							<td style="vertical-align:middle;text-align:right">{{ number_format($data['paymentTotal'][0]->delivery , 2, ',', '.') }}</td>
						</tr>
					@endif
			  </tbody>
				<tfoot>
						<tr>
							<td colspan="5">Summe</td>
							<td class="table-ac">{{ $productCnt }}</td>
							<td style="text-align:right">{{ number_format($productSum + $data['paymentTotal'][0]->delivery, 2, ',', '.') }}</td>
						</tr>
				</tfoot>
			</table>
		</div>
		@if(!empty($data['cancelledProducts']))
		<div>
      <h4 align="center">Warenrücknahme</h4>
			<table class="table table-bordered table-condensed">
			  <thead>
			    <tr>
				    <th class="success table-ac">No</th>
				    <th class="success table-ac">Product Code</th>
						<th class="success table-ac" width="50px">Tax</th>
				    <th class="success">Product Name</th>
				    <th class="success table-ac" width="50px">Discount</th>
				    <th class="success table-ac">Qty</th>
				    <th class="success table-ac">Total</th>
			    </tr>
			  </thead>
        <tbody>
        @php
					$i = 1;
					$productCnt = 0;
					$productSum = 0;
				@endphp
			  @foreach($data['cancelledProducts'] as $cancelledProducts)
          @foreach($cancelledProducts as $idx => $pt)
            <tr>
              @if($idx == 0)
                <td rowspan="{{ count($cancelledProducts) }}" class="table-ac" style="vertical-align:middle">{{ $i }}</td>
                <td rowspan="{{ count($cancelledProducts) }}" class="table-ac" style="vertical-align:middle">{{ $cancelledProducts[0]->품목코드 }}</td>
								<td rowspan="{{ count($cancelledProducts) }}" class="table-ac" style="vertical-align:middle">{{ $cancelledProducts[0]->세율 }} %</td>
                <td rowspan="{{ count($cancelledProducts) }}">{{ $cancelledProducts[0]->품목명 }}</td>
              @endif
              <td class="table-ac" style="vertical-align:middle">{{ $pt->discounted }} %</td>
              <td class="table-ac" style="vertical-align:middle">- {{ $pt->qty }}</td>
              <td style="vertical-align:middle;text-align:right">- {{ number_format($pt->discountTotal, 2, ',', '.') }}</td>
			      </tr>
						@php
							$productCnt += $pt->qty;
							$productSum += $pt->discountTotal
						@endphp
          @endforeach
          @php ($i++)
			  @endforeach
				@if($data['paymentTotal'][0]->cancelledDelivery > 0)
					<tr>
						<td class="table-ac" style="vertical-align:middle">{{ $i }}</td>
						<td class="table-ac" style="vertical-align:middle">{{ 'ADD_DLV_CHG' }}</td>
						<td class="table-ac" style="vertical-align:middle">-</td>
						<td style="vertical-align:middle">Delivery Cost</td>
						<td class="table-ac" style="vertical-align:middle">-</td>
						<td class="table-ac" style="vertical-align:middle">-</td>
						<td style="vertical-align:middle;text-align:right">{{ number_format($data['paymentTotal'][0]->cancelledDelivery, 2, ',', '.') }}</td>
					</tr>
				@endif
			  </tbody>
				<tfoot>
						<tr>
							<td colspan="5">Summe</td>
							<td class="table-ac">{{ $productCnt }}</td>
							<td style="text-align:right">- {{ number_format($productSum + $data['paymentTotal'][0]->cancelledDelivery, 2, ',', '.') }}</td>
						</tr>
				</tfoot>
			</table>
		</div>
		@endif
  </body>
</html>
