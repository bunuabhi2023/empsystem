@extends('layout.main')
@section('content')

    <section>

        <div class="container-fluid"><span id="general_result"></span></div>


        <div class="container-fluid mb-3">
            <button type="button" class="btn btn-info" name="create_record" id="create_record"><i
                        class="fa fa-plus"></i> {{__('Add Sub Location')}}</button>
            <!--<button type="button" class="btn btn-danger" name="bulk_delete" id="bulk_delete"><i-->
            <!--            class="fa fa-minus-circle"></i> {{__('Bulk delete')}}</button>-->
        </div>


        <div class="table-responsive">
            <table id="location-table" class="table ">
                <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                    <th>{{trans('Sub Location No')}}</th>
                    <th>{{trans('Sub Location')}}</th>
                    <th>{{__('Sub Location Code')}}</th>
                    <th>{{__('Location')}}</th>
                    <th>{{__('Location Code')}}</th>
                    <th>{{__('Company')}}</th>
                    <th>{{__('Company Code')}}</th>
                    <th>{{__('Client Group')}}</th>
                    <th>{{__('Client Group Code')}}</th>
                    <th>{{__('Address Line 1')}}</th>
                    <th>{{__('Address Line 2')}}</th>
                    <th>{{trans('file.City')}}</th>
                    <th>{{trans('file.State')}}</th>
                    <th>{{trans('file.Country')}}</th>
                    <th>{{trans('file.ZIP')}}</th>
                    <th>{{trans('PAN')}}</th>
                    <th>{{trans('GSTIN')}}</th>
                    <th>{{trans('TAN')}}</th>
                    <th>{{trans('Bank Account No.')}}</th>
                    <th>{{trans('IFSC')}}</th>
                    <th>{{trans('Agreement Valid From')}}</th>
                    <th>{{trans('Agreement Valid Till')}}</th>
                    <th>{{trans('Agreement Doc')}}</th>
                    <th>{{trans('Payment Method')}}</th>
                    <th>{{trans('Special Remark')}}</th>
                    <th>{{trans('Payroll Cycle From')}}</th>
                    <th>{{trans('Payroll Cycle To')}}</th>
                    <th>{{trans('Invoicing Timeline')}}</th>
                    <th>{{trans('Payment Receivable Timeline')}}</th>
                    <th>{{trans('Payment Payable Timeline')}}</th>
                    <th>{{trans('Scope of Revenue')}}</th>
                    <th>{{trans('Service Charges')}}</th>
                    <th>{{trans('Contact Person 1')}}</th>
                    <th>{{trans('Designation')}}</th>
                    <th>{{trans('Contact No')}}</th>
                    <th>{{trans('Contact Email')}}</th>
                    <th>{{trans('Contact Person 2')}}</th>
                    <th>{{trans('Designation')}}</th>
                    <th>{{trans('Contact No')}}</th>
                    <th>{{trans('Contact Email')}}</th>
                </tr>
                </thead>

            </table>
        </div>
    </section>



    <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{__('Add Location')}}</h5>
                    <button type="button" data-dismiss="modal" id="close" aria-label="Close" class="close"><i class="dripicons-cross"></i></button>
                </div>

                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">

                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{trans('Sub Location')}} <span class="text-danger">*</span></label>
                                <input type="text" name="sub_location_name" id="e_sub_location_name" required class="form-control"
                                       placeholder="{{__('Unique Value',['key'=>trans('Sub Location')])}}">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Sub Location Code')}} <span class="text-danger">*</span></label>
                                <input type="text" name="location_code" id="e_location_code" required class="form-control" maxlength="5"
                                       placeholder="{{__('Unique Value',['key'=>trans('Location Code')])}}">
                            </div>


                            <div class="col-md-6 form-group">
                                <label>{{__('Location')}} <span class="text-danger">*</span></label>
                                <select name="location_id" id="e_location_id" class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains" required
                                        title='{{__('Selecting',['key'=>trans('Location')])}}...'>
                                    @foreach($locations as $location)
                                        <option value="{{$location->id}}">{{$location->location_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{__('Nature Of Business')}} <span class="text-danger">*</span></label>
                                <select name="nature_business" id="e_nature_business" class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains" required
                                        title='{{__('Selecting',['key'=>trans('Nature Of Business')])}}...'>
                                          <option value="Agriculture">Agriculture; plantations; other rural sectors</option>
                                          <option value="Basic Metal Production">Basic Metal Production</option>
                                          <option value="Chemical industries">Chemical industries</option>
                                          <option value="Commerce">Commerce</option>
                                          <option value="Construction">Construction</option>
                                          <option value="Education">Education</option>
                                          <option value="Financial services">Financial services; professional services</option>
                                          <option value="Food">Food; drink; tobacco</option>
                                          <option value="Forestry">Forestry; wood; pulp and paper</option>
                                          <option value="Health services">Health services</option>
                                          <option value="Hotels">Hotels; tourism; catering</option>
                                          <option value="Mining">Mining (coal; other mining)</option>
                                          <option value="Mechanical and electrical engineering">Mechanical and electrical engineering</option>
                                          <option value="Media">Media; culture; graphical</option>
                                          <option value="Oil and gas production">Oil and gas production; oil refining</option>
                                          <option value="Postal and telecommunications services">Postal and telecommunications services</option>
                                          <option value="Public service">Public service</option>
                                          <option value="Shipping">Shipping; ports; fisheries; inland waterways</option>
                                          <option value="Textiles">Textiles; clothing; leather; footwear</option>
                                          <option value="Transport">Transport (including civil aviation; railways; road transport)</option>
                                          <option value="Transport equipment manufacturing">Transport equipment manufacturing</option>
                                          <option value="Utilities">Utilities (water; gas; electricity)</option>
                                </select>
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
                                       placeholder={{trans("Pincode")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('PAN')}} <span class="text-danger">*</span></label>
                                <input type="text" name="pan" id="e_pan" class="form-control" required
                                       placeholder={{trans("PAN No.")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('GSTIN')}} <span class="text-danger">*</span></label>
                                <input type="text" name="gstin" id="e_gst" class="form-control" required
                                       placeholder={{trans("GSTIN No.")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('TAN')}} <span class="text-danger">*</span></label>
                                <input type="text" name="tan" id="e_tan" class="form-control" required
                                       placeholder={{trans("TAN No.")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Bank Account No.')}} <span class="text-danger">*</span></label>
                                <input type="text" name="acc_no" id="e_acc_no" class="form-control" required
                                       placeholder={{trans("Account No.")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('IFSC')}} <span class="text-danger">*</span></label>
                                <input type="text" name="ifsc" id="e_ifsc" class="form-control" required
                                       placeholder={{trans("IFSC")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Agreement Valid From')}} <span class="text-danger">*</span></label>
                                <input type="date" name="agr_valid_from" id="e_agr_valid_from" required class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Agreement Valid Till')}} <span class="text-danger">*</span></label>
                                <input type="date" name="agr_valid_till" id="e_agr_valid_till" required class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Agreement Upload')}} <span class="text-danger">*</span></label>
                                <input type="file" name="agreement" id="e_agreement" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Payment Method')}} <span class="text-danger">*</span></label>
                                <select name="payment_term" id="e_payment_term" required class="form-control selectpicker"
                                        data-live-search="true" data-live-search-style="contains"
                                        title='{{__('Selecting',['key'=>trans("Payment Term")])}}...'>
                                    <option value="Pay & Collect">Pay & Collect</option>
                                    <option value="Collect & Pay">Collect & Pay</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label>{{trans('Special Remark For Payment')}} <span class="text-danger">*</span></label>
                                <input type="text" name="special_remark" id="e_special_remark" class="form-control"
                                       placeholder={{trans("Special Remark For Payment")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Payroll Cycle From')}} <span class="text-danger">*</span></label>
                                <input type="date" name="payroll_cycle_from" required id="e_payroll_cycle_from" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Payroll Cycle To')}} <span class="text-danger">*</span></label>
                                <input type="date" name="payroll_cycle_to" required id="e_payroll_cycle_to" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Invocing Timeline')}} <span class="text-danger">*</span></label>
                                <input type="text" name="invoicing_timeline" required id="e_invoicing_timeline" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Payment Receivable Time line')}} <span class="text-danger">*</span></label>
                                <input type="text" name="payment_receivable" required id="e_payment_receivable" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Payment Payable Time line')}} <span class="text-danger">*</span></label>
                                <input type="text" name="payment_payable" required id="e_payment_payable" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Scope of Revenue')}} <span class="text-danger">*</span></label>
                                <input type="text" name="scope_revenue" required id="e_scope_revenue" class="form-control">
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Service Charges')}} <span class="text-danger">*</span></label>
                                <input type="text" name="service_charges" required id="e_service_charges" class="form-control">
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label>{{trans('Contact Person 1')}} <span class="text-danger">*</span></label>
                                <input type="text" name="contact_p_1" id="e_contact_p_1" class="form-control"
                                       placeholder={{trans("Contact Person 1")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Designation')}} <span class="text-danger">*</span></label>
                                <input type="text" name="designation_1" id="e_designation_1" class="form-control"
                                       placeholder={{trans("Designation")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Contact No.')}} <span class="text-danger">*</span></label>
                                <input type="tel" name="contact_no_1" id="e_contact_no_1" class="form-control"
                                       placeholder={{trans("Contact No. 1")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Contact Email')}} <span class="text-danger">*</span></label>
                                <input type="email" name="contact_email_1" id="e_contact_email_1" class="form-control"
                                       placeholder={{trans("Contact Email 1")}}>
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label>{{trans('Contact Person 2')}}</label>
                                <input type="text" name="contact_p_2" id="e_contact_p_2" class="form-control"
                                       placeholder={{trans("Contact Person 2")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Designation')}}</label>
                                <input type="text" name="designation_2" id="e_designation_2" class="form-control"
                                       placeholder={{trans("Designation")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Contact No.')}}</label>
                                <input type="tel" name="contact_no_2" id="e_contact_no_2" class="form-control"
                                       placeholder={{trans("Contact No. 2")}}>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{trans('Contact Email')}}</label>
                                <input type="email" name="contact_email_2" id="e_contact_email_2" class="form-control"
                                       placeholder={{trans("Contact Email 2")}}>
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
    
    
    
    <div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
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
                                        <th>{{trans('Sub Location')}}</th>
                                        <td id="sub_location_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Location Code')}}</th>
                                        <td id="location_code_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Location')}}</th>
                                        <td id="location_id"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Nature Of Business')}}</th>
                                        <td id="nature_of_business"></td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Documents')}}</th>
                                        <td><p id="pan"></p>
                                            <p id="gstin"></p>
                                            <p id="tan"></p>
                                            <p id="agrement_valid_from"></p>
                                            <p id="agrement_valid_till"></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>{{__('Contact Person 1')}}</th>
                                        <td><p id="contact_p_1"></p>
                                            <p id="designation_1"></p>
                                            <p id="contact_1"></p>
                                            <p id="email_1"></p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th>{{__('Contact Person 2')}}</th>
                                        <td><p id="contact_p_2"></p>
                                            <p id="designation_2"></p>
                                            <p id="contact_2"></p>
                                            <p id="email_2"></p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th>{{__('Payment Details')}}</th>
                                        <td><p id="payment_terms"></p>
                                            <p id="payment_remark"></p>
                                            
                                            
                                            <p id="account_no"></p>
                                            <p id="ifsc_code"></p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th>{{__('Other Details')}}</th>
                                        <td>
                                            <p>Payroll Cycle: <span id="payroll_cycle_from"></span> - <span id="payroll_cycle_to"></span></p>
                                            <p>Invoicing Timeline: <span id="invoicing_timeline"></span></p>
                                            <p>Payment Receivable: <span id="payment_receivable"></span></p>
                                            <p>Payment Payable: <span id="payment_payable"></span></p>
                                            <p>Scope Revenue: <span id="scope_revenue"></span></p>
                                            <p>Service Charges: <span id="service_charges"></span></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>{{trans('Agreement')}}</th>
                                        <td><a href="{{ URL::to('/public') }}/" id="agreement_link">View Agreement</a></td>
                                    </tr>

                                    <tr>
                                        <th>{{trans('file.Address')}}</th>
                                        <td><p id="address1_id"></p>
                                            <p id="address2_id"></p>
                                            <p id="pincode_id"></p>
                                            <p id="city_id"></p>
                                            <p id="state_id"></p>
                                            <p id="country_id"></p>
                                        </td>
                                    </tr>
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



@endsection

@push('scripts')
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function () {

            $('#location-table').DataTable({
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
                fixedColumns: {
                    left: 5, // Freeze the first 4 columns
                },
                fixedHeader: {
                    header: true,
                    footer: true
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sub_locations.tables') }}",
                    type: "POST"
                },
                createdRow: function (row, data, dataIndex) {
                    $(row).find('td:eq(0) .dt-checkboxes').attr('data-id', data.id);
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
                        data: 'name',
                        name: 'name',

                    },
                    {
                        data: 'location_code',
                        name: 'location_code'
                    },
                    {
                        data: 'location_id',
                        name: 'location_id'
                    },
                    {
                        data: 'location_id_code',
                        name: 'location_id_code'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'company_code',
                        name: 'company_code'
                    },
                    {
                        data: 'client_group',
                        name: 'client_group'
                    },
                    {
                        data: 'client_group_code',
                        name: 'client_group_code'
                    },
                    {
                        data: 'address1',
                        name: 'address1'
                    },
                    {
                        data: 'address2',
                        name: 'address2'
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
                        data: 'pincode',
                        name: 'pincode'
                    },
                    {
                        data: 'pan',
                        name: 'pan'
                    },
                    {
                        data: 'gst',
                        name: 'gst'
                    },
                    {
                        data: 'tan',
                        name: 'tan'
                    },
                    {
                        data: 'accountNo',
                        name: 'accountNo'
                    },
                    {
                        data: 'ifsc',
                        name: 'ifsc'
                    },
                    {
                        data: 'agr_valid_from',
                        name: 'agr_valid_from'
                    },
                    {
                        data: 'agr_valid_till',
                        name: 'agr_valid_till'
                    },
                    {
                        data: 'agreement_doc',
                        name: 'agreement_doc'
                    },
                    {
                        data: 'payment_term',
                        name: 'payment_term'
                    },
                    {
                        data: 'payment_remark',
                        name: 'payment_remark'
                    },
                    {
                        data: 'payroll_cycle_from',
                        name: 'payroll_cycle_from'
                    },
                    {
                        data: 'payroll_cycle_to',
                        name: 'payroll_cycle_to'
                    },
                    {
                        data: 'invoicing_timeline',
                        name: 'invoicing_timeline'
                    },
                    {
                        data: 'payment_receivable',
                        name: 'payment_receivable'
                    },
                    {
                        data: 'payment_payable',
                        name: 'payment_payable'
                    },
                    {
                        data: 'scope_revenue',
                        name: 'scope_revenue'
                    },
                    {
                        data: 'service_charges',
                        name: 'service_charges'
                    },
                    {
                        data: 'contact_p_1',
                        name: 'contact_p_1'
                    },
                    {
                        data: 'designation_1',
                        name: 'designation_1'
                    },
                    {
                        data: 'contact_1',
                        name: 'contact_1'
                    },
                    {
                        data: 'email_1',
                        name: 'email_1'
                    },
                    {
                        data: 'contact_p_2',
                        name: 'contact_p_2'
                    },
                    {
                        data: 'designation_2',
                        name: 'designation_2'
                    },
                    {
                        data: 'contact_2',
                        name: 'contact_2'
                    },
                    {
                        data: 'email_2',
                        name: 'email_2'
                    },
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
                        'targets': [0, 36]
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
                        filename: 'Sub Locations'
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
        

        $('#create_record').on('click', function () {
            $('.modal-title').text("{{__('Add Sub Location')}}");
            $('#action_button').val('{{trans("file.Add")}}');
            $('#action').val('{{trans("file.Add")}}');
            $('#formModal').modal('show');
        });

        $('#sample_form').on('submit', function (event) {
            event.preventDefault();
            if ($('#action').val() == '{{trans('file.Add')}}') {
                $.ajax({
                    url: "{{ route('sub-locations.store') }}",
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
                            $('#location-table').DataTable().ajax.reload();
                        }
                        $('#form_result').html(html).slideDown(300).delay(5000).slideUp(300);
                    }
                })
            }

            if ($('#action').val() == '{{trans('file.Edit')}}') {
                $.ajax({
                    url: "{{ route('sub_locations.update') }}",
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
                            setTimeout(function () {
                                $('#formModal').modal('hide');
                                $('#location-table').DataTable().ajax.reload();
                                $('#sample_form')[0].reset();
                            }, 2000);

                        }
                        $('#form_result').html(html).slideDown(300).delay(5000).slideUp(300);
                    }
                });
            }
        });


        $(document).on('click', '.view', function () {

            var id = $(this).attr('id');
            $('#form_result').html('');

            var target = "{{ url('/organization/sub-locations/view')}}/" + id;


            $.ajax({
                url: target,
                dataType: "json",
                success: function (html) {
                    console.log(html)
                    $('#sub_location_id').text(html.data[0].name);
                    $('#location_code_id').text(html.data[0].location_code);
                    $('#location_id').text(html.data[0].location_name);
                    $('#nature_of_business').text(html.data[0].nature_business);
                    $('#pan').text(html.data[0].pan);
                    $('#gstin').text(html.data[0].gst);
                    $('#tan').text(html.data[0].tan);
                    $('#agrement_valid_from').text(html.data[0].agr_valid_from);
                    $('#agrement_valid_till').text(html.data[0].agr_valid_till);
                    $('#contact_p_1').text(html.data[0].contact_p_1);
                    $('#designation_1').text(html.data[0].designation_1);
                    $('#contact_1').text(html.data[0].contact_1);
                    $('#email_1').text(html.data[0].email_1);
                    $('#contact_p_2').text(html.data[0].contact_p_2);
                    $('#designation_2').text(html.data[0].designation_2);
                    $('#contact_2').text(html.data[0].contact_2);
                    $('#email_2').text(html.data[0].email_2);
                    $('#payment_terms').text(html.data[0].payment_term.replace(/&amp;/g, "&"));
                    $('#payment_remark').text(html.data[0].payment_remark);
                    $('#payroll_cycle_from').text(html.data[0].payroll_cycle_from);
                    $('#payroll_cycle_to').text(html.data[0].payroll_cycle_to);
                    $('#invoicing_timeline').text(html.data[0].invoicing_timeline);
                    $('#payment_receivable').text(html.data[0].payment_receivable);
                    $('#payment_payable').text(html.data[0].payment_payable);
                    $('#scope_revenue').text(html.data[0].scope_revenue);
                    $('#service_charges').text(html.data[0].service_charges);
                    $('#account_no').text(html.data[0].accountNo);
                    $('#ifsc_code').text(html.data[0].ifsc_code);
                    $('#agreement_link').text(html.data[0].agreement);
                    $('#agreement_link').attr('href', $('#agreement_link').attr('href')+html.data[0].agreement+"");
                    $('#address1_id').text(html.data[0].address1);
                    $('#address2_id').text(html.data[0].address2);
                    $('#city_id').text(html.data[0].city);
                    $('#state_id').text(html.data[0].state);
                    $('#country_id').text(html.data[0].country);
                    $('#pincode_id').text(html.data[0].pincode);

                    $('.modal-title').text('{{trans('View')}}');
                    
                    $('#action_button').hide();
                    $('#view_modal').modal('show');
                }
            })
        });
        
        $(document).on('click', '.edit', function () {

            var id = $(this).attr('id');
            $('#form_result').html('');

            var target = "{{ url('/organization/sub-locations/edit')}}/" + id;

            $.ajax({
                url: target,
                dataType: "json",
                success: function (html) {
                    $('#e_sub_location_name').val(html.data.name);
                    $('#e_location_code').val(html.data.location_code);
                    $('#e_location_id').selectpicker('val', html.data.location_id);
                    $('#e_nature_business').selectpicker('val', html.data.nature_business);
                    $('#e_pan').val(html.data.pan);
                    $('#e_gst').val(html.data.gst);
                    $('#e_tan').val(html.data.tan);
                    $('#e_agr_valid_from').val(html.data.agr_valid_from);
                    $('#e_agr_valid_till').val(html.data.agr_valid_till);
                    $('#e_contact_p_1').val(html.data.contact_p_1);
                    $('#e_designation_1').val(html.data.designation_1);
                    $('#e_contact_no_1').val(html.data.contact_1);
                    $('#e_contact_email_1').val(html.data.email_1);
                    $('#e_contact_p_2').val(html.data.contact_p_2);
                    $('#e_designation_2').val(html.data.designation_2);
                    $('#e_contact_no_2').val(html.data.contact_2);
                    $('#e_contact_email_2').val(html.data.email_2);
                    $('#e_payment_term').selectpicker('val', html.data.payment_term.replace(/&amp;/g, "&"));
                    $('#e_special_remark').val(html.data.payment_remark);
                    $('#e_payroll_cycle_from').val(html.data.payroll_cycle_from);
                    $('#e_payroll_cycle_to').val(html.data.payroll_cycle_to);
                    $('#e_invoicing_timeline').val(html.data.invoicing_timeline);
                    $('#e_payment_receivable').val(html.data.payment_receivable);
                    $('#e_payment_payable').val(html.data.payment_payable);
                    $('#e_scope_revenue').val(html.data.scope_revenue);
                    $('#e_service_charges').val(html.data.service_charges);
                    $('#e_acc_no').val(html.data.accountNo);
                    $('#e_ifsc').val(html.data.ifsc);
                    $('#e_address1').val(html.data.address1);
                    $('#e_address2').val(html.data.address2);
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
                                
                                $('#e_state').selectpicker("val",html.data.state);
                            }
                        });
                    }
                    
                    $('#e_zip').val(html.data.pincode);

                    $('#hidden_id').val(html.data.id);
                    $('.modal-title').text('{{trans('file.Edit')}}');
                    $('#action_button').val('{{trans('file.Edit')}}');
                    $('#action').val('{{trans('file.Edit')}}');
                    $('#formModal').modal('show');
                }
            })
        });


        var delete_id;

        $(document).on('click', '.delete', function () {
            delete_id = $(this).attr('id');
            $('#confirmModal').modal('show');
            $('.modal-title').text('{{__('DELETE Record')}}');
            $('#ok_button').text('{{trans('file.OK')}}');

        });


        $(document).on('click', '#bulk_delete', function () {
            var id = [];
            let table = $('#location-table').DataTable();
            id = table.rows({selected: true}).ids().toArray();
            console.log(id);
            if (id.length > 0) {
                if (confirm('{{__('Delete Selection',['key'=>trans('Sub Location')])}}')) {
                    $.ajax({
                        url: '{{route('mass_delete_sub_location')}}',
                        method: 'POST',
                        data: {
                            locationIdArray: id
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
                alert('{{__('Please select atleast one checkbox')}}');
            }
        });


        $('#close').on('click', function () {
            $('#sample_form')[0].reset();
        });

        $('#ok_button').on('click', function () {
            var target = "{{ url('/organization/sub-locations/delete')}}/" + delete_id;
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
                        $('#location-table').DataTable().ajax.reload();
                    }, 2000);
                }
            })
        });

    })(jQuery);
</script>
@endpush
