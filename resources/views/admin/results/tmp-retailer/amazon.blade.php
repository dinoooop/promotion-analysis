<div class="result-table">


    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th colspan="2">Baseline (daily)</th>
                <th colspan="2">Baseline (wkly)</th>
                @if($is_single_day)
                <th colspan="2">During (daily)</th>
                @else
                <th colspan="2">During (wkly)</th>
                @endif
                <th colspan="2">Post (wkly)</th>
                @if($is_single_day)
                <th colspan="2">During Incremental (daily)</th>
                @else
                <th colspan="2">During incremental (wkly)</th>
                @endif
                <th colspan="2">Post incremental (wkly)</th>
                <th colspan="2">Total During Incremental</th>
                <th colspan="2">Total Post Incremental</th>
                <th colspan="2">During lift</th>
                <th colspan="2">Post lift</th>
                <th></th>
            </tr>
            <tr>
                <th>Redshift data</th>
                <th>Preparation table</th>
                <th>Material Id</th>
                <th>ASIN</th>
                <!-- Baseline (daily) -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- Baseline (wkly) -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- During (daily) / During (wkly) -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- Post (wkly) -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- During Incremental (daily) / During Incremental (wkly) -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- Post incremental (wkly) -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- Total During Incremental -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- Total Post Incremental -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- During lift -->
                <th>Ordered Amount</th>
                <th>Ordered Units</th>
                
                <!-- Post lift -->
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
                <td><a href="{{ $record->href_week_preperation_table }}"><i class="fa fa-database"></i></a></td>
                <td>{{ $record->material_id }}</td>
                <td>{{ $record->asin }}</td>

                <td>{{ $record->daily_baseline_ordered_amount }}</td>
                <td>{{ $record->daily_baseline_ordered_units }}</td>
                
                <td>{{ $record->wkly_baseline_ordered_amount }}</td>
                <td>{{ $record->wkly_baseline_ordered_units }}</td>

                @if($is_single_day)
                <td>{{ $record->daily_during_ordered_amount }}</td>
                <td>{{ $record->daily_during_ordered_units }}</td>
                @else
                <td>{{ $record->wkly_during_ordered_amount }}</td>
                <td>{{ $record->wkly_during_ordered_units }}</td>
                @endif

                <td>{{ $record->wkly_post_ordered_amount }}</td>
                <td>{{ $record->wkly_post_ordered_units }}</td>
                
                @if($is_single_day)
                <td>{{ $record->daily_during_incremental_ordered_amount }}</td>
                <td>{{ $record->daily_during_incremental_ordered_units }}</td>
                @else
                <td>{{ $record->wkly_during_incremental_ordered_amount }}</td>
                <td>{{ $record->wkly_during_incremental_ordered_units }}</td>
                @endif

                <td>{{ $record->wkly_post_incremental_ordered_amount }}</td>
                <td>{{ $record->wkly_post_incremental_ordered_amount }}</td>
                
                <td>{{ $record->total_during_incremental_ordered_amount }}</td>
                <td>{{ $record->total_during_incremental_ordered_units }}</td>
                
                <td>{{ $record->total_post_incremental_ordered_amount }}</td>
                <td>{{ $record->total_post_incremental_ordered_units }}</td>

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