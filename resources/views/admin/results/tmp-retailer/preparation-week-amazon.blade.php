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
                        width: 100,
                    },
                    {
                        field: "ordered_amount",
                        title: "Ordered amount",
                        type: "number",
                    },
                    {
                        field: "ordered_units",
                        title: "Ordered units",
                    },
                    {
                        field: "quarter_ordered_amount",
                        title: "13 Week ordered amount",
                        type: "number",
                    },
                    {
                        field: "quarter_ordered_units",
                        title: "13 Week ordered units",
                        type: "number",
                    },
                    {
                        field: "normalized_ordered_amount",
                        title: "Normalized ordered amount",
                        type: "number",
                    },
                    {
                        field: "normalized_ordered_units",
                        title: "Normalized ordered units",
                        type: "number",
                    },
                   
                ]
            }).data("kendoGrid");
        });
    </script>
</div>