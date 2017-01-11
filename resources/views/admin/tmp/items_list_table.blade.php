@if ($records->count())
<table class="table table-striped table-bordered">
    
    <thead>
        <tr>
            <th>Promotions id</th>
            <th>Material id</th>
            <th>Start date</th>
            <th>End date</th>
            <th>Budget</th>
            <th>Projected sales</th>
            <th>Expected lift</th>
            <th>Funding per unit</th>
            <th>Forecasted qty</th>
            <th>Forecasted unit sales</th>
        </tr>
    </thead>
    

    <tbody>

        @foreach ($records as $record)
        <?php $record = App\promotions\Item::display_prepare($record) ?>
        <tr>
            <td>{{ $record->promotions_id }}</td>
            <td>{{ $record->material_id }}</td>
            <td>{{ $record->promotions_startdate }}</td>
            <td>{{ $record->promotions_enddate }}</td>
            
            <td>{{ $record->promotions_budget }}</td>
            <td>{{ $record->promotions_projected_sales }}</td>
            <td>{{ $record->promotions_expected_lift }}</td>
            <td>{{ $record->funding_per_unit }}</td>
            <td>{{ $record->forecasted_qty }}</td>
            <td>{{ $record->forecasted_unit_sales }}</td>
            
        </tr>
        @endforeach

    </tbody>

</table>

@else
<p>There are no items available</p>
@endif