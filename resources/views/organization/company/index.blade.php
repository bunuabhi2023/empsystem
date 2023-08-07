@extends('layout.main')
@section('content')



    <section>


        <div class="container-fluid mb-3">
            @can('store-company')
                <button type="button" class="btn btn-info" name="create_record" id="create_record"><i
                            class="fa fa-plus"></i> {{__('Add Company')}}</button>
            @endcan
            @can('delete-company')
                <button type="button" class="btn btn-danger" name="bulk_delete" id="bulk_delete"><i
                            class="fa fa-minus-circle"></i> {{__('Bulk delete')}}</button>
            @endcan
        </div>


        <div class="table-responsive">
            <table id="company-table" class="table ">
                <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                    <th>{{trans('Company No.')}}</th>
                    <th>{{trans('file.Company')}}</th>
                    <th>{{trans('Company Code')}}</th>
                    <th>{{trans('Client Group')}}</th>
                    <th>{{trans('Company Type')}}</th>
                    <th>{{trans('Trading/Legal Name')}}</th>
                    <th>{{trans('Registration No/CIN')}}</th>
                    <th>{{trans('Phone')}}</th>
                    <th>{{trans('file.Email')}}</th>
                    <th>{{trans('Website')}}</th>
                    <th>{{trans('Date Of Incorporation')}}</th>
                    <th>{{trans('Address Line 1')}}</th>
                    <th>{{trans('Address Line 2')}}</th>
                    <th>{{trans('City')}}</th>
                    <th>{{trans('State')}}</th>
                    <th>{{trans('Country')}}</th>
                    <th>{{trans('ZIP/Pincode')}}</th>
                    <th>{{trans('Remarks')}}</th>
                </tr>
                </thead>

            </table>
        </div>
    </section>



    <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{__('Add Company')}}</h5>
                    <button type="button" data-dismiss="modal" id="close" aria-label="Close" class="close"><i class="dripicons-cross"></i></button>
                </div>

                <div class="modal-body">
                    <span id="store_logo"></span>

                    <span id="form_result"></span>
                    <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">

                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{__('Company Name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" id="company_name" required class="form-control"
                                       placeholder="should be unique">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{__('Company Code')}} <span class="text-danger">*</span></label>
                                <input type="text" name="company_code" id="company_code" required class="form-control" maxlength="5"
                                       placeholder="should be unique and 5 digits alphanumerical">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{trans('Client Group')}} <span class="text-danger">*</span></label>
                                <select name="client_group_m" id="client_group_m" class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains"
                                        title='{{__('Selecting',['key'=>trans('Client Group')])}}...'>
                                    @foreach($client_groups as $client_group)
                                        <option value="{{$client_group->id}}">{{$client_group->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{__('Company/Entity Type')}} <span class="text-danger">*</span></label>
                                <select name="company_type" id="company_type" class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains"
                                        title='{{__('Selecting',['key'=>__('Company Type')])}}...'>
                                    <option value="private limited">{{__('Private Limited Company')}}</option>
                                    <option value="public company">{{trans('Public Company')}}</option>
                                    <option value="one person company">{{__('One Person Company')}}</option>
                                    <option value="sole proprietorship">{{trans('Sole Proprietorship')}}</option>
                                    <option value="partnership firm">{{trans('Partnership Firm')}}</option>
                                    <option value="limited liability company">{{__('Limited Liability Partnership (LLP)')}}</option>
                                    <option value="others">{{__('Others')}}</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{__('Trading/Legal Name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="trading_name" id="trading_name" class="form-control"
                                       placeholder="Enter value">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{__('Registration Number/CIN')}} <span class="text-danger">*</span></label>
                                <input type="text" name="registration_no" id="registration_no" class="form-control"
                                       placeholder="Enter value">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{__('Date of Incorporation')}} <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_inco" id="date_of_inco" required class="form-control">
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label>{{__('Address Line 1')}} <span class="text-danger">*</span></label>
                                <input type="text" name="address1" id="e_address1" required class="form-control" required
                                       placeholder="full address">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{__('Address Line 2')}} </label>
                                <input type="text" name="address2" id="e_address2" class="form-control"
                                       placeholder={{trans("Address Line 2")}}>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{trans('file.City')}} <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="e_city" class="form-control" required
                                       placeholder={{trans("City")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('file.Country')}} <span class="text-danger">*</span></label>
                                <select name="country" id="e_country" class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains" required
                                        title='{{__('Selecting',['key'=>trans('file.Country')])}}...'>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{trans('file.State')}} <span class="text-danger">*</span></label>
                                <select name="state" id="e_state" class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains" required
                                        title='{{__('Selecting',['key'=>trans('State')])}}...'></select>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('ZIP/Pincode')}} <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" id="e_zip" class="form-control" required
                                maxlength="6" placeholder={{trans("Pincode")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('file.Phone')}} <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" id="contact_no" class="form-control" required
                                       placeholder={{trans('file.Phone')}}>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{trans('file.Email')}} <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" class="form-control" required
                                       placeholder={{trans('file.Email')}}>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{trans('file.Website')}}</label>
                                <input type="text" name="website" id="website" class="form-control"
                                       placeholder={{trans("file.Optional")}}>
                            </div>

                            <!--<div class="col-md-6 form-group">-->
                            <!--    <label>{{trans('file.Location')}}</label>-->
                            <!--    <select name="location_id" id="location_id" class="form-control selectpicker"-->
                            <!--            data-live-search="true" data-live-search-style="contains"-->
                            <!--            title='{{__('Selecting',['key'=>trans('file.Location')])}}...'>-->
                            <!--        @foreach($locations as $location)-->
                            <!--            <option value="{{$location->id}}">{{$location->location_name}}</option>-->
                            <!--        @endforeach-->

                            <!--    </select>-->
                            <!--</div>-->

                            <div class="col-md-6 form-group">
                                <label>{{__('Company Logo')}} </label>
                                <input type="file" name="company_logo" id="company_logo" class="form-control"
                                       placeholder={{trans("file.Optional")}}>
                                <span id="store_logo"></span>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{__('Remarks')}}</label>
                                <input type="text" name="remarks" id="remarks_c" class="form-control"
                                       placeholder="{{__('Optional')}}">
                            </div>

                            <div class="form-group" align="center">
                                <input type="hidden" name="action" id="action"/>
                                <input type="hidden" name="hidden_id" id="hidden_id"/>
                                <input type="submit" name="action_button" id="action_button" class="btn btn-warning"
                                       value={{trans('file.Add')}} />
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>







    <div class="modal fade" id="company_modal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('Company Info')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <span id="logo_id"></span>

                    <div class="row">
                        <div class="col-md-12">

                            <div class="table-responsive">

                                <table class="table  table-bordered">
                                    <tr>
                                        <th>{{trans('Client Group')}}</th>
                                        <td id="client_group_id"></td>
                                    </tr>
                                    
                                    <tr>
                                        <th>{{trans('file.Company')}}</th>
                                        <td id="company_name_id"></td>
                                    </tr>
                                    
                                    <tr>
                                        <th>{{trans('Company Code')}}</th>
                                        <td id="company_code_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Company/Entity Type')}}</th>
                                        <td id="company_type_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Trading Name')}}</th>
                                        <td id="trading_name_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Registration Number')}}</th>
                                        <td id="registration_no_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Contact Number')}}</th>
                                        <td id="contact_no_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{trans('file.Email')}}</th>
                                        <td id="email_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{trans('file.Website')}}</th>
                                        <td id="website_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Tax Number')}}</th>
                                        <td id="tax_no_id"></td>
                                    </tr>
                                    
                                    <tr>
                                        <th>{{__('Remarks')}}</th>
                                        <td id="remarks_id"></td>
                                    </tr>

                                    <!--<tr>-->
                                    <!--    <th>{{trans('file.Address')}}</th>-->
                                    <!--    <td><p id="address1_id"></p>-->
                                    <!--        <p id="address2_id"></p>-->
                                    <!--        <p id="city_id"></p>-->
                                    <!--        <p id="state_id"></p>-->
                                    <!--        <p id="country_id"></p>-->
                                    <!--    </td>-->
                                    <!--</tr>-->

                                    <!--<tr>-->
                                    <!--    <th>{{trans('file.ZIP')}}</th>-->
                                    <!--    <td id="zip_id"></td>-->
                                    <!--</tr>-->


                                </table>

                            </div>

                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('file.Close')}}</button>
            </div>
        </div>
    </div>








    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h2 class="modal-title">{{trans('file.Confirmation')}}</h2>
                </div>
                <div class="modal-body">
                    <h4 align="center">{{__('Are you sure you want to remove this data?')}}</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">{{trans('file.OK')}}'
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('file.Cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function () {

            $('#company-table').DataTable({
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
                fixedColumns: {
                    left: 5, // Freeze the first 4 columns
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
                    url: "{{ route('companies.tables') }}",
                    type: "POST"
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
                        data: 'sr_no',
                        name: 'sr_no',

                    },
                    {
                        data: 'company_name',
                        name: 'company_name',

                    },
                    {
                        data: 'company_code',
                        name: 'company_code',

                    },
                    {
                        data: 'client_group',
                        name: 'client_group',

                    },
                    {
                        data: 'company_type',
                        name: 'company_type',

                    },
                    {
                        data: 'trading_name',
                        name: 'trading_name',

                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no',

                    },
                    {
                        data: 'contact_no',
                        name: 'contact_no',

                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'website',
                        name: 'website'
                    },
                    {
                        data: 'date_of_inco',
                        name: 'date_of_inco'
                    },
                    {
                        data: 'add1',
                        name: 'add1'
                    },
                    {
                        data: 'add2',
                        name: 'add2'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'zip',
                        name: 'zip'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },

                    // {
                    //     data: 'city',
                    //     name: 'city'
                    // },
                ],


                "order": [],
                'language': {
                    'lengthMenu': '_MENU_ {{__("records per page")}}',
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
                        'targets': [0, 19]
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
                    }
                ],


                'select': {style: 'multi', selector: 'td:first-child'},
                'lengthMenu': [[50, 100, 200, 500, -1], [50, 100, 200, 500, "All"]],
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
                        filename: 'Companies'
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
        });


        $('#create_record').on('click', function () {

            $('.modal-title').text('{{__('Add New Company')}}');
            $('#action_button').val('{{trans("file.Add")}}');
            $('#action').val('{{trans("file.Add")}}');
            $('#store_logo').html('');
            $('#formModal').modal('show');
        });

        $('#sample_form').on('submit', function (event) {
            event.preventDefault();
            if ($('#action').val() == '{{trans('file.Add')}}') {
                $.ajax({
                    url: "{{ route('companies.store') }}",
                    method: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: "json",
                    success: function (data) {
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#sample_form')[0].reset();
                            $('select').selectpicker('refresh');
                            $('#company-table').DataTable().ajax.reload();
                        }
                        $('#form_result').html(html).slideDown(300).delay(5000).slideUp(300);
                    }
                })
            }

            if ($('#action').val() == '{{trans('file.Edit')}}') {
                $.ajax({
                    url: "{{ route('companies.update') }}",
                    method: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: "json",
                    success: function (data) {
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#sample_form')[0].reset();
                            $('select').selectpicker('refresh');
                            $('#company-table').DataTable().ajax.reload();
                        }
                        $('#form_result').html(html).slideDown(300).delay(5000).slideUp(300);
                    }
                });
            }
        });
        
        $('#e_country').change(function (){
            if ($(this).val() !== '') {
                let value = $(this).val();
                let id = $(this).val();
                let _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('dynamic_state') }}",
                    method: "POST",
                    data: {value: value, _token: _token, id: id},
                    success: function (result) {
                        $('select').selectpicker("destroy");
                        $('#e_state').html(result);
                        $('select').selectpicker();
    
                    }
                });
            }
        });


        $(document).on('click', '.edit', function () {

            var id = $(this).attr('id');
            $('#form_result').html('');

            var target = "{{ url('/organization/companies/edit')}}/" + id;


            $.ajax({
                url: target,
                dataType: "json",
                success: function (html) {
                    $('#company_name').val(html.data.company_name);
                    $('#company_code').val(html.data.company_code);
                    $('#client_group_m').selectpicker('val', html.data.client_grp_id);
                    $('#company_type').selectpicker('val', html.data.company_type);
                    $('#trading_name').val(html.data.trading_name);
                    $('#registration_no').val(html.data.registration_no);
                    $('#contact_no').val(html.data.contact_no);
                    $('#email').val(html.data.email);
                    $('#website').val(html.data.website);
                    $('#remarks_c').val(html.data.remarks);
                    $('#tax_no').val(html.data.tax_no);
                    
                    $('#e_address1').val(html.data.add1);
                    $('#e_address2').val(html.data.add2);
                    $('#e_city').val(html.data.city);
                    
                    $('#e_country').selectpicker('val', html.data.country);
                    
                    if ($('#e_country').val() !== '') {
                        let value = $('#e_country').val();
                        let id = $('#e_country').val();
                        let _token = $('input[name="_token"]').val();
                        $.ajax({
                            url: "{{ route('dynamic_state') }}",
                            method: "POST",
                            data: {value: value, _token: _token, id: id},
                            success: function (result) {
                                $('select').selectpicker("destroy");
                                $('#e_state').html(result);
                                $('select').selectpicker();
            
                                $('#e_state').selectpicker('val', html.data.state);
                            }
                        });
                    }

                    
                    $('#e_zip').val(html.data.zip);
                    
                    // $('#location_id').selectpicker('val', html.data.location_id);
                    if (html.data.company_logo) {
                        $('#store_logo').html("<img src={{ URL::to('/public') }}/uploads/company_logo/" + html.data.company_logo + " width='70'  class='img-thumbnail' />");
                        $('#store_logo').append("<input type='hidden' name='hidden_image' value='" + html.data.company_logo + "'  />");
                    }
                    $('#hidden_id').val(html.data.id);
                    $('.modal-title').text('{{trans('file.Edit')}}');
                    $('#action_button').val('{{trans('file.Edit')}}');
                    $('#action').val('{{trans('file.Edit')}}');
                    $('#formModal').modal('show');
                }
            })
        });


        $(document).on('click', '.show_new', function () {

            var id = $(this).attr('id');
            $('#form_result').html('');

            var target = "{{ url('/organization/companies')}}/" + id;


            $.ajax({
                url: target,
                dataType: "json",
                success: function (result) {
                    // console.log(result.data);
                    $('#company_name_id').html(result.data.company_name);
                    $('#company_code_id').html(result.data.company_code);
                    $('#client_group_id').html(result.data.client_groups.name);
                    $('#company_type_id').html(result.data.company_type);
                    $('#trading_name_id').html(result.data.trading_name);
                    $('#registration_no_id').html(result.data.registration_no);
                    $('#contact_no_id').html(result.data.contact_no);
                    $('#email_id').html(result.data.email);
                    $('#website_id').html(result.data.website);
                    $('#tax_no_id').html(result.data.tax_no);
                    $('#remarks_id').html(result.data.remarks);
                    
                    if (result.data.company_logo) {
                        $('#logo_id').html("<img src={{ URL::to('/public') }}/uploads/company_logo/" + result.data.company_logo + " width='70'  class='img-thumbnail' />");
                        $('#logo_id').append("<input type='hidden'  name='hidden_image' value='" + result.data.company_logo + "'  />");
                    }
                    $('#company_modal').modal('show');
                    $('.modal-title').text('{{__('Company Info')}}');
                }
            });
        });


        let lid;

        $(document).on('click', '.delete', function () {
            lid = $(this).attr('id');
            $('#confirmModal').modal('show');
            $('.modal-title').text('{{__('DELETE Record')}}');
            $('#ok_button').text('{{trans('file.OK')}}');

        });


        $(document).on('click', '#bulk_delete', function () {
            let table = $('#company-table').DataTable();
            let id = [];
            id = table.rows({selected: true}).ids().toArray();
            if (id.length > 0) {
                if (confirm("Are you sure you want to delete the selected company?")) {
                    $.ajax({
                        url: '{{route('mass_delete_companies')}}',
                        method: 'POST',
                        data: {
                            companyIdArray: id
                        },
                        success: function (data) {
                            let html = '';
                            if (data.success) {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                            }
                            if (data.error) {
                                html = '<div class="alert alert-danger">' + data.error + '</div>';
                            }
                            table.ajax.reload();
                            table.rows('.selected').deselect();
                            if (data.errors) {
                                html = '<div class="alert alert-danger">' + data.error + '</div>';
                            }
                            $('#general_result').html(html).slideDown(300).delay(5000).slideUp(300);
                        }

                    });
                }
            } else {

            }

        });


        $('.close').on('click', function () {
            $('#sample_form')[0].reset();
            $('#store_logo').html('');
            $('#logo_id').html('');
            $('#company-table').DataTable().ajax.reload();
            $('select').selectpicker('refresh');


        });

        $('#ok_button').on('click', function () {
            var target = "{{ url('/organization/companies/delete')}}/" + lid;
            $.ajax({
                url: target,
                beforeSend: function () {
                    $('#ok_button').text('{{trans('file.Deleting...')}}');
                },
                success: function (data) {
                    let html = '';
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                    }
                    if (data.error) {
                        html = '<div class="alert alert-danger">' + data.error + '</div>';
                    }
                    setTimeout(function () {
                        $('#general_result').html(html).slideDown(300).delay(5000).slideUp(300);
                        $('#confirmModal').modal('hide');
                        $('#company-table').DataTable().ajax.reload();
                    }, 2000);
                }
            })
        });

    })(jQuery);
</script>
@endpush
