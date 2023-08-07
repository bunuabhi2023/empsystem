<div class="row">    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type Of Leave</th>
                    <th>Probation Days</th>
                    <th>No. Of Allowed Leave</th>
                    <th>Leave Credited</th>
                    <th>Manual Adjustment</th>
                    <th>Advance Leave</th>
                    <th>Leave Taken</th>
                    <th>Leave Balance</th>
                    <th>Carry Forward Limit</th>
                    <th>Leave Refreshed On</th>
                    <th>Leave Deduction Method</th>
                    <th>Leave Credit Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leave_types as $leave_type)
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" name="leave_type" value="{{ $leave_type['leave_type'] }}"></td>
                        <td><input type="number" class="form-control form-control-sm" name="probation_days[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="allowed_leave[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="leave_credited[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="manual_adjustment[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="advance_leave[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="leave_taken[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="leave_balance[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="carry_forward_limit[]"></td>
                        <td>
                            <select class="selectpicker form-control form-control-sm" name="leave_refresh[]">
                                <option value="1">Daily</option>
                                <option value="30">Monthly</option>
                                <option value="90">Quarterly</option>
                                <option value="180">Half Yearly</option>
                                <option value="365">Yearly</option>
                            </select>
                        </td>
                        <td>
                            <select class="selectpicker form-control form-control-sm" name="leave_deduction[]">
                                <option value="1">Continue Leave</option>
                                <option value="0">Only Leave</option>
                            </select>
                        </td>
                        <td>
                            <select class="selectpicker form-control form-control-sm" name="leave_credit[]">
                                <option value="1">Daily</option>
                                <option value="30">Monthly</option>
                                <option value="90">Quarterly</option>
                                <option value="180">Half Yearly</option>
                                <option value="365">Yearly</option>
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>