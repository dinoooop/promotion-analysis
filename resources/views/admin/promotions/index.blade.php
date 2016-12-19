@extends('admin.layouts.dashboard')


@section('title', 'Promotions')

@section('main')

<div class="right_col" role="main">

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                
                <div class="x_title">
                    <h3>{{$page_heading}}</h3>
                    <div class="clearfix"></div>
                </div>
                

                @if ($records->count())
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Promotion name</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Brand</th>
                            <th>Budget</th>
                            <th>Projected sales</th>
                            <th>expected lift</th>
                            <th>Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($records as $record)
                        <?php $record = App\promotions\Promotion::display_prepare($record) ?>
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td><a href="{{route('promotions.edit', array($record->id))}}">{{$record->promotions_name}}</a></td>
                            <td>{{ $record->promotions_startdate }}</td>
                            <td>{{ $record->promotions_enddate }}</td>
                            <td>{{ $record->brand }}</td>
                            <td>{{ $record->promotions_budget }}</td>
                            <td>{{ $record->promotions_projected_sales }}</td>
                            <td>{{ $record->promotions_expected_lift }}</td>
                            <td>{{ $record->status }}</td>

                            <td>
                                <a href="{{route('items.index', ['pid' => $record->id, 'hsv' => 1])}}" class="btn btn-info"><i class="fa fa-database" aria-hidden="true"></i></a>
                                <a class="btn btn-danger row-delete" href="{{route('promotions.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a>
                                @if($result_view_button)
                                <?php echo $record->button_result ?>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                    </tbody>

                </table>

                @else
                <p>There are no promotions available</p>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">{{ $records->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
@stop