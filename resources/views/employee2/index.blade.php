@extends('layout.main')
@section('content')
<style>
    .activeFilters{
        color: color-yiq(#dc3545);
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>
    <section>

        <div class="container-fluid"><span id="general_result"></span></div>


        <div class="container-fluid mb-3">
            @can('store-details-employee')
                <button type="button" class="btn btn-info" name="create_record" id="create_record"><i
                            class="fa fa-plus"></i> {{__('Add Employee')}}</button>
            @endcan
            @can('modify-details-employee')
                <button type="button" class="btn btn-danger" name="bulk_delete" id="bulk_delete"><i
                            class="fa fa-minus-circle"></i> {{__('Bulk delete')}}</button>
            @endcan
            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-filter" aria-hidden="true"></i> Filter
            </button>
            <button class="btn btn-primary all_employees" type="button">
                <i class="fa fa-search" aria-hidden="true"></i> Active Employees
            </button>
            <button class="btn btn-primary pending_employees" type="button">
                <i class="fa fa-search" aria-hidden="true"></i> Pending Employees
            </button>
            <input type="hidden" id="pending_employee">
            <button class="btn btn-primary inactive_employees" type="button">
                <i class="fa fa-search" aria-hidden="true"></i> Inactive Employees
            </button>
            <input type="hidden" id="inactive_employee">
            <button class="btn btn-primary unapprove_employees" type="button">
                <i class="fa fa-search" aria-hidden="true"></i> Unapproved Employees (CSV)
            </button>
            <input type="hidden" id="unapprove_employee">
        </div>
        <div class="col-12">
            <!-- Filtering -->
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <form action="" method="GET" id="filter_form">
                        <div class="row">
                            <!-- Company -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-bold"><strong>{{trans('file.Company')}}</strong></label>
                                    <select name="company_id" id="company_id_filter"
                                            class="form-control selectpicker dynamic"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="department_name"
                                            title="{{__('Selecting',['key'=>trans('file.Company')])}}...">
                                            <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <!--/ Company-->

                            <!-- Department-->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-bold"><b>{{trans('file.Department')}}</b></label>
                                    <select name="department_id" id="department_id_filter"
                                            class="selectpicker form-control designationFilter"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-designation_name="designation_name"
                                            title="{{__('Selecting',['key'=>trans('file.Department')])}}...">
                                    </select>
                                </div>
                            </div>
                            <!--/ Department-->

                            <!-- Designation -->
                            <div class="col-md-3 form-group">
                                <label class="text-bold"><b>{{trans('file.Designation')}}</b></label>
                                <select name="designation_id" id="designation_id_filter" class="selectpicker form-control"
                                        data-live-search="true" data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>trans('file.Designation')])}}...">
                                </select>
                            </div>
                            <!--/ Designation -->

                            <!-- Office Shift -->
                            <div class="col-md-2 form-group">
                                <label class="text-bold"><b>{{__('Office Shift')}}</b></label>
                                <select name="office_shift_id" id="office_shift_id_filter" class="selectpicker form-control"
                                        data-live-search="true" data-live-search-style="contains"
                                        title="{{__('Selecting Office Shift')}}...">
                                </select>
                            </div>
                            <!--/ Office Shift -->

                            <div class="col-md-1">
                                <label class="text-bold"></label><br>
                                <button type="button" class="btn btn-dark" id="filterSubmit">
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i> &nbsp; GET
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Filtering -->
        </div>


        <div class="table-responsive">
            <table id="employee-table" class="table ">
                <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                    <th>{{trans('Staff Id')}}</th>
                    <th>{{trans('Full Name')}}</th>
                    <th>{{trans('Father Name')}}</th>
                    <th>{{trans('Mother Name')}}</th>
                    <th>{{trans('Email')}}</th>
                    <th>{{trans('Phone')}}</th>
                    <th>{{trans('Alt Phone')}}</th>
                    <th>{{trans('Address')}}</th>
                    <th>{{trans('City')}}</th>
                    <th>{{trans('Zip')}}</th>
                    <th>{{trans('State')}}</th>
                    <th>{{trans('Country')}}</th>
                    <th>{{trans('DOB')}}</th>
                    <th>{{trans('Gender')}}</th>
                    <th>{{trans('Marital Status')}}</th>
                    <th>{{trans('Marriage Anniversary')}}</th>
                    <th>{{trans('Religion')}}</th>
                    <th>{{trans('Blood Group')}}</th>
                    <th>{{trans('Disability')}}</th>
                    <th>{{trans('S_Disability')}}</th>
                    <th>{{trans('Client Group')}}</th>
                    <th>{{trans('Company')}}</th>
                    <th>{{trans('Location')}}</th>
                    <th>{{trans('Sub Location')}}</th>
                    <th>{{trans('Department')}}</th>
                    <th>{{trans('Designation')}}</th>
                    <th>{{trans('Status')}}</th>
                    <th>{{trans('Office Shift')}}</th>
                    <th>{{trans('DOJ')}}</th>
                    <th>{{trans('DOL')}}</th>
                    <th>{{trans('Attendance Type')}}</th>
                    <th>{{trans('Remaining Leave')}}</th>
                    <th>{{trans('Bank Name')}}</th>
                    <th>{{trans('Bank Branch')}}</th>
                    <th>{{trans('Account Number')}}</th>
                    <th>{{trans('Account Name')}}</th>
                    <th>{{trans('IFSC')}}</th>
                    <th>{{trans('Aadhar No')}}</th>
                    <th>{{trans('Pan No')}}</th>
                </tr>
                </thead>

            </table>
        </div>
    </section>



    <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{__('Add Employee')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">

                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="text-bold">{{__('Full Name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" placeholder="{{__('Full Name')}}"
                                       required class="form-control">
                            </div>
                            <div class="col-md-6 form-group d-none">
                                <label class="text-bold">{{__('Last Name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" placeholder="{{__('Last Name')}}"
                                       class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="text-bold">{{trans('file.Email')}}</label>
                                <input type="email" name="email" id="email" placeholder="example@example.com"
                                       class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="text-bold">{{trans('file.Phone')}} <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" id="contact_no"
                                       placeholder="{{trans('file.Phone')}}" required
                                       class="form-control" value="{{ old('contact_no') }}">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label class="text-bold">{{trans('file.Role')}} <span class="text-danger">*</span></label>
                                <select name="role_users_id" id="role_users_id" required
                                        class="selectpicker form-control"
                                        data-live-search="true" data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>trans('file.Role')])}}...">
                                    @foreach ($roles as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('Client Groups')}} <span class="text-danger">*</span></label>
                                    <select name="client_grp_id" id="client_grp_id" required
                                            class="form-control selectpicker"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="company_name"
                                            title="{{__('Selecting',['key'=>trans('Client Group')])}}...">
                                        @foreach($client_groups as $client_group)
                                            <option value="{{$client_group->id}}">{{$client_group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('file.Company')}} <span class="text-danger">*</span></label>
                                    <select name="company_id" id="company_id" required
                                            class="form-control selectpicker dynamic"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="department_name"
                                            title="{{__('Selecting',['key'=>trans('file.Company')])}}...">
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('file.Location')}} <span class="text-danger">*</span></label>
                                    <select name="location_id" id="location_id"
                                            class="form-control selectpicker"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="locations_name"
                                            title="{{__('Selecting',['key'=>trans('file.Location')])}}...">
                                        
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('Sub Location')}} <span class="text-danger">*</span></label>
                                    <select name="sub_location_id" id="sub_location_id"
                                            class="form-control selectpicker"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="sub_locations_name"
                                            title="{{__('Selecting',['key'=>trans('Sub Location')])}}...">
                                        
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('file.Department')}} <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_id" required
                                            class="selectpicker form-control designation"
                                            data-live-search="true" data-live-search-style="contains"
                                            data-designation_name="designation_name"
                                            title="{{__('Selecting',['key'=>trans('file.Department')])}}...">
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6 form-group">
                                <label class="text-bold">{{trans('file.Designation')}} <span class="text-danger">*</span></label>
                                <select name="designation_id" id="designation_id" required class="selectpicker form-control"
                                        data-live-search="true" data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>trans('file.Designation')])}}...">
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="text-bold">{{trans('file.Office_Shift')}} <span class="text-danger">*</span></label>
                                <select name="office_shift_id" id="office_shift_id" required class="selectpicker form-control"
                                        data-live-search="true" data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>trans('file.Office_Shift')])}}...">
                                </select>
                            </div>
                            
                            <div class="col-md-6 otherRoleAllowCompany">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('Allowed Client Group')}} <span class="text-danger">*</span></label>
                                    <select name="a_client_group[]" id="a_client_group"
                                            class="form-control selectpicker" multiple
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="company_name"
                                            title="{{__('Selecting',['key'=>trans('Client Group')])}}...">
                                        @foreach($client_groups as $client_group)
                                            <option value="{{$client_group->id}}">{{$client_group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 otherRoleAllowCompany">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('Allowed Company')}} <span class="text-danger">*</span></label>
                                    <select name="a_company_id[]" id="a_company_id"
                                            class="form-control selectpicker" multiple
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="department_name"
                                            title="{{__('Selecting',['key'=>trans('file.Company')])}}...">
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 otherRoleAllowCompany">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('Allowed Location')}} <span class="text-danger">*</span></label>
                                    <select name="a_location_id[]" id="a_location_id"
                                            class="form-control selectpicker" multiple
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="locations_name"
                                            title="{{__('Selecting',['key'=>trans('file.Location')])}}...">
                                        
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 otherRoleAllowCompany">
                                <div class="form-group">
                                    <label class="text-bold">{{trans('Allowed Sub Location')}} <span class="text-danger">*</span></label>
                                    <select name="a_sub_location_id[]" id="a_sub_location_id"
                                            class="form-control selectpicker" multiple
                                            data-live-search="true" data-live-search-style="contains"
                                            data-shift_name="shift_name" data-dependent="sub_locations_name"
                                            title="{{__('Selecting',['key'=>trans('Sub Location')])}}...">
                                        
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="col-md-6 form-group"  id="ipField"></div> --}}

                            <div class="container">
                                <div class="form-group" align="center">
                                    <input type="hidden" name="action" id="action"/>
                                    <input type="hidden" name="hidden_id" id="hidden_id"/>
                                    <input type="submit" name="action_button" id="action_button" class="btn btn-warning w-100" value="{{trans('file.Add')}}" />
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">{{trans('file.Confirmation')}}</h2>
                    <button type="button" class="employee-close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">{{__('Are you sure you want to remove this data?')}}</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button"
                            class="btn btn-danger">{{trans('file.OK')}}</button>
                    <button type="button" class="close btn-default"
                            data-dismiss="modal">{{trans('file.Cancel')}}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function () {
		$('.otherRoleAllowCompany').hide();
        
        $('#role_users_id').change(function(){
        	if($('#role_users_id').val() == 7){
        		$('.otherRoleAllowCompany').show();
        	}
        });

        if (window.location.href.indexOf('#formModal') != -1) {
            $('#formModal').modal('show');
        }

        var date = $('.date');
        date.datepicker({
            format: '{{ env('Date_Format_JS')}}',
            autoclose: true,
            todayHighlight: true
        });

        var table_table = $('#employee-table').DataTable({
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
            fixedColumns: {
                left: 4
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('employees.get_all') }}",
                type: 'POST',
                data: function (d) {
                    d.pending_employees     = $("#pending_employee").val();
                    d.inactive_employees     = $("#inactive_employee").val();
                    d.unapprove_employees     = $("#unapprove_employee").val();
                    d.company_id     = $("#company_id_filter").val();
                    d.department_id  = $('#department_id_filter').val();
                    d.designation_id = $('#designation_id_filter').val();
                    d.office_shift_id = $('#office_shift_id_filter').val();
                }
            },

            columns: [
                {
                    data: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                },
                {
                    data: 'staff_id',
                    name: 'staff_id',
                },
                {
                    data: 'full_name',
                    name: 'full_name',
                },
                {
                    data: 'fname',
                    name: 'fname',
                },
                {
                    data: 'mname',
                    name: 'mname',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'phone',
                    name: 'phone',
                },
                {
                    data: 'alt_phone',
                    name: 'alt_phone',
                },
                {
                    data: 'address',
                    name: 'address',
                },
                {
                    data: 'city',
                    name: 'city',
                },
                {
                    data: 'zip',
                    name: 'zip',
                },
                {
                    data: 'state',
                    name: 'state',
                },
                {
                    data: 'country',
                    name: 'country',
                },
                {
                    data: 'dob',
                    name: 'dob',
                },
                {
                    data: 'gender',
                    name: 'gender',
                },
                {
                    data: 'marital_status',
                    name: 'marital_status',
                },
                {
                    data: 'manniversary',
                    name: 'manniversary',
                },
                {
                    data: 'religion',
                    name: 'religion',
                },
                {
                    data: 'blood_group',
                    name: 'blood_group',
                },
                {
                    data: 'disability',
                    name: 'disability',
                },
                {
                    data: 'sdisability',
                    name: 'sdisability',
                },
                {
                    data: 'client_group',
                    name: 'client_group',
                },
                {
                    data: 'company',
                    name: 'company',
                },
                {
                    data: 'location',
                    name: 'location',
                },
                {
                    data: 'sub_location',
                    name: 'sub_location',
                },
                {
                    data: 'department',
                    name: 'department',
                },
                {
                    data: 'designation',
                    name: 'designation',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'office_shift',
                    name: 'office_shift',
                },
                {
                    data: 'doj',
                    name: 'doj',
                },
                {
                    data: 'dol',
                    name: 'dol',
                },
                {
                    data: 'attendance_type',
                    name: 'attendance_type',
                },
                {
                    data: 'rem_leave',
                    name: 'rem_leave',
                },
                {
                    data: 'bank_name',
                    name: 'bank_name',
                },
                {
                    data: 'bank_branch',
                    name: 'bank_branch',
                },
                {
                    data: 'account_no',
                    name: 'account_no',
                },
                {
                    data: 'account_name',
                    name: 'account_name',
                },
                {
                    data: 'ifsc_code',
                    name: 'ifsc_code',
                },
                {
                    data: 'aadhar',
                    name: 'aadhar',
                },
                {
                    data: 'pancard',
                    name: 'pancard',
                },
            ],


            "order": [],
            'language': {
                'lengthMenu': '_MENU_ {{__('records per page')}}',
                "info": '{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)',
                "search": '{{trans("file.Search")}}',
                'paginate': {
                    'previous': '{{trans("file.Previous")}}',
                    'next': '{{trans("file.Next")}}'
                }
            },
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0,38],
                    "className": "text-left"
                },
                {
                    'render': function (data, type, row, meta) {
                        if (type == 'display') {
                            data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label class="text-bold"></label></div>';
                        }

                        return data;
                    },
                    'checkboxes': {
                        'selectRow': true,
                        'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label class="text-bold"></label></div>'
                    },
                    'targets': [0]
                }
            ],


            'select': {style: 'multi', selector: 'td:first-child'},
            'lengthMenu': [[100, 200, 500, -1], [100, 200, 500, "All"]],
            dom: '<"row"lfB>rtip',
            buttons: [
                {
                    extend: 'pdf',
                    text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                },
                {
                    extend: 'csv',
                    text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    filename: 'Employees'
                },
                {
                    extend: 'print',
                    text: '<i title="print" class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                },
            ],
        });
        new $.fn.dataTable.FixedHeader(table_table);

    });


    //-------------- Filter -----------------------

    $('#filterSubmit').on("click",function(e){
        $('#pending_employee').val("");
        $('#inactive_employee').val("");
        $('#employee-table').DataTable().draw(true);
        //$('#filter_form')[0].reset();
        //$('select').selectpicker('refresh');
    });
    //--------------/ Filter ----------------------


    $('#create_record').click(function () {

        $('.modal-title').text("Add Employee");
        $('#action_button').val('{{trans('file.Add')}}');
        $('#action').val('{{trans('file.Add')}}');
        $('#formModal').modal('show');
    });

    $('#sample_form').on('submit', function (event) {
        event.preventDefault();
        // var attendance_type = $("#attendance_type").val();
        // console.log(attendance_type);

        $.ajax({
            url: "{{ route('employees.store') }}",
            method: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
                console.log(data);
                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.error) {
                    html = '<div class="alert alert-danger">' + data.error + '</div>';
                }
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#sample_form')[0].reset();
                    $('select').selectpicker('refresh');
                    $('.date').datepicker('update');
                    $('#employee-table').DataTable().ajax.reload();
                }
                $('#form_result').html(html).slideDown(300).delay(5000).slideUp(300);
            }
        });
    });


    let employee_delete_id;

    $(document).on('click', '.delete', function () {
        employee_delete_id = $(this).attr('id');
        $('#confirmModal').modal('show');
        $('.modal-title').text('{{__('DELETE Record')}}');
        $('#ok_button').text('{{trans('file.OK')}}');

    });


    $(document).on('click', '#bulk_delete', function () {

        var id = [];
        let table = $('#employee-table').DataTable();
        id = table.rows({selected: true}).ids().toArray();
        if (id.length > 0) {
            if (confirm('{{__('Delete Selection',['key'=>trans('file.Employee')])}}')) {
                $.ajax({
                    url: '{{route('mass_delete_employees')}}',
                    method: 'POST',
                    data: {
                        employeeIdArray: id
                    },
                    success: function (data) {
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success + '</div>';
                        }
                        if (data.error) {
                            html = '<div class="alert alert-danger">' + data.error + '</div>';
                        }
                        table.ajax.reload();
                        table.rows('.selected').deselect();
                        $('#general_result').html(html).slideDown(300).delay(5000).slideUp(300);

                    }

                });
            }
        } else {
            alert('{{__('Please select atleast one checkbox')}}');
        }
    });


    $('#close').click(function () {
        $('#sample_form')[0].reset();
        $('select').selectpicker('refresh');
        $('.date').datepicker('update');
        $('#employee-table').DataTable().ajax.reload();
    });

    $('#ok_button').click(function () {
        let target = "{{ route('employees.index') }}/" + employee_delete_id + '/delete';
        $.ajax({
            url: target,
            beforeSend: function () {
                $('#ok_button').text('{{trans('file.Deleting...')}}');
            },
            success: function (data) {
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                }
                if (data.error) {
                    html = '<div class="alert alert-danger">' + data.error + '</div>';
                }
                setTimeout(function () {
                    $('#general_result').html(html).slideDown(300).delay(5000).slideUp(300);
                    $('#confirmModal').modal('hide');
                    $('#employee-table').DataTable().ajax.reload();
                }, 2000);
            }
        })
    });




    $('#confirm_pass').on('input', function () {

        if ($('input[name="password"]').val() != $('input[name="password_confirmation"]').val())
            $("#divCheckPasswordMatch").html('{{__('Password does not match! please type again')}}');
        else
            $("#divCheckPasswordMatch").html('{{__('Password matches!')}}');

    });


    $('#client_grp_id').change(function () {
        if ($(this).val() !== '') {
            let value = $(this).val();
            let dependent = $(this).data('dependent');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_companies') }}",
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
    
    $('.dynamic').change(function () {
        if ($(this).val() !== '') {
            let value = $(this).val();
            let dependent = $(this).data('dependent');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_department') }}",
                method: "POST",
                data: {value: value, _token: _token, dependent: dependent},
                success: function (result) {

                    $('select').selectpicker("destroy");
                    $('#department_id').html(result);
                    $('select').selectpicker();

                }
            });
        }
    });


    $('.dynamic').change(function () {
        if ($(this).val() !== '') {
            let value = $(this).val();
            let dependent = $(this).data('shift_name');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_office_shifts') }}",
                method: "POST",
                data: {value: value, _token: _token, dependent: dependent},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#office_shift_id').html(result);
                    $('select').selectpicker();
                }
            });
        }
    });

    $('.designation').change(function () {
        if ($(this).val() !== '') {
            let value = $(this).val();
            let designation_name = $(this).data('designation_name');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_designation_department') }}",
                method: "POST",
                data: {value: value, _token: _token, designation_name: designation_name},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#designation_id').html(result);
                    $('select').selectpicker();

                }
            });
        }
    });


    // Login Type Change
    // $('#login_type').change(function() {
    //     var login_type = $('#login_type').val();
    //     if (login_type=='ip') {
    //         data = '<label class="text-bold">{{__("IP Address")}} <span class="text-danger">*</span></label>';
    //         data += '<input type="text" name="ip_address" id="ip_address" placeholder="Type IP Address" required class="form-control">';
    //         $('#ipField').html(data)
    //     }else{
    //         $('#ipField').empty();
    //     }
    // });



    //--------  Filter  ---------
    
    $('.all_employees').click(function (){
        $('.activeFilters').removeClass("activeFilters");
        $(this).addClass("activeFilters");
        
        $('#inactive_employee').val("");
        $('#pending_employee').val("");
        $('#unapprove_employee').val("");
        $('#employee-table').DataTable().draw(true);
    });
    
    $('.inactive_employees').click(function (){
        $('.activeFilters').removeClass("activeFilters");
        $(this).addClass("activeFilters");
        
        $('#inactive_employee').val("1");
        $('#pending_employee').val("");
        $('#unapprove_employee').val("");
        $('#employee-table').DataTable().draw(true);
    });
    
    $('.unapprove_employees').click(function (){
        $('.activeFilters').removeClass("activeFilters");
        $(this).addClass("activeFilters");
        
        $('#inactive_employee').val("");
        $('#pending_employee').val("");
        $('#unapprove_employee').val("1");
        $('#employee-table').DataTable().draw(true);
    });
    
    $('.pending_employees').click(function (){
        $('.activeFilters').removeClass("activeFilters");
        $(this).addClass("activeFilters");
        
        $('#pending_employee').val("1");
        $('#inactive_employee').val("");
        $('#unapprove_employee').val("");
        $('#employee-table').DataTable().draw(true);
    });

    // Company--> Department
    $('.dynamic').change(function () {
        if ($(this).val() !== '') {
            let value = $('#company_id_filter').val();
            let dependent = $(this).data('dependent');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_department') }}",
                method: "POST",
                data: {value: value, _token: _token, dependent: dependent},
                success: function (result) {

                    $('select').selectpicker("destroy");
                    $('#department_id_filter').html(result);
                    $('select').selectpicker();

                }
            });
        }
    });

    //Department--> Designation
    $('.designationFilter').change(function () {
        if ($(this).val() !== '') {
            // let value = $(this).val();
            // let value = $('#company_id_filter').val();
            let value = $('#department_id_filter').val();
            let designation_name = $(this).data('designation_name');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_designation_department') }}",
                method: "POST",
                data: {value: value, _token: _token, designation_name: designation_name},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#designation_id_filter').html(result);
                    $('select').selectpicker();

                }
            });
        }
    });

    //Company--> Office Shift
    $('.dynamic').change(function () {
        if ($(this).val() !== '') {
            // let value = $(this).val();
            let value = $('#company_id_filter').val();
            let dependent = $(this).data('shift_name');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_office_shifts') }}",
                method: "POST",
                data: {value: value, _token: _token, dependent: dependent},
                success: function (result) {
                    $('select').selectpicker("destroy");
                    $('#office_shift_id_filter').html(result);
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
            url: "{{ route('dynamic_locations') }}",
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
            url: "{{ route('dynamic_sub_locations') }}",
            method: "POST",
            data: {value: value, _token: _token},
            success: function (result) {
                $('select').selectpicker("destroy");
                $('#sub_location_id').html(result);
                $('select').selectpicker();
            }
        });
    });
    
    //Allowed Client Groups --> Companies
    $('#a_client_group').change(function () {
        let value = $('#a_client_group').val();
        let _token = $('input[name="_token"]').val();
        
        $.ajax({
            url: "{{ route('dynamic_companies') }}",
            method: "POST",
            data: {value: value, _token: _token},
            success: function (result) {
                $('select').selectpicker("destroy");
                $('#a_company_id').html(result);
                $('select').selectpicker();
            }
        });
    });
    
    //Allowed Company--> Locations
    $('#a_company_id').change(function () {
        let value = $('#a_company_id').val();
        let _token = $('input[name="_token"]').val();
        
        $.ajax({
            url: "{{ route('dynamic_locations') }}",
            method: "POST",
            data: {value: value, _token: _token},
            success: function (result) {
                $('select').selectpicker("destroy");
                $('#a_location_id').html(result);
                $('select').selectpicker();
            }
        });
    });
    
    //Allowed Location--> Allowed Sub Location
    $('#a_location_id').change(function () {
        let value = $('#a_location_id').val();
        let _token = $('input[name="_token"]').val();
        
        $.ajax({
            url: "{{ route('dynamic_sub_locations') }}",
            method: "POST",
            data: {value: value, _token: _token},
            success: function (result) {
                $('select').selectpicker("destroy");
                $('#a_sub_location_id').html(result);
                $('select').selectpicker();
            }
        });
    });

</script>
@endpush
