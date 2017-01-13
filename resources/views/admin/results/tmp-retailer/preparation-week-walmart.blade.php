<div class="result-table">

    <p>Material ID: {{$item->material_id}} </p>
    <p>ASIN: {{$item->asin}} </p>
    

    <div id="grid"></div>

    <script>
        $(document).ready(function () {


            var grid = $("#grid").kendoGrid({
                dataSource: {
                    transport: {
                        read: {
                            url: "<?php echo $kendo_url; ?>",
                            dataType: 'json',
                            cache: false
                        },
                    },
                    
                    pageSize: 20,
                },
                height: 650,
                filterable: true,
                sortable: true,
                pageable: true,
                editable: "inline",
                columns: [
                    
                    {
                        field: "week",
                        title: "Week",
                        format: "{0:MM/dd/yyyy}",
                        type: "date",
                        sortable: true,
                        width: 250,
                    },
                    {
                        field: "ordered_amount",
                        title: "pos amount",
                        type: "number",
                    },
                    {
                        field: "ordered_units",
                        title: "pos units",
                    },
                    {
                        field: "quarter_ordered_amount",
                        title: "13 Week pos sales",
                        type: "number",
                    },
                    {
                        field: "quarter_ordered_units",
                        title: "13 Week pos units",
                        type: "number",
                    },
                    {
                        field: "normalized_ordered_amount",
                        title: "Normalized pos amount",
                        type: "number",
                    },
                    {
                        field: "normalized_ordered_units",
                        title: "Normalized pos units",
                        type: "number",
                    },
                ]
            }).data("kendoGrid");
        });
    </script>
</div>