@extends('admin.layouts.dashboard')


@section('title', 'Configurations')

@section('main')

<div class="right_col" role="main">
    <p>{{ link_to_route('configurations.create', 'Create new configuration [+]')}}</p>

    @if ($records->count())
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Promotions Type</th>
                <th>Level of promotions</th>
                <th>Retailer</th>
                <th>Brand</th>
                <th>Division</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Baseline weeks</th>
                <th>Post weeks</th>
                <th>Baseline threshold</th>
                <th width="150">Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($records as $record)
            <?php $record = App\promotions\Configuration::display_prepare($record)?>
            <tr>
                <td>{{ $record->promotions_type }}</td>
                <td>{{ $record->level_of_promotions }}</td>
                <td>{{ $record->retailer }}</td>
                <td>{{ $record->brand }}</td>
                <td>{{ $record->division }}</td>
                <td>{{ $record->category }}</td>
                <td>{{ $record->sub_category }}</td>
                <td>{{ $record->baseline_weeks }}</td>
                <td>{{ $record->post_weeks }}</td>
                <td>{{ $record->baseline_threshold }}</td>
                                
                
                <td>
                    <a href="{{route('configurations.edit', array($record->id))}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a class="btn btn-danger row-delete" href="{{route('configurations.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>

    @else
    <p>There are no configurations available</p>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->links() }}</div>
        </div>
    </div>
    
    

</div>
@stop