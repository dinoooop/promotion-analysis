<div class="row">
    <div class="col-md-4 col-sm-12">
        <p>Promotion name: {{ $promotion->promotions_name }}</p>
        <p>{{ $promotion->promotions_description }}</p>
        <p>Start Date: {{ $promotion->promotions_startdate }}</p>
        <p>End Date: {{ $promotion->promotions_enddate }}</p>
        <p>Promotion Type: {{ $promotion->promotions_type }}</p>
        <p>Retailer: {{ $promotion->retailer }}</p>
    </div>
    <div class="col-md-4 col-sm-12">
        <p>Level of promotions: {{ $promotion->level_of_promotions }}</p>
        <p>Newell Status: {{ $promotion->newell_status }}</p>
    </div>
</div>