<style>
    .salary_details_container *{
        color: #7C5CC4;
    }
</style>

<div class="row salary_details_container">
    <div class="col-md-12 mb-3">
        <div style="display: flex;align-items: center;gap: 20px;flex-wrap: wrap">
            <button class="btn btn-primary btn-sm" id="addition_table_btn" type="button">Show Addition Table</button>
            <button class="btn btn-primary btn-sm" id="deduction_table_btn" type="button">Show Deduction Table</button>
            <button class="btn btn-primary btn-sm" id="overtime_table_btn" type="button">Show Overtime Attendance Table</button>
            <button class="btn btn-primary btn-sm" id="adeduction_table_btn" type="button">Show Deduction Attendance Table</button>
        </div>
    </div>
</div>

<form method="post" action="{{ route('employees_basicSalary.store', ['employee'=> $employee->id]) }}">
@csrf
<div class="row salary_details_container" id="addition_salary_table" style="display: none;">
    <div class="col-md-12 text-center mb-3">
        <h4>Addition</h4>
    </div>
    @php $addition = isset(json_decode($salary)[0]) ? json_decode($salary)[0]->addition:'';
         $deduction = isset(json_decode($salary)[0]) ? json_decode($salary)[0]->deduction:'';
         $addition = json_decode($addition, true);
         $deduction = json_decode($deduction, true);
         
         $adeduction = isset(json_decode($salary)[0]) ? json_decode($salary)[0]->adeduction_settings:'';
         $adeduction = json_decode($adeduction, true);
         
         $overtime = isset(json_decode($salary)[0]) ? json_decode($salary)[0]->overtime_settings:'';
         $overtime = json_decode($overtime, true);
    @endphp
    <div class="col-md-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">Variable</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 0 && $component->category == 0)
                        <tr>
                            <td>{{ $component->title }}</td>
                            @php($componentValue = isset($addition) && isset($addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))]) ? $addition['a_variable_' . str_replace(' ', '_', strtolower($component->title))]:'')
                            <td><input type="text" class="form-control form-control-sm a_variable" onkeyup="calculateTotal('.a_variable', '#a_variable_total')" id="a_component_{{ str_replace(' ', '_', strtolower($component->title)) }}" value="{{ $componentValue }}" placeholder="Enter Value" name="a_variable_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <td>Total: </td>
                <td><input type="number" placeholder="Auto Calculate" id="a_variable_total" name="a_variable_total" value="{{ isset($addition['a_variable_total']) ? $addition['a_variable_total']:'' }}" class="totals form-control form-control-sm" readonly /></td>
            </tfoot>
        </table>
    </div>
    <div class="col-md-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">Fixed</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 2 && $component->category == 0)
                        <tr>
                            <td>{{ $component->title }}</td>
                            @php($componentValue = isset($addition) && isset($addition['a_fixed_' . str_replace(' ', '_', strtolower($component->title))]) ? $addition['a_fixed_' . str_replace(' ', '_', strtolower($component->title))]:'')
                            <td><input type="number" class="form-control form-control-sm a_fixed" onkeyup="calculateTotal('.a_fixed', '#a_fixed_total')" id="a_component_{{ str_replace(' ', '_', strtolower($component->title)) }}" value="{{ $componentValue }}" placeholder="Enter Value" name="a_fixed_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <td>Total: </td>
                <td><input type="number" placeholder="Auto Calculate" id="a_fixed_total" name="a_fixed_total" value="{{ isset($addition['a_fixed_total']) ? $addition['a_fixed_total']:'' }}" class="totals form-control form-control-sm" readonly /></td>
            </tfoot>
        </table>
    </div>
    <div class="col-md-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">One Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 3 && $component->category == 0)
                        <tr>
                            <td>{{ $component->title }}</td>
                            @php($componentValue = isset($addition) && isset($addition['a_onetime_' . str_replace(' ', '_', strtolower($component->title))]) ? $addition['a_onetime_' . str_replace(' ', '_', strtolower($component->title))]:'')
                            <td><input type="number" class="form-control form-control-sm a_onetime" onkeyup="calculateTotal('.a_onetime', '#a_onetime_total')" id="a_component_{{ str_replace(' ', '_', strtolower($component->title)) }}" value="{{ $componentValue }}" placeholder="Enter Value" name="a_onetime_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <td>Total: </td>
                <td><input type="number" placeholder="Auto Calculate" id="a_onetime_total" name="a_onetime_total" value="{{ isset($addition['a_onetime_total']) ? $addition['a_onetime_total']:'' }}" class="totals form-control form-control-sm" readonly /></td>
            </tfoot>
        </table>
    </div>
    <div class="col-md-12 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">Compliances</th>
                    <th colspan="2">Limits</th>
                    <th>Components</th>
                    <th>Type Of Calculation</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 1 && $component->category == 0)
                        <?php 
                            $componentValue = isset($addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title))]) ? $addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title))]:'';
                            $componentMin = isset($addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_min']) ? $addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_min']:'';
                            $componentMax = isset($addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_max']) ? $addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_max']:'';
                            $componentCmps = isset($addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_components']) ?  $addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_components']:array();
                            $componentCalc = isset($addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_calculation']) ?  $addition['a_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_calculation']:'';
                        ?>
                        <tr>
                            <td>{{ $component->title }}</td>
                            <td><input type="number" class="form-control form-control-sm a_compliance" onkeyup="calculateCompliances('#a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}', '#a_compliances_total', 'a')" name="a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}" placeholder="Enter Rate (in %)" name="a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}" value="{{ $componentValue }}"></td>
                            <td><input type="number" value="{{ $componentMin }}" class="form-control form-control-sm" onkeyup="calculateCompliances('#a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}', '#a_compliances_total', 'a')" placeholder="Enter Min Limit" name="a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}_min"></td>
                            <td><input type="number" value="{{ $componentMax }}" class="form-control form-control-sm" onkeyup="calculateCompliances('#a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}', '#a_compliances_total', 'a')" placeholder="Enter Max Limit" name="a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}_max"></td>
                            <td>
                                <select class="form-control form-control-sm selectpicker" onclick="calculateCompliances('#a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}', '#a_compliances_total', 'a')"
                                        data-live-search="true"  name="a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}_components[]"
                                        data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>__('Components')])}}..." multiple>
                                    @foreach ($components as $componentS)
                                        @if ($componentS->category == 0 && $componentS->type != 1)
                                            <?php
                                                $optionValue = 'a_component_' . strtolower($componentS->title);
                                                $isSelected = in_array($optionValue, $componentCmps) ? 'selected' : '';
                                            ?>
                                            <option value="a_component_{{ strtolower($componentS->title) }}" {{ $isSelected }}>{{ $componentS->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control form-control-sm selectpicker" onclick="calculateCompliances('#a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}', '#a_compliances_total', 'a')"
                                        data-live-search="true" name="a_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}_calculation"
                                        data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>__('Calculation')])}}...">
                                    <option value="gross" {{ $componentCalc == "gross" ? 'selected':'' }}>Gross Salary</option>
                                    <option value="net" {{ $componentCalc == "net" ? 'selected':'' }}>Net Salary</option>
                                </select>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-5 salary_details_container" id="deduction_salary_table" style="display: none">
    <div class="col-md-12 text-center mb-3">
        <h4>Deduction</h4>
    </div>
    <div class="col-md-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">Variable</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 0 && $component->category == 1)
                        <tr>
                            <td>{{ $component->title }}</td>
                            @php($componentValue = isset($deduction['d_variable_' . str_replace(' ', '_', strtolower($component->title))]) ? $deduction['d_variable_' . str_replace(' ', '_', strtolower($component->title))]:'')
                            <td><input type="number" class="form-control form-control-sm d_variable" onkeyup="calculateTotal('.d_variable', '#d_variable_total')" id="d_component_{{ str_replace(' ', '_', strtolower($component->title)) }}" value="{{ $componentValue }}" placeholder="Enter Value" name="d_variable_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <td>Total: </td>
                <td><input type="number" placeholder="Auto Calculate" id="d_variable_total" name="d_variable_total" value="{{ isset($deduction['d_variable_total']) ? $deduction['d_variable_total']:'' }}" class="form-control form-control-sm" readonly /></td>
            </tfoot>
        </table>
    </div>
    <div class="col-md-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">Fixed</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 2 && $component->category == 1)
                        <tr>
                            <td>{{ $component->title }}</td>
                            @php($componentValue = isset($deduction['d_fixed_' . str_replace(' ', '_', strtolower($component->title))]) ? $deduction['d_fixed_' . str_replace(' ', '_', strtolower($component->title))]:'')
                            <td><input type="number" class="form-control form-control-sm d_fixed" onkeyup="calculateTotal('.d_fixed', '#d_fixed_total')" id="d_component_{{ str_replace(' ', '_', strtolower($component->title)) }}" value="{{ $componentValue }}" placeholder="Enter Value" name="d_fixed_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <td>Total: </td>
                <td><input type="number" placeholder="Auto Calculate" id="d_fixed_total" name="d_fixed_total" value="{{ isset($deduction['d_fixed_total']) ? $deduction['d_fixed_total']:'' }}" class="form-control form-control-sm" readonly /></td>
            </tfoot>
        </table>
    </div>
    <div class="col-md-4 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">One Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 3 && $component->category == 1)
                        <tr>
                            <td>{{ $component->title }}</td>
                            @php($componentValue = isset($deduction['d_onetime_' . str_replace(' ', '_', strtolower($component->title))]) ? $deduction['d_onetime_' . str_replace(' ', '_', strtolower($component->title))]:'')
                            <td><input type="number" class="form-control form-control-sm d_onetime" id="d_component_{{ str_replace(' ', '_', strtolower($component->title)) }}" onkeyup="calculateTotal('.d_onetime', '#d_onetime_total')" value="{{ $componentValue }}" placeholder="Enter Value" name="d_onetime_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <td>Total: </td>
                <td><input type="number" placeholder="Auto Calculate" id="d_onetime_total" name="d_onetime_total" value="{{ isset($deduction['d_onetime_total']) ? $deduction['d_onetime_total']:'' }}" class="form-control form-control-sm" readonly /></td>
            </tfoot>
        </table>
    </div>
    <div class="col-md-12 text-center">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2">Compliances</th>
                    <th colspan="2">Limits</th>
                    <th>Components</th>
                    <th>Type Of Calculation</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $component)
                    @if ($component->type === 1 && $component->category == 1)
                        <?php 
                            $componentValue = isset($deduction['d_compliance_' . str_replace(' ', '_', strtolower($component->title))]) ? $deduction['d_compliance_' . str_replace(' ', '_', strtolower($component->title))]:'';
                            $componentMin = isset($deduction['d_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_min']) ? $deduction['d_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_min']:'';
                            $componentMax = isset($deduction['d_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_max']) ? $deduction['d_compliance_' . str_replace(' ', '_', strtolower($component->title)) . '_max']:'';
                            $componentCmps = isset($deduction['d_comp_compo_' . str_replace(' ', '_', strtolower($component->title))]) ? $deduction['d_comp_compo_' . str_replace(' ', '_', strtolower($component->title))]:array();
                            $componentCalc = isset($deduction['d_comp_calculation_' . str_replace(' ', '_', strtolower($component->title))]) ? $deduction['d_comp_calculation_' . str_replace(' ', '_', strtolower($component->title))]:'';
                        ?>
                        <tr>
                            <td>{{ $component->title }}</td>
                            <td><input type="number" class="form-control form-control-sm d_compliance" id="d_component_{{ str_replace(' ', '_', strtolower($component->title)) }}"  value="{{ $componentValue }}" placeholder="Enter Rate (in %)" name="d_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}"></td>
                            <td><input type="number" value="{{ $componentMin }}" class="form-control form-control-sm" placeholder="Enter Min Limit" name="d_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}_min"></td>
                            <td><input type="number" value="{{ $componentMax }}" class="form-control form-control-sm" placeholder="Enter Max Limit" name="d_compliance_{{ str_replace(' ', '_', strtolower($component->title)) }}_max"></td>
                            <td>
                                <select class="form-control form-control-sm selectpicker" name="d_comp_compo_{{ str_replace(' ', '_', strtolower($component->title)) }}[]"
                                        data-live-search="true"
                                        data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>__('Components')])}}..." multiple>
                                    @foreach ($components as $componentS)
                                        @if ($componentS->category == 0 && $componentS->type != 1)
                                            <?php
                                                $optionValue = 'd_component_' . strtolower($componentS->title);
                                                $isSelected = in_array($optionValue, $componentCmps) ? 'selected' : '';
                                            ?>
                                            <option value="d_component_{{ strtolower($componentS->title) }}" {{ $isSelected }}>{{ $componentS->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control form-control-sm selectpicker"
                                        data-live-search="true" name="d_comp_calculation_{{ str_replace(' ', '_', strtolower($component->title)) }}"
                                        data-live-search-style="contains"
                                        title="{{__('Selecting',['key'=>__('Calculation')])}}...">
                                    <option value="gross" {{ $componentCalc == "gross" ? 'selected':'' }}>Gross Salary</option>
                                    <option value="net" {{ $componentCalc == "net" ? 'selected':'' }}>Net Salary</option>
                                </select>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row mt-5" id="save_btn">
    <div class="col-md-12 text-center">
        <button type="submit" name="submit_salary" class="btn btn-warning text-black">Save</button>
    </div>
</div>
</form>

<style>
    #adeduction_table td, #adeduction_table th, #adeduction_table tr{
        border: 1px solid !important;
    }
</style>


<?php
// Function to generate input fields with data
function generateInputFields($data, $index) {
    $punch_in_name = isset($data['punch_in'][$index]['name']) ? $data['punch_in'][$index]['name'] : 'Limit '.$index+1;
    $punch_in_min_value = isset($data['punch_in'][$index]['min_value']) ? $data['punch_in'][$index]['min_value'] : '';
    $punch_in_max_value = isset($data['punch_in'][$index]['max_value']) ? $data['punch_in'][$index]['max_value'] : '';
    $punch_in_allowed = isset($data['punch_in'][$index]['allowed']) ? $data['punch_in'][$index]['allowed'] : '';
    $punch_in_payable = isset($data['punch_in'][$index]['payable']) ? $data['punch_in'][$index]['payable'] : '';

    $punch_out_min_value = isset($data['punch_out'][$index]['min_value']) ? $data['punch_out'][$index]['min_value'] : '';
    $punch_out_max_value = isset($data['punch_out'][$index]['max_value']) ? $data['punch_out'][$index]['max_value'] : '';
    $punch_out_allowed = isset($data['punch_out'][$index]['allowed']) ? $data['punch_out'][$index]['allowed'] : '';
    $punch_out_payable = isset($data['punch_out'][$index]['payable']) ? $data['punch_out'][$index]['payable'] : '';

    return '
    <tr>
        <!--Punch In-->
        <td><input type="text" name="punch_in_name[]" class="form-control form-control-sm" value="' . $punch_in_name . '" placeholder="Enter value."></td>
        <td><input type="number" name="punch_in_min_value[]" class="form-control form-control-sm" value="' . $punch_in_min_value . '" placeholder="Enter value."></td>
        <td><input type="number" name="punch_in_max_value[]" class="form-control form-control-sm" value="' . $punch_in_max_value . '" placeholder="Enter value."></td>
        <td><input type="number" name="punch_in_allowed[]" class="form-control form-control-sm" value="' . $punch_in_allowed . '" placeholder="Enter value."></td>
        <td><input type="text" name="punch_in_payable[]" class="form-control form-control-sm" value="' . $punch_in_payable . '" placeholder="Enter value."></td>

        <!--Punch Out-->
        <td><input type="number" name="punch_out_min_value[]" class="form-control form-control-sm" value="' . $punch_out_min_value . '" placeholder="Enter value."></td>
        <td><input type="number" name="punch_out_max_value[]" class="form-control form-control-sm" value="' . $punch_out_max_value . '" placeholder="Enter value."></td>
        <td><input type="number" name="punch_out_allowed[]" class="form-control form-control-sm" value="' . $punch_out_allowed . '" placeholder="Enter value."></td>
        <td><input type="text" name="punch_out_payable[]" class="form-control form-control-sm" value="' . $punch_out_payable . '" placeholder="Enter value."></td>
    </tr>';
}
?>

<div class="row mt-5 salary_details_container" id="adeduction_table" style="display: none">
    <div class="col-md-12 text-center mb-3">
        <h4>Attendance Deduction</h4>
    </div>
    <form method="post" action="{{ route('employees_attendanceD.store', ['employee'=> $employee->id]) }}">
    @csrf
    <div class="col-md-12 text-center">
        <table class="table table-bordered table-responsive" id="deduction_table_a">
            <thead>
                <tr>
                    <th colspan="5">Punch In</th>
                    <th colspan="4">Punch Out</th>
                </tr>
                <tr>
                    <!--Punch In-->
                    <th>Limits</th>
                    <th>Min. Minutes</th>
                    <th>Max. Minutes</th>
                    <th>Allowed</th>
                    <th>Payable</th>
                    
                    <!--Punch Out-->
                    <th>Min. Minutes</th>
                    <th>Max. Minutes</th>
                    <th>Allowed</th>
                    <th>Payable</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if the array is empty
                if (empty($adeduction['punch_in']) && empty($adeduction['punch_out'])) {
                    // Display a single row of blank inputs
                    for ($i = 0; $i < 16; $i++) {
                        echo generateInputFields($adeduction, $i);
                    }
                } else {
                    // Get the maximum number of elements in either 'punch_in' or 'punch_out'
                    $max_count = max(count($adeduction['punch_in']), count($adeduction['punch_out']));
            
                    // Loop through each set of data and generate the input fields
                    for ($i = 0; $i < $max_count; $i++) {
                        echo generateInputFields($adeduction, $i);
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-warning text-black">Save</button>
    </div>
    </form>
</div>

<div class="row mt-5 salary_details_container" id="overtime_table" style="display: none">
    <div class="col-md-12 mb-3">
        <h4>Overtime</h4>
    </div>
    <form method="post" action="{{ route('employees_overtime.store', ['employee'=> $employee->id]) }}">
    @csrf
    <div class="col-md-12 text-center">
        <div class="form-group">
            <select class="form-control selectpicker" name="calculation_type" onchange="if($(this).val() == '0'){$('#rate_overtime_inp').show()}else{$('#rate_overtime_inp').hide()}">
                <option value="0" <?= isset($overtime['calculation_type']) && $overtime['calculation_type'] == "0" ? 'selected':'' ?>>Per Minute</option>
                <option value="1" <?= isset($overtime['calculation_type']) && $overtime['calculation_type'] == "1" ? 'selected':'' ?>>Per Day</option>
            </select>
        </div>
    </div>
    <div class="col-md-12 text-center" id="rate_overtime_inp" style="display: <?php if(isset($overtime['calculation_type']) && $overtime['calculation_type'] == "0"){echo 'block';}else if(isset($overtime['calculation_type']) && $overtime['calculation_type'] == "1"){echo 'none';}else if(!isset($overtime['calculation_type'])){echo 'block';} ?>">
        <div class="form-group">
            <input type="number" class="form-control" value="<?= isset($overtime['rate']) ? $overtime['rate']:'' ?>" name="rate" placeholder="Rate (in Rs.)">
        </div>
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-warning text-black">Save</button>
    </div>
    </form>
</div>

<script>
    $(document).ready(function() {
      $("#addition_table_btn").click(function() {
          if($("#addition_salary_table").css("display") == "none"){
            $("#addition_salary_table").fadeIn(100);
            
            $("#deduction_salary_table").fadeOut(100);
            $("#adeduction_table").fadeOut(100);
            $("#overtime_table").fadeOut(100);
            
            $("#save_btn").fadeIn(100);
          }else{
            $("#addition_salary_table").fadeOut(100);
          }
      });
      
      $("#deduction_table_btn").click(function() {
          if($("#deduction_salary_table").css("display") == "none"){
            $("#deduction_salary_table").fadeIn(100);
            
            $("#save_btn").fadeIn(100);
            
            $("#addition_salary_table").fadeOut(100);
            $("#adeduction_table").fadeOut(100);
            $("#overtime_table").fadeOut(100);
          }else{
            $("#deduction_salary_table").fadeOut(100);
          }
      });
      
      $("#adeduction_table_btn").click(function() {
          if($("#adeduction_table").css("display") == "none"){
            $("#adeduction_table").fadeIn(100);
            
            $("#save_btn").fadeOut(100);
            
            $("#overtime_table").fadeOut(100);
            $("#addition_salary_table").fadeOut(100);
            $("#deduction_salary_table").fadeOut(100);
          }else{
            $("#adeduction_table").fadeOut(100);
          }
      });
      
      $("#overtime_table_btn").click(function() {
          if($("#overtime_table").css("display") == "none"){
            $("#overtime_table").fadeIn(100);

            $("#save_btn").fadeOut(100);

            $("#adeduction_table").fadeOut(100);
            $("#addition_salary_table").fadeOut(100);
            $("#deduction_salary_table").fadeOut(100);
          }else{
            $("#overtime_table").fadeOut(100);
          }
      });
    });
    
    
    function calculateTotal(className, totalId) {
      var total = 0;
      $(className).each(function() {
        var value = parseFloat($(this).val());
        if (!isNaN(value)) {
          total += value;
        }
      });
    
      $(totalId).val(total);
    }
    
    function calcNetW(){
        var table = document.getElementById("deduction_table_a");
        var rows = table.getElementsByTagName("tr");
        
        // Skip the first row (header row)
        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            var cells = row.getElementsByTagName("td");
        
            // Extract the input values from the current row
            var earlyComing = Number(cells[1].querySelector("input").value);
            var lateGoing = Number(cells[4].querySelector("input").value);
            var earlyGoing = Number(cells[3].querySelector("input").value);
            var lateComing = Number(cells[2].querySelector("input").value);
        
            // Perform the calculation
            var result = earlyComing + lateGoing - earlyGoing - lateComing;
            cells[5].querySelector("input").value = result
            console.log(result)
        }
    }
</script>