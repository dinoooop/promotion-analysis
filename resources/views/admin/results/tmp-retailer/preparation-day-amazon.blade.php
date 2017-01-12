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
                        field: "date_day",
                        title: "Date",
                        format: "{0:MM/dd/yyyy}",
                        type: "date",
                        sortable: true,
                        width: 100,
                    },
                    {
                        field: "week",
                        title: "Week",
                        format: "{0:MM/dd/yyyy}",
                        type: "date",
                        sortable: true,
                        width: 100,
                    },
                    {
                        field: "material_id",
                        title: "Material id",
                        type: "number",
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
                    
                ]
            }).data("kendoGrid");
        });
    </script>
</div>