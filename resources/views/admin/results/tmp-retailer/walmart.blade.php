<div>
    
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
                
                <th></th>
            </tr>
            <tr>
                <th>Preparation table</th>
                <th>Material Id</th>
                <th>ASIN</th>
                <th>POS Sales</th>
                <th>POS Units</th>
                <th>POS Sales</th>
                <th>POS Units</th>
                <th>POS Sales</th>
                <th>POS Units</th>
                <th>POS Sales</th>
                <th>POS Units</th>
                <th>POS Sales</th>
                <th>POS Units</th>
                
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

                <td>{{ $record->daily_baseline_pos_sales }}</td>
                <td>{{ $record->daily_baseline_pos_units }}</td>

                <td>{{ $record->daily_during_pos_sales }}</td>
                <td>{{ $record->daily_during_pos_units }}</td>

                <td>{{ $record->daily_post_pos_sales }}</td>
                <td>{{ $record->daily_post_pos_units }}</td>

                <td>{{ $record->during_incremental_pos_sales }}</td>
                <td>{{ $record->during_incremental_pos_units }}</td>

                <td>{{ $record->post_incremental_pos_sales }}</td>
                <td>{{ $record->post_incremental_pos_units }}</td>


                <td>{{ $record->no_of_promotion_days }}</td>
            </tr>
            @endforeach

        </tbody>

    </table>
    
</div>