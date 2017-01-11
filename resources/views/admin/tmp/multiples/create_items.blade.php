<div class="row">
    <div class="col-sm-12">
        
        <h2>Step 2: Add promoted items</h2>
        @if(!isset($items) || $items->count() == 0)
        {{ Form::open(array('route' => 'multiples.store', 'id' => 'pv_create_multiple', 'class'=>'normal_form', 'enctype' => 'multipart/form-data')) }}
        <?php echo $form_multiple_items; ?>
        {{ Form::close() }}
        @endif

    </div>
</div>
@if(isset($items) && $items->count() != 0)
<div class="row">
    <div class="col-sm-12">

        <div class="promotion-list-table">
            @include('admin/tmp/items_list_table', ['records' => $items])
        </div>

    </div>
</div>
@endif