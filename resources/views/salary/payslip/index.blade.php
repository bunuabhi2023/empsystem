@extends('layout.main')
@section('content')
<style>
    .dt-buttons{
        float: right;
    }
</style>
    <!-- Modal -->
    <div class="modal fade" id="importFields" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Import Fields</h5>
          </div>
          <form action="{{ route('payment_history.importFields') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="modal-body">
                  <fieldset class="form-group">
                        <label for="logo">{{trans('file.Upload')}} {{trans('file.File')}}</label>
                        <input type="file" class="form-control-file" id="file" name="file"
                               accept=".xlsx, .xls, .csv">
                        <small>{{__('Please select excel/csv')}} file (allowed file size 2MB)</small>
                    </fieldset>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit Payment</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    
    <section>
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-header with-border">
                    <h3 class="card-title text-center"> {{__('Filter Payment History')}} </h3>
                </div>
                <span id="bulk_payment_result"></span>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" id="filter_form" class="form-horizontal" >
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold">{{trans('Client Groups')}} <span class="text-danger">*</span></label>
                                            <select name="client_grp_id" id="client_grp_id" required
                                                    class="form-control selectpicker"
                                                    data-live-search="true" data-live-search-style="contains"
                                                    data-shift_name="shift_name" data-dependent="company_name"
                                                    title="{{__('Selecting',['key'=>trans('Client Group')])}}..." required>
                                                @foreach($client_groups as $client_group)
                                                    <option value="{{$client_group->id}}" {{ isset($client_groupS) &&  $client_groupS == $client_group->id ? 'selected':'' }}>{{$client_group->name}}</option>
                                                @endforeach 
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold">{{trans('file.Company')}} <span class="text-danger">*</span></label>
                                            <input type="hidden" id="company_hidden" value="{{ isset($company) ? $company:'' }}">
                                            <select name="company_id" id="company_id"
                                                    class="form-control selectpicker dynamic"
                                                    data-live-search="true" data-live-search-style="contains"
                                                    data-shift_name="shift_name" data-dependent="department_name"
                                                    title="{{__('Selecting',['key'=>trans('file.Company')])}}...">
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold">{{trans('file.Location')}} <span class="text-danger">*</span></label>
                                            <input type="hidden" id="location_hidden" value="{{ isset($location_id) ? $location_id:'' }}">
                                            <select name="location_id" id="location_id"
                                                    class="form-control selectpicker"
                                                    data-live-search="true" data-live-search-style="contains"
                                                    data-shift_name="shift_name" data-dependent="locations_name"
                                                    title="{{__('Selecting',['key'=>trans('file.Location')])}}...">
                                                
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold">{{trans('Sub Location')}} <span class="text-danger">*</span></label>
                                            <input type="hidden" id="sub_location_hidden" value="{{ isset($sub_location_id) ? $sub_location_id:'' }}">
                                            <select name="sub_location_id" id="sub_location_id"
                                                    class="form-control selectpicker"
                                                    data-live-search="true" data-live-search-style="contains"
                                                    data-shift_name="shift_name" data-dependent="sub_locations_name"
                                                    title="{{__('Selecting',['key'=>trans('Sub Location')])}}...">
                                                
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold">{{trans('Salary From')}} <span class="text-danger">*</span></label>
                                            <input type="date" name="salary_from" class="form-control" value="{{ isset($from) ? $from:'' }}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="text-bold">{{trans('Salary To')}} <span class="text-danger">*</span></label>
                                            <input type="date" name="salary_to" class="form-control" value="{{ isset($to) ? $to:''  }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div class="form-group">
                                        <div class="form-actions">
                                            <button id="payslip_filter" type="submit" class="filtering btn btn-primary"> <i class="fa fa-search"></i> {{trans('file.Search')}} </button>

                                            <button id="bulk_payment" onclick="$('#importFields').modal('show')" type="button" class="btn btn-primary"> <i class="fa fa-check-square-o"></i> {{__('Import Fields')}} </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-title text-center"><h3>{{__('Payment Info')}} <span id="details_month_year"></span></h3></div>
        <div class="container-fluid"><span id="general_result"></span></div>
        <div class="">
            <table id="pay_list-table" class="table table-responsive">
                @php
                    $categoryCounts = [
                        'variableCount' => 0,
                        'addCount' => 0,
                        'dedCount' => 0,
                        'contCount' => 0
                    ];
                
                    foreach ($components as $component) {
                        if ($component->type == 0 && $component->category == 0) {
                            $categoryCounts['variableCount']++;
                        } elseif ($component->type != 0 && $component->type != 1 && $component->category == 0) {
                            $categoryCounts['addCount']++;
                        } elseif ($component->category == 1) {
                            $categoryCounts['dedCount']++;
                        } elseif ($component->category == 0 && $component->type == 1) {
                            $categoryCounts['contCount']++;
                        }
                    }
                @endphp

                <style>
                    .table thead th, .table tbody td {
                        border: 1px solid !important;
                        text-align: center;
                    }
                </style>
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th colspan="23" class="text-center">{{trans('Associate Basic Details')}}</th>
                        <th colspan="{{$categoryCounts['variableCount']+1}}" class="text-center">{{trans('Fixed Payment')}}</th>
                        <th colspan="2">Payment Cycle</th>
                        <th colspan="2">Overtime</th>
                        <th colspan="3" class="text-center">{{trans('Working Days')}}</th>
                        <th colspan="{{$categoryCounts['variableCount']+1}}" class="text-center">{{trans('Variable Payment')}}</th>
                        <th colspan="{{$categoryCounts['addCount']+2}}" class="text-center">{{trans('Additional Payment')}}</th>
                        <th colspan="{{$categoryCounts['dedCount']+1}}" class="text-center">{{trans('Employee Deduction')}}</th>
                        <th colspan="{{$categoryCounts['contCount']+1}}" class="text-center">{{trans('Employer Contribution')}}</th>
                        <th class="text-center">{{trans('Net Payable')}}</th>
                        <th class="text-center">{{trans('CTC')}}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>Payslip</th>
                        
                        <th>{{trans('Sr. No.')}}</th>
                        <th>{{trans('Associate ID')}}</th>
                        <th>{{trans('Associate Name')}}</th>
                        <th>{{trans('Father Name')}}</th>
                        <th>{{trans('Sub Location')}}</th>
                        <th>{{trans('Sub Location Code')}}</th>
                        <th>{{trans('Payable Type')}}</th>
                        <th>{{trans('Department')}}</th>
                        <th>{{trans('Designation')}}</th>
                        <th>{{trans('Bank Name')}}</th>
                        <th>{{trans('IFSC')}}</th>
                        <th>{{trans('Account No.')}}</th>
                        <th>{{trans('PAN')}}</th>
                        <th>{{trans('Aadhar No.')}}</th>
                        <th>{{trans('UAN')}}</th>
                        <th>{{trans('PF No.')}}</th>
                        <th>{{trans('ESIC No.')}}</th>
                        <th>{{trans('D.O.J')}}</th>
                        <th>{{trans('D.O.B')}}</th>
                        <th>{{trans('D.O.L')}}</th>
                        <th>{{trans('Mobile No.')}}</th>
                        <th>{{trans('Alternate Mobile No.')}}</th>
                        <th>{{trans('Email ID')}}</th>
                        
                        <!--Fixed Salary-->
                        @foreach($components as $component)
                            @if($component->type == 0 && $component->category == 0)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        <th>{{trans('Gross Payment')}}</th>
                        
                        <th>From</th>
                        <th>To</th>
                        
                        <th>{{trans('Overtime Rate per Minute')}}</th>
                        <th>{{trans('Overtime (in Minute)')}}</th>
                        
                        <!--Working Days-->
                        <th>{{trans('Absent Days')}}</th>
                        <th>{{trans('Total Payable Days')}}</th>
                        <th>{{trans('Total Month Days')}}</th>
                        
                        <!--Variable Salary-->
                        @foreach($components as $component)
                            @if($component->type == 0 && $component->category == 0)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        <th>{{trans('Gross Payment')}}</th>
                        
                        <!--Additional Salary-->
                        @foreach($components->sortBy('type') as $component)
                            @if($component->type == 2 && $component->category == 0)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        @foreach($components->sortBy('type') as $component)
                            @if($component->type == 3 && $component->category == 0)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        <th>{{trans('Overtime')}}</th>
                        <th>{{trans('Payable')}}</th>
                        
                        <!--Employee Deduction-->
                        @foreach($components as $component)
                            @if($component->category == 1 && $component->type == 1)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        @foreach($components as $component)
                            @if($component->category == 1 && $component->type == 0)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        @foreach($components as $component)
                            @if($component->category == 1 && $component->type == 2)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        @foreach($components as $component)
                            @if($component->category == 1 && $component->type == 3)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        <th>{{trans('Total')}}</th>
                        
                        <!--Employer Contribution-->
                        @foreach($components as $component)
                            @if($component->category == 0 && $component->type == 1)
                                <th>{{ $component->title }}</th>
                            @endif
                        @endforeach
                        <th>{{trans('Total')}}</th>
                        
                        <th>{{trans('Payable To Associate')}}</th>
                        
                        <th>{{trans('CTC Of Payment')}}</th>
                        
                        <th class="text-center">{{trans('Ref. No.')}}</th>
                        <th class="text-center">{{trans('Invoice Number')}}</th>
                        <th class="text-center">{{trans('Invoice Date')}}</th>
                        <th class="text-center">{{trans('UTR Number')}}</th>
                        <th class="text-center">{{trans('Payment Date')}}</th>
                        <th class="text-center">{{trans('Remarks')}}</th>
                        <!--<th class="not-exported">{{trans('file.action')}}</th>-->
                    </tr>
                </thead>
                <tbody>
                    @php ($index = 1)
                    @if(isset($employees) && count($employees) > 0)
                        @foreach($employees as $employee)
                            <tr>
                                <td><a href="{{ route('payslip_details.show', [$employee, $employee->payroll->payslip_id]) }}">View Payslip</a></td>
                                <td>{{ $index++ }}</td>
                                
                                <td>{{ $employee->staff_id }}</td>
                                <td>{{ $employee->first_name }}</td>
                                <td>{{ $employee->fname }}</td>
                                <td>{{ $employee->sub_location->name }}</td>
                                <td>{{ $employee->sub_location->location_code }}</td>
                                <td>{{ $employee->payable_type }}</td>
                                <td>{{ $employee->department->department_name }}</td>
                                <td>{{ $employee->designation->designation_name }}</td>
                                <td>{{ $employee->bankAccounts[0]->bank_name }}</td>
                                <td>{{ $employee->bankAccounts[0]->bank_code }}</td>
                                <td>{{ $employee->bankAccounts[0]->account_number }}</td>
                                <td>{{ $employee->pancard }}</td>
                                <td>{{ $employee->aadhar }}</td>
                                <td>{{ $employee->uan }}</td>
                                <td>{{ $employee->pf_no }}</td>
                                <td>{{ $employee->esic_no }}</td>
                                <td>{{ $employee->joining_date }}</td>
                                <td>{{ $employee->date_of_birth }}</td>
                                <td>{{ $employee->exit_date != "" ? $employee->exit_date:"N/A" }}</td>
                                <td>{{ $employee->contact_no }}</td>
                                <td>{{ $employee->alt_phone }}</td>
                                <td>{{ $employee->email }}</td>
                                @if($employee->payroll)
                                    <?php
                                        $payroll = json_decode($employee->payroll, true);
                                        
                                        $addition = isset($payroll) ? $payroll['addition'] : 0;
                                        $deduction = isset($payroll) ? $payroll['deduction'] : 0;
                                        $adeduction = isset($payroll) ? $payroll['adeduction_settings'] : 0;
                                        $overtime_settings = isset($payroll) ? $payroll['overtime_settings'] : 0;
                                        
                                        $addition = json_decode($addition, true);
                                        $deduction = json_decode($deduction, true);
                                        $adeduction = json_decode($adeduction, true);
                                        $overtime_settings = json_decode($overtime_settings, true);

                                        $grossSalary = 0;
                                        $netSalary = 0;
                                    ?>
                                    @foreach ($components as $component)
                                        @if ($component->type === 0 && $component->category == 0)
                                            <?php
                                                $componentValue = isset($addition) && isset($addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))]) ? $addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))] : 0;
                                                $grossSalary += $componentValue;
                                            ?>
    
                                            <td>{{ number_format($componentValue) }}</td>
                                        @endif
                                    @endforeach
                                    
                                    <td>{{ number_format($grossSalary) }}</td>
                                    
                                    <td>{{ $from }}</td>
                                    <td>{{ $to }}</td>
                                    
                                    <?php
                                        $fromDateTime = new DateTime($from);
                                        $toDateTime = new DateTime($to);
                                        
                                        // Extract the year and month from the DateTime object
                                        $yearF = $fromDateTime->format('Y');
                                        $monthF = $fromDateTime->format('m');
                                        
                                        // Get the total number of days in the specified month
                                        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $monthF, $yearF);
                                        
                                        // $interval = $fromDateTime->diff($toDateTime);
                                        // $numberOfDays = $interval->days + 1;
                                        
                                        $presentDays = DB::table('attendances')
                                            ->select('attendance_date')
                                            ->where('employee_id', $employee->id)
                                            ->where('attendance_status', 'present')
                                            ->whereRaw('DAYOFWEEK(attendance_date) != 1') // Exclude Sundays (where 1 represents Sunday)
                                            ->whereBetween('attendance_date', [$from, $to]) // Add the date range condition
                                            ->distinct()
                                            ->get();
                                            
                                        $presentDays = count($presentDays);
                                        $payableDays = $payroll['payable_days'];
                                        
                                        $overtime = $payroll['total_overtime'];
                                        
                                        if($overtime_settings['calculation_type'] == 1){
                                            // Convert minutes to days
                                            $overtimeDays = $overtime / 1440;
                                            // Round the result to 2 decimal places
                                            $overtimeDays = number_format($overtimeDays);
                                            
                                            $payableDays += $overtimeDays;
                                        }else{
                                            $overtimePayment = $overtime_settings['rate'] * $overtime;
                                        }
                                    ?>
                                    
                                    <td>{{ $overtime_settings['rate'] }}</td>
                                    <td>{{ $overtime }}</td>
                                    
                                    <td>{{ $numberOfDays - $presentDays }}</td>
                                    <td>{{ $payableDays }}</td>
                                    <td>{{ $numberOfDays }}</td>
                                    
                                    @foreach ($components as $component)
                                        @if ($component->type === 0 && $component->category == 0)
                                            <?php
                                                $componentValue = isset($addition) && isset($addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))]) ? ($addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))] / $numberOfDays) * $payableDays : 0;
                                                $netSalary += $componentValue;
                                            ?>
                                            <td>{{ number_format(round($componentValue)) }}</td>
                                        @endif
                                    @endforeach
                                    
                                    <td>{{ number_format(round($netSalary)) }}</td>
                                    
                                    @php($addSalary = 0)
                                    @foreach($components as $component)
                                        @if($component->type == 2 && $component->category == 0)
                                            <?php
                                                $componentKey = 'a_fixed_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($addition[$componentKey]) && $addition[$componentKey] !== '' ? $addition[$componentKey] : 0;
                                                $addSalary += $componentValue;
                                            ?>
                                            <td>{{ number_format(round($componentValue)) }}</td>
                                        @endif
                                    @endforeach
                                    @foreach($components as $component)
                                        @if($component->type == 3 && $component->category == 0)
                                            <?php
                                                $componentKey = 'a_onetime_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($addition[$componentKey]) && $addition[$componentKey] !== '' ? $addition[$componentKey] : 0;
                                                $addSalary += $componentValue;
                                            ?>
                                            <td>{{ number_format(round($componentValue)) }}</td>
                                        @endif
                                    @endforeach
                                    <td>{{ isset($overtimePayment) ? $overtimePayment:'N/A' }}</td>
                                    <td>{{ $addSalary+(isset($overtimePayment) ? $overtimePayment:0) }}</td>
                                    
                                    
                                    <!--Deduction-->
                                    @php($dedSalary = 0)
                                    
                                    @php($componentValue = 0)
                                    @foreach($components as $component)
                                        @if($component->category == 1 && $component->type == 1)
                                            <?php
                                                $componentKey = 'd_compliance_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                
                                                $componentMin = isset($deduction[$componentKey . '_min']) ? $deduction[$componentKey . '_min']:'';
                                                $componentMax = isset($deduction[$componentKey . '_max']) ? $deduction[$componentKey . '_max']:'';
                                                
                                                // Calculation Type
                                                $componentCalc = isset($deduction['d_comp_calculation_' . str_replace(' ', '_', strtolower($component->title))]) ?  $deduction['d_comp_calculation_' . str_replace(' ', '_', strtolower($component->title))]:'';
                                                // Compliance Selected Components
                                                $componentCmps = isset($deduction['d_comp_compo_' . str_replace(' ', '_', strtolower($component->title))]) ?  $deduction['d_comp_compo_' . str_replace(' ', '_', strtolower($component->title))]:array();
                                                
                                                if(count($componentCmps) > 0){
                                                    if($componentCalc == "gross"){
                                                        $complianceSalary = 0;
                                                        $grossTotalSalary = 0;
                                                        foreach($componentCmps as $componentCm){
                                                            $componentCm = substr($componentCm, strlen("d_component"));
                                                            foreach ($addition as $key => $value) {
                                                                if (strpos($key, $componentCm) !== false) {
                                                                    $grossTotalSalary += $value;
                                                                }
                                                            }
                                                        }
                                                        
                                                        if($grossTotalSalary <= $componentMin){
                                                            $complianceSalary += $componentMin*($componentValue/100);
                                                        }else if($grossTotalSalary >= $componentMax){
                                                            $complianceSalary += $componentMax*($componentValue/100);
                                                        }else{
                                                            $complianceSalary += $grossTotalSalary*($componentValue/100);
                                                        }
                                                    }else{
                                                        $complianceSalary = 0;
                                                        $netTotalSalary = 0;
                                                        foreach($componentCmps as $componentCm){
                                                            $componentCm = substr($componentCm, strlen("d_component"));
                                                            foreach ($addition as $key => $value) {
                                                                if (strpos($key, '_variable') !== false) {
                                                                    if (strpos($key, $componentCm) !== false) {
                                                                        $netTotalSalary += ($value/$numberOfDays) * $payableDays;
                                                                    }
                                                                } else {
                                                                    if (strpos($key, $componentCm) !== false) {
                                                                        $netTotalSalary += $value;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        if($netTotalSalary <= $componentMin){
                                                            $complianceSalary += $componentMin*($componentValue/100);
                                                        }else if($netTotalSalary >= $componentMax){
                                                            $complianceSalary += $componentMax*($componentValue/100);
                                                        }else{
                                                            $complianceSalary += $netTotalSalary*($componentValue/100);
                                                        }
                                                    }
                                                }else{
                                                    $componentValue = 0;
                                                }
                                                
                                                $dedSalary += $complianceSalary;
                                            ?>
                                            <td>{{ number_format(round($complianceSalary)) }}</td>
                                        @endif
                                    @endforeach
                                    
                                    @php($componentValue = 0)
                                    @foreach($components as $component)
                                        @if($component->category == 1 && $component->type == 0)
                                            <?php
                                                $componentKey = 'd_variable_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                $dedSalary += $componentValue;
                                            ?>
                                            <td>{{ number_format(round($componentValue)) }}</td>
                                        @endif
                                    @endforeach
                                    
                                    @php($componentValue = 0)
                                    @foreach($components as $component)
                                        @if($component->category == 1 && $component->type == 2)
                                            <?php
                                                $componentKey = 'd_fixed_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                $dedSalary += $componentValue;
                                            ?>
                                            <td>{{ number_format(round($componentValue)) }}</td>
                                        @endif
                                    @endforeach

                                    @php($componentValue = 0)
                                    @foreach($components as $component)
                                        @if($component->category == 1 && $component->type == 3)
                                            <?php
                                                $componentKey = 'd_onetime_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                $dedSalary += $componentValue;
                                            ?>
                                            <td>{{ number_format(round($componentValue)) }}</td>
                                        @endif
                                    @endforeach
                                    <td>{{ number_format(round($dedSalary)) }}</td>
                                    
                                    
                                    @php($empCont = 0)
                                    @php($componentValue = 0)
                                    @foreach($components as $component)
                                        @if($component->category == 0 && $component->type == 1)
                                            <?php
                                                $componentKey = 'a_compliance_' . str_replace(' ', '_', strtolower($component->title));
                                                $componentValue = isset($addition[$componentKey]) && $addition[$componentKey] !== '' ? $addition[$componentKey] : 0;
                                                
                                                $componentMin = isset($addition[$componentKey . '_min']) ? $addition[$componentKey . '_min']:'';
                                                $componentMax = isset($addition[$componentKey . '_max']) ? $addition[$componentKey . '_max']:'';
                                                
                                                // Calculation Type
                                                $componentCalc = isset($addition[$componentKey . '_calculation']) ?  $addition[$componentKey . '_calculation']:'';
                                                // Compliance Selected Components
                                                $componentCmps = isset($addition[$componentKey . '_components']) ?  $addition[$componentKey . '_components']:array();
                                                
                                                if(count($componentCmps) > 0){
                                                    if($componentCalc == "gross"){
                                                        $complianceSalary = 0;
                                                        $grossTotalSalary = 0;
                                                        foreach($componentCmps as $componentCm){
                                                            $componentCm = substr($componentCm, strlen("a_component"));
                                                            foreach ($addition as $key => $value) {
                                                                if (strpos($key, $componentCm) !== false) {
                                                                    $grossTotalSalary += $value;
                                                                }
                                                            }
                                                        }
                                                        
                                                        if($grossTotalSalary <= $componentMin){
                                                            $complianceSalary += $componentMin*($componentValue/100);
                                                        }else if($grossTotalSalary >= $componentMax){
                                                            $complianceSalary += $componentMax*($componentValue/100);
                                                        }else{
                                                            $complianceSalary += $grossTotalSalary*($componentValue/100);
                                                        }
                                                    }else{
                                                        $complianceSalary = 0;
                                                        $netTotalSalary = 0;
                                                        foreach($componentCmps as $componentCm){
                                                            $componentCm = substr($componentCm, strlen("a_component"));
                                                            foreach ($addition as $key => $value) {
                                                                if (strpos($key, '_variable') !== false) {
                                                                    if (strpos($key, $componentCm) !== false) {
                                                                        $netTotalSalary += ($value/$numberOfDays) * $payableDays;
                                                                    }
                                                                } else {
                                                                    if (strpos($key, $componentCm) !== false) {
                                                                        $netTotalSalary += $value;
                                                                    }
                                                                }
                                                            }
                                                        }
    
                                                        if($netTotalSalary <= $componentMin){
                                                            $complianceSalary += $componentMin*($componentValue/100);
                                                        }else if($netTotalSalary >= $componentMax){
                                                            $complianceSalary += $componentMax*($componentValue/100);
                                                        }else{
                                                            $complianceSalary += $netTotalSalary*($componentValue/100);
                                                        }
                                                    }
                                                }else{
                                                    $componentValue = 0;
                                                }
                                                
                                                $empCont += $complianceSalary;
                                            ?>
                                            <td>{{ number_format(round($complianceSalary)) }}</td>
                                        @endif
                                    @endforeach
                                <td>{{ number_format(round($empCont)) }}</td>
                                
                                <td>{{ number_format(round(($netSalary+$addSalary)-$dedSalary)) }}</td>
                                <td>{{ number_format(round($netSalary+$addSalary+$empCont)) }}</td>
                                    
                                @endif
                                <td>{{ $payroll['ref_no'] }}</td>
                                <td>{{ $payroll['invoice_no'] }}</td>
                                <td>{{ $payroll['invoice_date'] }}</td>
                                <td>{{ $payroll['utr_no']}}</td>
                                <td>{{ $payroll['payment_date'] }}</td>
                                <td>{{ $payroll['remarks'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="payment_model" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">{{__('Payment Info')}}--- <span id="payment_month_year_show"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <span id="form_result"></span>
                        <form method="get" id="payment_form" class="form-horizontal" >
                           <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">{{__('Basic Salary')}}</label> &nbsp;&nbsp;&nbsp;&nbsp; <span id="payment_type_error"></span>
                                        <input type="text" name="basic_salary" id="basic_salary_payment" class="form-control" value="0" readonly="readonly">
                                        <input type="hidden" value="0" name="month_year" id="hidden_month_year">
                                        <input type="hidden" value="" name="employee_id" id="employee_id">
                                    </div>
                                </div>

                                   <div class="col-md-6 hide-element">
                                       <div class="form-group">
                                           <label for="worked_hours">{{__('Total Hours(This Month)')}}</label>
                                           <input type="text" readonly="readonly" name="worked_hours" id="worked_hours" class="form-control" value="0">
                                       </div>
                                   </div>

                                   <div class="col-md-6 hide-element">
                                       <div class="form-group">
                                           <label for="worked_amount">{{__('Amount')}}</label> <a href="#" data-toggle="popover" data-placement="top" data-content="If you don't set this month's amount, the last month's amount will be treated as this month"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i></a>
                                           <input type="text" readonly="readonly" name="worked_amount" id="worked_amount" class="form-control" value="0">
                                       </div>
                                   </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('Present Days')}}</label>
                                        <input type="text" name="present_days" id="total_worked_days" class="form-control" readonly="readonly">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('Total Allowance')}}</label> <a href="#" data-toggle="popover" data-placement="top" data-content="If you don't set this month's amount, the last month's amount will be treated as this month"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i></a>
                                        <input type="text" name="total_allowance" id="total_allowance_payment" class="form-control" value="0" readonly="readonly">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{trans('Commissions')}}</label> <a href="#" data-toggle="popover" data-placement="top" data-content="If you don't set this month's amount, 0 amount will be treated as this month"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i></a>
                                        <input type="text" name="total_commission" id="total_commission_payment" class="form-control" value="0" readonly="readonly">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('Total Overtime')}}</label> <a href="#" data-toggle="popover" data-placement="top" data-content="If you don't set this month's amount, 0 amount will be treated as this month"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i></a>
                                        <input type="text" name="total_overtime" id="total_overtime_payment" class="form-control" value="0" readonly="readonly">
                                    </div>
                                </div>

                                   <div class="col-md-6">
                                       <div class="form-group">
                                           <label for="name">{{__('Other Payment')}}</label> <a href="#" data-toggle="popover" data-placement="top" data-content="If you don't set this month's amount, 0 amount will be treated as this month"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i></a>
                                           <input type="text" name="total_other_payment" id="total_other_payment_payment" class="form-control" value="0" readonly="readonly">
                                       </div>
                                   </div>

                                   <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('Compliances/Deductions')}}</label> <a href="#" data-toggle="popover" data-placement="top" data-content="If you don't set this month's amount, the last month's amount will be treated as this month"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i></a>
                                        <input type="text" name="total_deduction" id="total_deduction_payment" class="form-control" value="0" readonly="readonly">
                                    </div>
                                </div>

                                   <div class="col-md-6">
                                       <div class="form-group">
                                           <label for="name">{{__('Monthly Payable')}}</label>
                                           <input type="text" name="monthly_payable" id="monthly_payable" class="form-control" value="0" readonly="readonly">
                                       </div>
                                   </div>

                                   <div class="col-md-6">
                                       <div class="form-group">
                                           <label for="name">{{__('Loan Remaining')}}</label>
                                           <input type="text" name="amount_remaining" id="amount_remaining" class="form-control" value="0" readonly="readonly">
                                       </div>
                                   </div>

                                   <div class="col-md-6">
                                       <div class="form-group">
                                           <label for="name">{{__('Pension Amount')}}</label>
                                           <input type="text" name="pension_amount" id="pension_amount_payment" class="form-control" value="0" readonly="readonly">
                                       </div>
                                   </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('Net Salary')}}</label>
                                        <input type="text" readonly="readonly" name="net_salary" id="net_salary_payment" class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{__('Payment Amount')}}</label>
                                        <input type="text" readonly="readonly" name="payment_amount" id="total_salary_payment" class="form-control" >
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <span><strong>{{trans('file.NOTE')}}:</strong> {{__('Total Allowance,Commissions,Total Loan,Total Overtime,Deductions,Other Payment, Pension are not editable.')}}</span>
                                    </div>
                                </div>

                                <div class="form-actions"> <button  type="submit" class="btn btn-primary"><i class="fa fa fa-check-square-o"></i> {{trans('file.Pay')}}</button></div>
                           </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script type="text/javascript">
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
    
    $('#sub_location_id').change(function () {
        let value = $('#sub_location_id').val();
        let _token = $('input[name="_token"]').val();
        
        $.ajax({
            url: "{{ route('dynamic_payroll_cycle') }}",
            method: "POST",
            data: {value: value, _token: _token},
            success: function (result) {
                $('#dynamic_payroll_cycle').val(result);
            }
        });
    });
    
    $(document).ready(function() {
        if ($('#client_grp_id').val() !== '') {
            let value = $('#client_grp_id').val();
            let dependent = $('#client_grp_id').data('dependent');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('dynamic_companies') }}",
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
                        url: "{{ route('dynamic_locations') }}",
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
                                url: "{{ route('dynamic_sub_locations') }}",
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
                                        url: "{{ route('dynamic_payroll_cycle') }}",
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
        
        $('#pay_list-table').DataTable({
            fixedHeader: {
                header: true,
                footer: true
            },
            dom: 'Bfrtip',
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
            order: [[1, 'asc']],
        });
    });
</script>
@endpush
