<?php $__env->startSection('content'); ?>

    <section>
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-header with-border">
                    <h3 class="card-title text-center"><?php echo e(__('Monthly Attendance Info')); ?> <hr><span
                                        id="details_month_year" class="thin-text"></span></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" id="filter_form" class="form-horizontal">
                                <?php echo csrf_field(); ?>
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold"><?php echo e(trans('From Date')); ?> <span class="text-danger">*</span></label>
                                            <input class="form-control date"  name="from_date" id="from_date" type="text">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold"><?php echo e(trans('To Date')); ?> <span class="text-danger">*</span></label>
                                            <input class="form-control date"  name="to_date" id="to_date" type="text">
                                        </div>
                                    </div>

                                    
                                    <?php if((Auth::user()->can('monthly-attendances'))): ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="text-bold"><?php echo e(trans('Client Groups')); ?> <span class="text-danger">*</span></label>
                                                <select name="client_grp_id" id="client_grp_id" required
                                                        class="form-control selectpicker"
                                                        data-live-search="true" data-live-search-style="contains"
                                                        data-shift_name="shift_name" data-dependent="company_name"
                                                        title="<?php echo e(__('Selecting',['key'=>trans('Client Group')])); ?>..." required>
                                                    <?php $__currentLoopData = $client_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($client_group->id); ?>" <?php echo e(isset($client_groupS) &&  $client_groupS == $client_group->id ? 'selected':''); ?>><?php echo e($client_group->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="text-bold"><?php echo e(trans('file.Company')); ?> <span class="text-danger">*</span></label>
                                                <input type="hidden" id="company_hidden" value="<?php echo e(isset($company) ? $company:''); ?>">
                                                <select name="company_id" id="company_id"
                                                        class="form-control selectpicker dynamic"
                                                        data-live-search="true" data-live-search-style="contains"
                                                        data-shift_name="shift_name" data-dependent="department_name"
                                                        title="<?php echo e(__('Selecting',['key'=>trans('file.Company')])); ?>...">
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="text-bold"><?php echo e(trans('file.Location')); ?> <span class="text-danger">*</span></label>
                                                <input type="hidden" id="location_hidden" value="<?php echo e(isset($location_id) ? $location_id:''); ?>">
                                                <select name="location_id" id="location_id"
                                                        class="form-control selectpicker"
                                                        data-live-search="true" data-live-search-style="contains"
                                                        data-shift_name="shift_name" data-dependent="locations_name"
                                                        title="<?php echo e(__('Selecting',['key'=>trans('file.Location')])); ?>...">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="text-bold"><?php echo e(trans('Sub Location')); ?> <span class="text-danger">*</span></label>
                                                <input type="hidden" id="sub_location_hidden" value="<?php echo e(isset($sub_location_id) ? $sub_location_id:''); ?>">
                                                <select name="sub_location_id" id="sub_location_id"
                                                        class="form-control selectpicker"
                                                        data-live-search="true" data-live-search-style="contains"
                                                        data-shift_name="shift_name" data-dependent="sub_locations_name"
                                                        title="<?php echo e(__('Selecting',['key'=>trans('Sub Location')])); ?>...">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button name="submit_form" id="submit_form" type="submit" class="btn btn-primary"><i class="fa fa fa-check-square-o"></i> <?php echo e(trans('file.Get')); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <span class="attendace_mark_info mb-3">
                <small><?php echo e(trans('file.present')); ?> = P  , <?php echo e(trans('file.Absent')); ?> = A  ,<?php echo e(trans('file.Leave')); ?> = L  , <?php echo e(trans('file.Holiday')); ?> = H  ,<?php echo e(__('Off Day')); ?> = O</small>
            </span>
        </div>
        <div class="table-responsive">
            <table id="month_wise_attendance-table" class="table ">
                <thead>
                <tr>
                    <th></th>
                    <th><?php echo e(trans('Employee Code')); ?> </th>
                    <th><?php echo e(trans('file.Employee')); ?> </th>
                    <th><?php echo e(trans('Sub Location Code')); ?> </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?php echo e(__('Total Present Days')); ?></th>
                    <th><?php echo e(__('Total Add/Ded')); ?></th>
                    <th><?php echo e(__('Total Week Off')); ?></th>
                    <th><?php echo e(__('Total Holiday')); ?></th>
                    <th><?php echo e(__('Total Absent')); ?></th>
                    <th><?php echo e(__('Manual Adjustment')); ?></th>
                    <th><?php echo e(__('Net Payable Days')); ?></th>
                    <th><?php echo e(__('Total Worked Hours')); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </section>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">

    (function($) {
        "use strict";

        $(document).ready(function() {

            let date = $('.date');
            date.datepicker({
                format: "dd MM yyyy",
                // startView: "dates",
                // minViewMode: 1,
                // autoclose: true,
            }).datepicker("setDate", new Date());

            fill_datatable();

            function fill_datatable(filter_group = '', filter_company = '', filter_loc = '', filter_sub_loc = '', filter_employee = '', filter_month_year = $('#month_year').val(), from_date = $('#from_date').val(), to_date = $('#to_date').val()) {
                $('#details_month_year').html($('#month_year').val());
                let table_table = $('#month_wise_attendance-table').DataTable({
                    initComplete: function () {
                        this.api().columns([2, 4]).every(function () {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });

                            column.data().unique().sort().each(function (d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                                $('select').selectpicker('refresh');
                            });
                        });
                    },
                    responsive: false,
                    scrollX: true,
                    fixedHeader: {
                        header: true,
                        footer: true
                    },
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "<?php echo e(route('monthly_attendances.tables')); ?>",
                        type: "POST",
                        data: {
                            filter_group: filter_group,
                            filter_company: filter_company,
                            filter_loc: filter_loc,
                            filter_sub_loc: filter_sub_loc,
                            filter_employee: filter_employee,
                            to_date: $('#to_date').val(),
                            from_date: $('#from_date').val(),
                            "_token": "<?php echo e(csrf_token()); ?>"
                        },
                        // success: function (data) {
                        //     console.log(data);
                        // },
                        dataSrc: function ( json ) {
                            $.each( json.date_range, function( key, value ) {
                                $( table_table.column( key+4 ).header() ).text(value);
                            });
                            for (var i = json.date_range.length; i < 31; i++) {
                                table_table.column( i+2 ).visible( false );
                            }
                            return json.data;
                        }
                    },

                    columns: [
                        {
                            data: null,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'employee_code',
                            name: 'employee_code',
                        },
                        {
                            data: 'employee_name',
                            name: 'employee_name',
                        },
                        {
                            data: 'sub_loc_code',
                            name: 'sub_loc_code',
                        },
                        {
                            data: 'day1',
                            name: 'day1',
                        },
                        {
                            data: 'day2',
                            name: 'day2',
                        },
                        {
                            data: 'day3',
                            name: 'day3',
                        },
                        {
                            data: 'day4',
                            name: 'day4',
                        },
                        {
                            data: 'day5',
                            name: 'day5',
                        },
                        {
                            data: 'day6',
                            name: 'day6',
                        },
                        {
                            data: 'day7',
                            name: 'day7',
                        },
                        {
                            data: 'day8',
                            name: 'day8',
                        },
                        {
                            data: 'day9',
                            name: 'day9',
                        },
                        {
                            data: 'day10',
                            name: 'day10',
                        },
                        {
                            data: 'day11',
                            name: 'day11',
                        },
                        {
                            data: 'day12',
                            name: 'day12',
                        },
                        {
                            data: 'day13',
                            name: 'day13',
                        },
                        {
                            data: 'day14',
                            name: 'day14',
                        },
                        {
                            data: 'day15',
                            name: 'day15',
                        },
                        {
                            data: 'day16',
                            name: 'day16',
                        },
                        {
                            data: 'day17',
                            name: 'day17',
                        },
                        {
                            data: 'day18',
                            name: 'day18',
                        },
                        {
                            data: 'day19',
                            name: 'day19',
                        },
                        {
                            data: 'day20',
                            name: 'day20',
                        },
                        {
                            data: 'day21',
                            name: 'day21',
                        },
                        {
                            data: 'day22',
                            name: 'day22',
                        },
                        {
                            data: 'day23',
                            name: 'day23',
                        },
                        {
                            data: 'day24',
                            name: 'day24',
                        },
                        {
                            data: 'day25',
                            name: 'day25',
                        },
                        {
                            data: 'day26',
                            name: 'day26',
                        },
                        {
                            data: 'day27',
                            name: 'day27',
                        },
                        {
                            data: 'day28',
                            name: 'day28',
                        },
                        {
                            data: 'day29',
                            name: 'day29',
                        },
                        {
                            data: 'day30',
                            name: 'day30',
                        },
                        {
                            data: 'day31',
                            name: 'day31',
                        },
                        {
                            data: 'worked_days',
                            name: 'worked_days',
                        },
                        {
                            data: 'tot_ded',
                            name: 'tot_ded',
                        },
                        {
                            data: 'week_off',
                            name: 'week_off',
                        },
                        {
                            data: 'tot_holiday',
                            name: 'tot_holiday',
                        },
                        {
                            data: 'tot_absent',
                            name: 'tot_absent',
                        },
                        {
                            data: 'total_adjust',
                            name: 'total_adjust',
                        },
                        {
                            data: 'net_total',
                            name: 'net_total',
                        },
                        {
                            data: 'total_worked_hours',
                            name: 'total_worked_hours',
                        },

                    ],


                    "order": [],
                    'language': {
                        'lengthMenu': '_MENU_ <?php echo e(__("records per page")); ?>',
                        "info": '<?php echo e(trans("file.Showing")); ?> _START_ - _END_ (_TOTAL_)',
                        "search": '<?php echo e(trans("file.Search")); ?>',
                        'paginate': {
                            'previous': '<?php echo e(trans("file.Previous")); ?>',
                            'next': '<?php echo e(trans("file.Next")); ?>'
                        }
                    },
                    'columnDefs': [
                        {
                            "orderable": false,
                            'targets': [0]
                        },
                        {
                            'render': function (data, type, row, meta) {
                                if (type == 'display') {
                                    data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                                }

                                return data;
                            },
                            'checkboxes': {
                                'selectRow': true,
                                'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                            },
                            'targets': [0]
                        },
                    ],

                    'select': {style: 'multi', selector: 'td:first-child'},
                    'lengthMenu': [[50, 100, 200, 500, -1], [50, 100, 200, 500, "All"]],
                    dom: '<"row"lfB>rtip',
                    buttons: [
                        {
                            extend: 'pdf',
                            orientation: 'landscape',
                            pageSize : 'LEGAL',
                            text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible'
                            },
                        },
                        {
                            extend: 'csv',
                            orientation: 'landscape',
                            pageSize : 'LEGAL',
                            text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible'
                            },
                        },
                        {
                            extend: 'print',
                            orientation: 'landscape',
                            pageSize : 'LEGAL',
                            text: '<i title="print" class="fa fa-print"></i>',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible'
                            },
                        },
                    ],
                });
            }

            $('#submit_form').on('click', function (e) {
                e.preventDefault();

                var filter_group = $('#client_grp_id').val();
                var filter_company = $('#company_id').val();
                var filter_loc = $('#location_id').val();
                var filter_sub_loc = $('#sub_location_id').val();
                
                var filter_employee = $('#employee_id').val();
                
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                
                if (filter_group !== '' && from_date !== '' && to_date !== '') {
                    $('#month_wise_attendance-table').DataTable().destroy();
                    fill_datatable(filter_group, filter_company, filter_loc, filter_sub_loc, filter_employee, from_date, to_date);
                }
                else {
                    alert('<?php echo e(__('Select at least one filter option')); ?>');
                }
            });
        });


        $('#client_grp_id').change(function () {
            if ($(this).val() !== '') {
                let value = $(this).val();
                let dependent = $(this).data('dependent');
                let _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "<?php echo e(route('dynamic_companies')); ?>",
                    method: "POST",
                    data: {value: value, _token: _token, dependent: dependent},
                    success: function (result) {
                        $('select').selectpicker("destroy");
                        $('#company_id').html(result);
                        $('select').selectpicker();
                    }
                });
            }
        });
        
        //Company--> Location
        $('#company_id').change(function () {
            let value = $('#company_id').val();
            let _token = $('input[name="_token"]').val();
            
            $.ajax({
                url: "<?php echo e(route('dynamic_locations')); ?>",
                method: "POST",
                data: {value: value, _token: _token},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#location_id').html(result);
                    $('select').selectpicker();
                }
            });
        });
        
        //Location--> Sub Location
        $('#location_id').change(function () {
            let value = $('#location_id').val();
            let _token = $('input[name="_token"]').val();
            
            $.ajax({
                url: "<?php echo e(route('dynamic_sub_locations')); ?>",
                method: "POST",
                data: {value: value, _token: _token},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#sub_location_id').html(result);
                    $('select').selectpicker();
                }
            });
        });
        
        $('#sub_location_id').change(function () {
            let value = $('#sub_location_id').val();
            let _token = $('input[name="_token"]').val();
            
            $.ajax({
                url: "<?php echo e(route('dynamic_payroll_cycle')); ?>",
                method: "POST",
                data: {value: value, _token: _token},
                success: function (result) {
                    $('#dynamic_payroll_cycle').val(result);
                }
            });
        });
        
        if ($('#client_grp_id').val() !== '') {
            let value = $('#client_grp_id').val();
            let dependent = $('#client_grp_id').data('dependent');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "<?php echo e(route('dynamic_companies')); ?>",
                method: "POST",
                data: {value: value, _token: _token, dependent: dependent},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#company_id').html(result);
                    $('select').selectpicker();
                    $('#company_id').val($('#company_hidden').val());
                    
                    value = $('#company_id').val();
                    _token = $('input[name="_token"]').val();
                    
                    $.ajax({
                        url: "<?php echo e(route('dynamic_locations')); ?>",
                        method: "POST",
                        data: {value: value, _token: _token},
                        success: function (result) {
                            $('select').selectpicker("destroy");
                            $('#location_id').html(result);
                            $('select').selectpicker();
                            $('#location_id').val($('#location_hidden').val());
                            
                            value = $('#location_id').val();
                            _token = $('input[name="_token"]').val();
                            
                            $.ajax({
                                url: "<?php echo e(route('dynamic_sub_locations')); ?>",
                                method: "POST",
                                data: {value: value, _token: _token},
                                success: function (result) {
                                    $('select').selectpicker("destroy");
                                    $('#sub_location_id').html(result);
                                    $('select').selectpicker();
                                    
                                    $('#sub_location_id').selectpicker('val', $('#sub_location_hidden').val());
                                    
                                    let value = $('#sub_location_id').val();
                                    let _token = $('input[name="_token"]').val();
                                    
                                    $.ajax({
                                        url: "<?php echo e(route('dynamic_payroll_cycle')); ?>",
                                        method: "POST",
                                        data: {value: value, _token: _token},
                                        success: function (result) {
                                            $('#dynamic_payroll_cycle').val(result);
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }

    })(jQuery);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u540105116/domains/investation.team/public_html/app/resources/views/timesheet/monthlyAttendance/index.blade.php ENDPATH**/ ?>