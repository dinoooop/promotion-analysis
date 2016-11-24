@extends('admin.layouts.dashboard')


@section('title', 'Items')

@section('main')

<div class="right_col" role="main">



    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h2>Promotion Items</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <p>Promotion name: {{ $promotion->promotions_name }}</p>
                    <p>{{ $promotion->promotions_description }}</p>
                    <p>Start Date: {{ $promotion->promotions_startdate }}</p>
                    <p>End Date: {{ $promotion->promotions_enddate }}</p>
                    <p>Promotion Type: {{ $promotion->promotions_type }}</p>
                </div>
                <div class="col-md-6 col-sm-12">
                    <p>Retailer: {{ $promotion->retailer }}</p>
                    <p>Promotion Status: {{ $promotion->promotions_status }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <a href="{{route('items.create', ['pid' => $promotion->id])}}" class="btn btn-primary">Add Item [+]</a>
                    <?php echo $button_update_promotion_status; ?>
                </div>
            </div>

            @if ($records->count())
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Material Id</th>
                        <th>ASIN</th>

                        <th>Start date</th>
                        <th>End date</th>
                        <th>Product Name</th>
                        <th>Promotions Projected Sales</th>
                        <th>Promotions Budget Type</th>
                        <th>Funding per unit</th>
                        <th>Forecaseted qty</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($records as $record)
                    <?php $record = App\promotions\Item::display_prepare($record) ?>
                    <tr>
                        <td>{{ $record->material_id }}</td>
                        <td>{{ $record->asin }}</td>

                        <td>{{ $record->promotions_startdate }}</td>
                        <td>{{ $record->promotions_enddate }}</td>
                        <td>{{ $record->product_name }}</td>
                        <td>{{ $record->promotions_projected_sales }}</td>
                        <td>{{ $record->promotions_budget_type }}</td>
                        <td>{{ $record->funding_per_unit }}</td>
                        <td>{{ $record->forecaseted_qty }}</td>

                        <td>
                            <a href="{{route('items.edit', array($record->id, 'pid'=>$promotion->id))}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <a class="btn btn-danger row-delete" href="{{route('items.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>

            @else
            <p>There are no items available under this promotion</p>
            @endif
        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->links() }}</div>
        </div>
    </div>



</div>
@stop