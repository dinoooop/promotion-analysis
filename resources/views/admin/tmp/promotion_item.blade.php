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