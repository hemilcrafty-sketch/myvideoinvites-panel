@inject('roleManager', 'App\Http\Controllers\Utils\RoleManager')
@inject('contentManager', '\App\Http\Controllers\Admin\Utils\ContentManager')
@inject('helperController', 'App\Http\Controllers\Utils\HelperController')
@include('layouts.masterhead')

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">

            <div class="pd-20 card-box mb-30">

                <!-- ================= USER INFO ================= -->
                <h5 class="mb-3">User Information</h5>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Picture</th>
                                <th>Email</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $userData['user']['name'] }}</td>

                                <td>
                                    @if(!empty($userData['user']['profile_pic']))
                                        <img src="{{ $userData['user']['profile_pic'] }}"
                                             style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                                    @else
                                        <div style="
                                            width:100px;
                                            height:100px;
                                            border-radius:50%;
                                            background:#2EC4B6;
                                            color:white;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            font-size:40px;
                                            font-weight:bold;">
                                            {{ strtoupper(substr($userData['user']['name'],0,1)) }}
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $userData['user']['email'] }}</td>
                                <td>{{ $userData['user']['contact_no'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <!-- ================= TABS SECTION ================= -->
                <div class="mt-5">

                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="subs-tab" data-toggle="tab"
                               href="#subs" role="tab">Subscription History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="drafts-tab" data-toggle="tab"
                               href="#drafts" role="tab">Drafts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="exports-tab" data-toggle="tab"
                               href="#exports" role="tab">Export History</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">

                        <!-- ================= SUBSCRIPTIONS ================= -->
                        <div class="tab-pane fade show active" id="subs" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Package</th>
                                            <th>Transaction ID</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Purchase</th>
                                            <th>Billing</th>
                                            <th>Validity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($userData['subsHistory']))
                                            @foreach ($userData['subsHistory'] as $sub)
                                                <tr>
                                                    <td>{{ $sub['package_name'] }}</td>
                                                    <td>{{ $sub['transaction_id'] }}</td>
                                                    <td>{{ $sub['amount'] }}</td>
                                                    <td>{{ $sub['method'] }}</td>
                                                    <td>{{ $sub['purchase_date'] }}</td>
                                                    <td>{{ $sub['billing_date'] }}</td>
                                                    <td>{{ $sub['validity'] }}</td>
                                                    <td>
                                                        <span class="badge"
                                                              style="background:{{ $sub['color']; }}; color: white">
                                                            {{ $sub['status'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    No Subscription History Found
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ================= DRAFTS ================= -->
                        <div class="tab-pane fade" id="drafts" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Thumbnail</th>
                                            <th>Draft ID</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($userData['drafts']))
                                            @foreach($userData['drafts'] as $index => $draft)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>

                                                    <td>
                                                        <img src="{{ $draft['thumb'] }}"
                                                             style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
                                                    </td>

                                                    <td>{{ $draft['id'] }}</td>

                                                    <td>{{ $draft['created_at'] }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No Drafts Found
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- ================= EXPORT HISTORY ================= -->
                        <div class="tab-pane fade" id="exports" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Draft ID</th>
                                            <th>Draft Thumb</th>
                                            <th>Export Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($userData['export']))
                                            @foreach($userData['export'] as $index => $export)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $export['draft_id'] }}</td>
                                                    <td>
                                                        <img src="{{ $export['thumb'] }}"
                                                             style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
                                                    </td>
                                                    <td>{{ $export['created_at'] }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No Export History Found
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('layouts.masterscript')
</body>
</html>
