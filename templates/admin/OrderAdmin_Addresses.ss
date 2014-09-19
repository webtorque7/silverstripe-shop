<table class="ss-gridfield-table">
	<thead>
		<tr class="title">
			<th colspan="3">
				<h2><% _t("ADDRESS","Address") %></h2>
			</th>
		</tr>
		<tr class="header">
			<th class="main"><% _t("SHIPTYPE","Shipping Type") %></th>
			<th class="main"><% _t("SHIPTO","Ship To") %></th>
			<th class="main"><% _t("BILLTO","Bill To") %></th>
		</tr>
	</thead>
	<tbody>
		<tr class="ss-gridfield-item ">
			<td>$orderPickup.ShippingType.XML</td>
			<td>$ShippingAddress</td>
			<td>$BillingAddress</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom-all" colspan="5"></td>
		</tr>
	</tfoot>
</table>