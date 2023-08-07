@extends('layout.reg_form_front')
@section('content')
    <style>
        .nav-tabs li a {
            padding: 0.75rem 1.25rem;
        }

        .nav-tabs.vertical li {
            border: 1px solid #ddd;
            display: block;
            width: 100%
        }

        .tab-pane {
            padding: 15px 0
        }

    </style>
    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    @if($employee->is_active == 0)
                    <div class="text-center">
                        <h2>Fill your registration form!</h2>
                    </div>
                    <ul class="nav nav-tabs d-flex justify-content-between" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#General" role="tab"
                               aria-controls="General" aria-selected="true">{{trans('file.General')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#Profile" role="tab"
                               aria-controls="Profile" aria-selected="false">{{trans('file.Profile')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="General" role="tabpanel"
                             aria-labelledby="general-tab">
                            <!--Contents for General starts here-->
                            {{__('General Info')}}
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <ul class="nav nav-tabs vertical" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#Basic"
                                               role="tab" aria-controls="Basic"
                                               aria-selected="true">{{trans('file.Basic')}}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{route('documents.show',$employee)}}"
                                               id="document-tab" data-toggle="tab" data-table="document"
                                               data-target="#Document" role="tab" aria-controls="Document"
                                               aria-selected="false">{{trans('file.Document')}}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{route('qualifications.show',$employee)}}"
                                               id="qualification-tab" data-toggle="tab" data-table="qualification"
                                               data-target="#Qualification" role="tab" aria-controls="Qualification"
                                               aria-selected="false">{{trans('file.Qualification')}}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{route('work_experience.show',$employee)}}"
                                               id="work_experience-tab" data-toggle="tab" data-table="work_experience"
                                               data-target="#Work_experience" role="tab" aria-controls="Work_experience"
                                               aria-selected="false">{{__('Work Experience')}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-9">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="Basic" role="tabpanel"
                                             aria-labelledby="basic-tab">
                                            <!--Contents for Basic starts here-->
                                            {{__('Basic Information')}}
                                            <hr>
                                            <span id="form_result"></span>
                                            <form method="post" id="basic_sample_form" class="form-horizontal"
                                                  enctype="multipart/form-data" autocomplete="off">

                                                @csrf
                                                <div class="row">

                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('First Name')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="first_name" id="first_name"
                                                               placeholder="{{__('First Name')}}"
                                                                class="form-control"
                                                               value="{{ $employee->first_name }}">
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Last Name')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="last_name" id="last_name"
                                                               placeholder="{{__('Last Name')}}"
                                                                class="form-control"
                                                               value="{{ $employee->last_name }}">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Father\'s Name')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="fname" id="fname"
                                                               placeholder="{{__('Father\'s Name')}}"
                                                                class="form-control"
                                                               value="{{ $employee->fname }}">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Mother\'s Name')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="mname" id="mname"
                                                               placeholder="{{__('Mother\'s Name')}}"
                                                                class="form-control"
                                                               value="{{ $employee->mname }}">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Date Of Birth')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="date_of_birth" id="date_of_birth"
                                                                autocomplete="off" class="form-control date"
                                                               value="{{$employee->date_of_birth}}">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.Gender')}}</label>
                                                        <input type="hidden" name="gender_hidden"
                                                               value="{{ $employee->gender }}"/>
                                                        <select name="gender" id="gender"
                                                                class="selectpicker form-control"
                                                                data-live-search="true"
                                                                data-live-search-style="contains"
                                                                title="{{__('Selecting',['key'=>trans('file.Gender')])}}...">
                                                            <option value="Male">{{trans('file.Male')}}</option>
                                                            <option value="Female">{{trans('file.Female')}}</option>
                                                            <option value="Other">{{trans('file.Other')}}</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Marital Status')}}</label>
                                                        <input type="hidden" name="marital_status_hidden"
                                                               value="{{ $employee->marital_status }}"/>
                                                        <select name="marital_status" id="marital_status"
                                                                class="selectpicker form-control"
                                                                data-live-search="true"
                                                                data-live-search-style="contains"
                                                                title="{{__('Selecting',['key'=>__('Marital Status')])}}...">
                                                            <option value="single">{{trans('file.Single')}}</option>
                                                            <option value="married">{{trans('file.Married')}}</option>
                                                            <option value="widowed">{{trans('file.Widowed')}}</option>
                                                            <option value="divorced">{{trans('file.Divorced/Separated')}}</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Marriage Anniversary')}} (Optional)</label>
                                                        <input type="text" name="mAnniversary" id="mAnniversary"
                                                                autocomplete="off" class="form-control date"
                                                               value="{{$employee->maniversary}}">
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.Phone')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="contact_no" id="contact_no"
                                                               placeholder="{{trans('file.Phone')}}"
                                                                class="form-control"
                                                               value="{{ $employee->contact_no }}">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('Alt Phone')}} </label>
                                                        <input type="text" name="alt_phone" id="alt_phone"
                                                               placeholder="{{trans('Alt Phone')}}"
                                                                class="form-control"
                                                               value="{{ $employee->alt_phone }}">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.Email')}}</label>
                                                        <input type="text" name="email" id="email"
                                                               placeholder="{{trans('file.Email')}}"
                                                               class="form-control"
                                                               value="{{ $employee->email }}">
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.Address')}} </label>
                                                        <input type="text" name="address" id="address"
                                                               placeholder="Address"
                                                               value="{{$employee->address}}" class="form-control">
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('Address Line 2')}} <span class="text-danger">*</span></label>
                                                        <input type="text" name="address2" id="address2"
                                                               placeholder="Address Ln 2"
                                                               value="{{$employee->address2}}" class="form-control">
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.City')}} </label>
                                                        <input type="text" name="city" id="city"
                                                               placeholder="{{trans('file.City')}}"
                                                               value="{{$employee->city}}" class="form-control">
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.State/Province')}}</label>
                                                        <input type="hidden" name="state_hidden"
                                                               value="{{ $employee->state }}"/>
                                                        <label>{{trans('file.State/Province')}}</label>
                                                       <select name="state" id="state" 
                                                                class="selectpicker form-control"
                                                                data-live-search="true"
                                                                data-live-search-style="contains"
                                                                title="{{__('Selecting',['key'=>__('State')])}}..."></select>
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{trans('file.ZIP')}} </label>
                                                        <input type="text" name="zip_code" id="zip_code"
                                                               placeholder="{{trans('file.ZIP')}}"
                                                               value="{{$employee->zip_code}}" class="form-control">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>{{trans('file.Country')}}</label>
                                                            <select name="country" id="country"
                                                                    class="form-control selectpicker"
                                                                    data-live-search="true"
                                                                    data-live-search-style="contains"
                                                                    title="{{__('Selecting',['key'=>trans('file.Country')])}}...">
                                                                @foreach($countries as $country)
                                                                    <option value="{{$country->id}}" {{ ($employee->country == $country->id) ? "selected" : '' }}>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Religion')}}</label>
                                                        <input type="hidden" name="religion_hidden"
                                                               value="{{ $employee->religion }}"/>
                                                        <select name="religion" id="religion"
                                                                class="selectpicker form-control"
                                                                data-live-search="true"
                                                                data-live-search-style="contains"
                                                                title="{{__('Selecting',['key'=>__('Religion')])}}...">
                                                            <option value="hinduism">{{trans('Hinduism')}}</option>
                                                            <option value="islam">{{trans('Islam')}}</option>
                                                            <option value="sikhism">{{trans('Sikhism')}}</option>
                                                            <option value="buddhism">{{trans('Buddhism')}}</option>
                                                            <option value="jainism">{{trans('Jainism')}}</option>
                                                            <option value="christianity">{{trans('Christianity')}}</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Blood Group')}}</label>
                                                        <input type="hidden" name="blood_grp_hidden"
                                                               value="{{ $employee->blood_grp }}"/>
                                                        <select name="blood_grp" id="blood_grp"
                                                                class="selectpicker form-control"
                                                                data-live-search="true"
                                                                data-live-search-style="contains"
                                                                title="{{__('Selecting',['key'=>__('Blood Group')])}}...">
                                                            <option value="A+">{{trans('A+')}}</option>
                                                            <option value="B+">{{trans('B+')}}</option>
                                                            <option value="AB+">{{trans('AB+')}}</option>
                                                            <option value="O+">{{trans('O+')}}</option>
                                                            <option value="A-">{{trans('A-')}}</option>
                                                            <option value="B-">{{trans('B-')}}</option>
                                                            <option value="AB-">{{trans('AB-')}}</option>
                                                            <option value="O-">{{trans('O-')}}</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__('Disability')}}</label>
                                                        <input type="hidden" name="disability_hidden"
                                                               value="{{ $employee->disability }}"/>
                                                        <select name="disability" id="disability"
                                                                class="selectpicker form-control"
                                                                data-live-search="true"
                                                                data-live-search-style="contains"
                                                                title="{{__('Selecting',['key'=>__('Disability')])}}...">
                                                            <option value="0">No</option>
                                                            <option value="1">Yes</option>
                                                        </select>
                                                        
                                                        <input type="hidden" placeholder="Specify Disability" value="{{ $employee->s_disability }}" id="s_disability" name="s_disability" class="form-control mt-2">
                                                    </div>
                                                    
                                                    <input type="hidden" name="username" id="username" placeholder="{{trans('file.Username')}}"  class="form-control" value="{{$employee->user->username}}">

                                                    <div class="mt-3 form-group row">
                                                        <div class="form-group row mb-0">
                                                            <div class="col-md-6 offset-md-4">
                                                                <button type="submit" class="btn btn-primary">
                                                                    Update Details
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>
                                        
                                        <div class="tab-pane fade" id="Document" role="tabpanel"
                                             aria-labelledby="document-tab">
                                            {{__('All Documents')}}
                                            <hr>
                                            @include('employee2.documents.indexf')
                                        </div>
                                        <div class="tab-pane fade" id="Qualification" role="tabpanel"
                                             aria-labelledby="qualification-tab">
                                            {{__('All Qualifications')}}
                                            <hr>
                                            @include('employee2.qualifications.indexf')
                                        </div>
                                        <div class="tab-pane fade" id="Work_experience" role="tabpanel"
                                             aria-labelledby="work_experience-tab">
                                            {{__('Work Experience')}}
                                            <hr>
                                            @include('employee2.work_experience.indexf')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Contents for General Ends here-->
                        </div>
                        <div class="tab-pane fade" id="Profile" role="tabpanel" aria-labelledby="profile-tab">
                            <!--Contents for Profile starts here-->
                            {{__('Profile Picture')}}
                            <hr>

                        @include('employee2.profile_picture.index')

                        <!--Contents for Profile ends here-->
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <small>When you have submitted all the details correctly. Click on this button</small>
                        <button type="button" class="btn btn-success" id="mainSubmitBtn">Submit</button>
                    </div>
                    @else
                    <div class="text-center">
                        <h4>Your Form Has Been Submitted To The Admin! Please wait for approval</h4>
                    </div>
                    <script>
                        setTimeout(function(){
                            window.location.href = "/";
                        }, 5000);
                    </script>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
