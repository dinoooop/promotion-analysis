

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
            destroy: {
            url: function (options) {
            return appConst.base_url + "/admin/promotions/" + options.id
            },
                    data: {_token: appConst.token},
                    dataType: 'json',
                    type: 'DELETE',
            },
    },
            schema: {
            model: {
            fields: {
            daily_baseline_ordered_amount: { type: "number" },
                    daily_baseline_ordered_units: { type: "number" },
                    wkly_baseline_ordered_amount: { type: "number" },
                    wkly_baseline_ordered_units: { type: "number" },
                    daily_during_ordered_amount: { type: "number" },
                    daily_during_ordered_units: { type: "number" },
                    wkly_during_ordered_amount: { type: "number" },
                    wkly_during_ordered_units: { type: "number" },
                    wkly_post_ordered_amount: { type: "number" },
                    wkly_post_ordered_units: { type: "number" },
                    daily_during_incremental_ordered_amount: { type: "number" },
                    daily_during_incremental_ordered_units: { type: "number" },
                    wkly_during_incremental_ordered_amount: { type: "number" },
                    wkly_during_incremental_ordered_units: { type: "number" },
                    wkly_post_incremental_ordered_amount: { type: "number" },
                    wkly_post_incremental_ordered_units: { type: "number" },
                    total_during_incremental_ordered_amount: { type: "number" },
                    total_during_incremental_ordered_units: { type: "number" },
                    total_post_incremental_ordered_amount: { type: "number" },
                    total_post_incremental_ordered_units: { type: "number" },
                    during_lift_ordered_amount: { type: "number" },
                    during_lift_ordered_units: { type: "number" },
                    post_lift_ordered_amount: { type: "number" },
                    post_lift_ordered_units: { type: "number" },
                    no_of_promotion_days: { type: "number" },
            }
            }
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
            field: "material_id",
                    title: "material_id",
                    width: 90,
                    locked: true,
            },
            {
            field: "ASIN",
                    title: "asin",
                    width: 90,
                    locked: true,
            },
            {
            title: 'Baseline (daily)',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "daily_baseline_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "daily_baseline_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
            title: 'Baseline (wkly)',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "wkly_baseline_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "wkly_baseline_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
<?php if ($is_single_day): ?>
                title: 'During (daily)',
                        columns: [
                        {
                        title: "POS Sales",
                                field: "daily_during_ordered_amount",
                                width: 90,
                        },
                        {
                        title: "POS units",
                                field: "daily_during_ordered_units",
                                width: 90,
                        },
                        ],
<?php else: ?>
                title: 'During (wkly)',
                        columns: [
                        {
                        title: "POS Sales",
                                field: "wkly_during_ordered_amount",
                                width: 90,
                        },
                        {
                        title: "POS units",
                                field: "wkly_during_ordered_units",
                                width: 90,
                        },
                        ],
<?php endif; ?>

            },
            {
            title: 'Post (wkly)',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "wkly_post_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "wkly_post_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
<?php if ($is_single_day): ?>
                title: 'During Incremental (daily)',
                        columns: [
                        {
                        title: "POS Sales",
                                field: "daily_during_incremental_ordered_amount",
                                width: 90,
                        },
                        {
                        title: "POS units",
                                field: "daily_during_incremental_ordered_units",
                                width: 90,
                        },
                        ],
<?php else: ?>
                title: 'During Incremental (wkly)',
                        columns: [
                        {
                        title: "POS Sales",
                                field: "wkly_during_incremental_ordered_amount",
                                width: 90,
                        },
                        {
                        title: "POS units",
                                field: "wkly_during_incremental_ordered_units",
                                width: 90,
                        },
                        ],
<?php endif; ?>

            },
            {
            title: 'Post incremental (wkly)',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "wkly_post_incremental_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "wkly_post_incremental_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
            title: 'Total During Incremental',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "total_during_incremental_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "total_during_incremental_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
            title: 'Total Post Incremental',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "total_post_incremental_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "total_post_incremental_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
            title: 'During lift',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "during_lift_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "during_lift_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
            title: 'Post lift',
                    columns: [
                    {
                    title: "POS Sales",
                            field: "post_lift_ordered_amount",
                            width: 90,
                    },
                    {
                    title: "POS units",
                            field: "post_lift_ordered_units",
                            width: 90,
                    },
                    ],
            },
            {
            title: '# days',
                    field: "no_of_promotion_days",
                    width: 90,
            },
            ]
    }).data("kendoGrid");
    });
</script>