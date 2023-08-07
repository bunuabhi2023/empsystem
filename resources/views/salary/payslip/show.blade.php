@extends('layout.main')
@section('content')


    <section>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-2">
                        <div class="card-header d-flex justify-content-between">
                            <h2 class="card-title">{{__('Payslip')}} <span class="text-grey text-small"></span>
                            </h2>
                            <div class="pull-right"><a href="#" onclick="printDiv('print_content')"
                                                       class="btn btn-default btn-sm" data-toggle="tooltip"
                                                       data-placement="top" title=""
                                                       data-original-title="Download Payslip"><i
                                            class="fa fa-print"></i></a></div>
                        </div>
                        <div class="card-body" id="print_content">
                            <div class="text-center">
                                <h3><strong>Investation Team Private Limited</strong></h3>
                                <h4>New Delhi - 110002</h4>
                                <h4>Pay Slip for April 2023</h4>
                            </div>
                            <div class="mt-5">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>Employee ID</td>
                                            <td>{{ $employee->staff_id }}</td>
                                            <td>UAN</td>
                                            <td>{{ $employee->uan != "" ? $employee->uan:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Employee Name</td>
                                            <td>{{ $employee->first_name }}</td>
                                            <td>PF No</td>
                                            <td>{{ $employee->pf_no != "" ? $employee->pf_no:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Designation</td>
                                            <td>{{ $employee->designation->designation_name }}</td>
                                            <td>ESIC No</td>
                                            <td>{{ $employee->esic_no != "" ? $employee->esic_no:'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Department</td>
                                            <td>{{ $employee->department->department_name }}</td>
                                            <td>Leave Taken</td>
                                            <td>{{ $employee->total_leave-$employee->remaining_leave }}</td>
                                        </tr>
                                        <tr>
                                            <td>DOJ</td>
                                            <td>{{ $employee->joining_date }}</td>
                                            <td>Leave Available</td>
                                            <td>{{ $employee->remaining_leave }}</td>
                                        </tr>
                                        <tr>
                                            <td>Location</td>
                                            <td>{{ $employee->location->location_name }}</td>
                                            <td>Total Working Days</td>
                                            <td>{{ $workingDays }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bank Name</td>
                                            <td>{{ $employee->bankAccounts[0]->account_title }}</td>
                                            <td>Total Paid Days</td>
                                            <td>{{ $salary->payable_days }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bank A/C No</td>
                                            <td>{{ $employee->bankAccounts[0]->account_number }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td colspan="2" class="text-center text-black">Addition</td>
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                                
                                                <?php
                                                    $totalDays += 1;
                                                    $payableDays = $salary->payable_days;
                                                    
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
                                                    
                                                    $netSalary += $overtimePayment; 
                                                ?>
                                                
                                                <?php $dedSalary = 0; ?>
                                                
                                                @foreach($components as $component)
                                                    <tr>
                                                        @if($component->type == 0 && $component->category == 0)
                                                            <td>{{ $component->title }}</td>
                                                            <?php
                                                                $componentValue = isset($addition) && isset($addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))]) ? ($addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))] / $totalDays) * $payableDays : 0;
                                                                $netSalary += $componentValue;
                                                            ?>
                                                            <td>{{ number_format(round($componentValue)) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                
                                                @foreach($components->sortBy('type') as $component)
                                                    <tr>
                                                        @if($component->type == 2 && $component->category == 0)
                                                            <td>{{ $component->title }}</td>
                                                            <?php
                                                                $componentKey = 'a_fixed_' . str_replace(' ', '_', strtolower($component->title));
                                                                $componentValue = isset($addition[$componentKey]) && $addition[$componentKey] !== '' ? $addition[$componentKey] : 0;
                                                                $netSalary += $componentValue;
                                                            ?>
                                                            <td>{{ number_format(round($componentValue)) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                
                                                @foreach($components->sortBy('type') as $component)
                                                    <tr>
                                                        @if($component->type == 3 && $component->category == 0)
                                                            <td>{{ $component->title }}</td>
                                                            <?php
                                                                $componentKey = 'a_onetime_' . str_replace(' ', '_', strtolower($component->title));
                                                                $componentValue = isset($addition[$componentKey]) && $addition[$componentKey] !== '' ? $addition[$componentKey] : 0;
                                                                $netSalary += $componentValue;
                                                            ?>
                                                            <td>{{ number_format(round($componentValue)) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                
                                                <tr>
                                                    <td>{{trans('Overtime')}}</td>
                                                    <td>{{ isset($overtimePayment) ? $overtimePayment:'N/A' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-6">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td colspan="2" class="text-center text-black">Deduction</td>
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                                ?>
                                                
                                                <?php $dedSalary = 0; ?>
                                                
                                                @foreach($components as $component)
                                                    <tr>
                                                        @if($component->category == 1 && $component->type == 1)
                                                            <td>{{ $component->title }}</td>
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
                                                                                        $netTotalSalary += ($value/$totalDays) * $payableDays;
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
                                                    </tr>
                                                @endforeach
                                                
                                                @foreach($components->sortBy('type') as $component)
                                                    <tr>
                                                       @if($component->category == 1 && $component->type == 0)
                                                            <td>{{ $component->title }}</td>
                                                            <?php
                                                                $componentKey = 'd_variable_' . str_replace(' ', '_', strtolower($component->title));
                                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                                $dedSalary += $componentValue;
                                                            ?>
                                                            <td>{{ number_format(round($componentValue)) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                
                                                @foreach($components->sortBy('type') as $component)
                                                    <tr>
                                                        @if($component->category == 1 && $component->type == 2)
                                                            <td>{{ $component->title }}</td>
                                                            <?php
                                                                $componentKey = 'd_fixed_' . str_replace(' ', '_', strtolower($component->title));
                                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                                $dedSalary += $componentValue;
                                                            ?>
                                                            <td>{{ number_format(round($componentValue)) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                
                                                @foreach($components->sortBy('type') as $component)
                                                    <tr>
                                                        @if($component->category == 1 && $component->type == 3)
                                                            <td>{{ $component->title }}</td>
                                                            <?php
                                                                $componentKey = 'd_onetime_' . str_replace(' ', '_', strtolower($component->title));
                                                                $componentValue = isset($deduction[$componentKey]) && $deduction[$componentKey] !== '' ? $deduction[$componentKey] : 0;
                                                                $dedSalary += $componentValue;
                                                            ?>
                                                            <td>{{ number_format(round($componentValue)) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-12">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td>Total Earnings</td>
                                                    <td>{{ number_format(round($netSalary)) }}</td>
                                                    <td>Total Deductions</td>
                                                    <td>{{ number_format(round($dedSalary)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Net Salary: 
                                                        <?php
                                                            $netSalary = round($netSalary-$dedSalary);
                                                            $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                                                            echo ucfirst($f->format($netSalary));
                                                        ?>
                                                    </td>
                                                    <td>{{ number_format(round($netSalary)) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <p>This is system generated payslip, Hense signature does not required.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    
    <script>
        function printDiv(divName) {
             var printContents = document.getElementById(divName).innerHTML;
             var originalContents = document.body.innerHTML;
        
             document.body.innerHTML = printContents;
        
             window.print();
        
             document.body.innerHTML = originalContents;
        }
    </script>
@endsection