<script type="text/javascript">

    $('#mainSubmitBtn').click(function (){
        $.ajax({
            url: "{{route('employeesf.submit',$employee->id)}}",
            type: "GET",
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
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                }
                window.location.href = "/";
            }
        });
    });
    

    $('select[name="gender"]').val($('input[name="gender_hidden"]').val());
    $('#marital_status').selectpicker('val', $('input[name="marital_status_hidden"]').val());
    
    $('#religion').selectpicker('val', $('input[name="religion_hidden"]').val());
    $('#blood_grp').selectpicker('val', $('input[name="blood_grp_hidden"]').val());
    $('#disability').selectpicker('val', $('input[name="disability_hidden"]').val());
    
    if($('#disability').val() == "1"){
        $('#s_disability').attr("type", "text");
    }else{
        $('#s_disability').attr("type", "hidden");
    }
    
    $('#disability').change(function (){
        if($('#disability').val() == "1"){
            $('#s_disability').attr("type", "text");
        }else{
            $('#s_disability').attr("type", "hidden");
        }
    });
    
    if ($('#country').val() !== '') {
        let value = $('#country').val();
        let id = $('#country').val();
        let _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('dynamic_state') }}",
            method: "POST",
            data: {value: value, _token: _token, id: id},
            success: function (result) {
                $('select').selectpicker("destroy");
                $('#state').html(result);
                $('#state').selectpicker("val", $('input[name="state_hidden"]').val());
                $('select').selectpicker();

            }
        });
    }
    
    $('#country').change(function (){
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
                    $('#state').html(result);
                    $('select').selectpicker();

                }
            });
        }
    });
    
    $(document).ready(function () {

        let date = $('.date');
        date.datepicker({
            format: '{{ env('Date_Format_JS')}}',
            autoclose: true,
            todayHighlight: true
        });

        let month_year = $('.month_year');
        month_year.datepicker({
            format: "MM-yyyy",
            startView: "months",
            minViewMode: 1,
            autoclose: true,
        }).datepicker("setDate", new Date());
    });

    $('[data-table="document"]').one('click', function (e) {
            @include('employee2.documents.fjs')
    });

    $('[data-table="qualification"]').one('click', function (e) {
        @include('employee2.qualifications.fjs')
    });

    $('[data-table="work_experience"]').one('click', function (e) {
        @include('employee2.work_experience.fjs')
    });

    $('#profile-tab').one('click', function (e) {
        @include('employee2.profile_picture.fjs')
    });

    $('#basic_sample_form').on('submit', function (event) {
        event.preventDefault();
        var attendance_type = $("#attendance_type").val();
        // console.log(attendance_type);

        $.ajax({
            url: "{{ route('employees_basicInfoF.update',$employee->id) }}",
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
                if (data.success) {
                    $('#remaining_leave').val(data.remaining_leave)
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                }
                $('#form_result').html(html).slideDown(300).delay(5000).slideUp(300);
            }
        });
    });
</script>
@endpush