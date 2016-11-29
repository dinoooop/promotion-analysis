@extends('admin.layouts.dashboard')


@section('title', 'Promotions')

@section('main')

<div class="right_col" role="main">
    <p>{{ link_to_route('promotions.create', 'Create new promotion [+]')}}</p>

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
                
                <th width="150">Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($records as $record)
            <?php $record = App\promotions\Promotion::display_prepare($record)?>
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ link_to_route('items.index', $record->promotions_name, array('pid' => $record->id)) }}</td>
                <td>{{ $record->promotions_startdate }}</td>
                <td>{{ $record->promotions_enddate }}</td>
                <td>{{ $record->brand }}</td>
                <td>{{ $record->promotions_budget }}</td>
                <td>{{ $record->promotions_projected_sales }}</td>
                <td>{{ $record->promotions_expected_lift }}</td>
                
                
                
                <td>
                    <a href="{{route('promotions.edit', array($record->id))}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a class="btn btn-danger row-delete" href="{{route('promotions.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a>
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
@stop