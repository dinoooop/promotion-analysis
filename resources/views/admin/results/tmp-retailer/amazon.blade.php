<div class="result-table">


    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th colspan="2">Baseline</th>
                <th colspan="2">During</th>
                <th colspan="2">Post</th>
                <th colspan="2">During incremental</th>
                <th colspan="2">Post incremental</th>
                <th colspan="2">During lift</th>
                <th colspan="2">Post lift</th>
                <th></th>
            </tr>
            <tr>
                <th>Preparation table</th>
                <th>Material Id</th>
                <th>ASIN</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                <th>Pro Days</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($records as $record)
            <?php $record = App\promotions\Promotion::display_prepare($record) ?>
            <tr>
                <td><a href="{{ $record->href_preperation_table }}"><i class="fa fa-database"></i></a></td>
                <td>{{ $record->material_id }}</td>
                <td>{{ $record->asin }}</td>

                <td>{{ $record->daily_baseline_ordered_amount }}</td>
                <td>{{ $record->daily_baseline_ordered_units }}</td>

                <td>{{ $record->daily_during_ordered_amount }}</td>
                <td>{{ $record->daily_during_ordered_units }}</td>

                <td>{{ $record->daily_post_ordered_amount }}</td>
                <td>{{ $record->daily_post_ordered_units }}</td>

                <td>{{ $record->during_incremental_ordered_amount }}</td>
                <td>{{ $record->during_incremental_ordered_units }}</td>

                <td>{{ $record->post_incremental_ordered_amount }}</td>
                <td>{{ $record->post_incremental_ordered_units }}</td>

                <td>{{ $record->during_lift_ordered_amount }}</td>
                <td>{{ $record->during_lift_ordered_units }}</td>

                <td>{{ $record->post_lift_ordered_amount }}</td>
                <td>{{ $record->post_lift_ordered_units }}</td>

                <td>{{ $record->no_of_promotion_days }}</td>
            </tr>
            @endforeach

        </tbody>

    </table>
</div>