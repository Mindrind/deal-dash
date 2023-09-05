@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Users')}}</h1>
        </div>
        <div class="col text-right">
            <!--- {{ route('auction_products.create') }}    -->
            <a href="{{ route('bot_user_create.admin') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Bot User')}}</span>
            </a>
        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Users') }}</h5>
            </div>

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{translate('Name')}}</th>
                        <!-- <th data-breakpoints="sm">{{translate('Email')}}</th> -->
                        <!-- <th data-breakpoints="sm">{{translate('Location')}}</th> -->
                        <!-- <th data-breakpoints="sm">{{translate('Product')}}</th> -->
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr>
                        <td>{{ ($key+1) + ($users->currentPage() - 1)*$users->perPage() }}</td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                <img src="https://cdn.onlinewebfonts.com/svg/img_508630.png" class="rounded-circle" height="20" width="20" alt="Default User Image" />
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $user->name }}</span>
                                </div>
                            </div>
                        </td>
                        <!-- <td>{{ $user->email }}</td> -->
                        <!-- <td>{{ $user->location }}</td> -->
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('bot_users.admin.edit', ['id'=>$user->id])}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" value="{{$user->id}}"  title="{{ translate('Delete') }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" onclick="getId({{$user->id}})">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $users->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function sort_products(el){
            $('#sort_products').submit();
        }

        
        
        var clickedId = null;
        function getId(id){
            clickedId = id;
        }
        $(document).ready(function(){
          
            $("#delete-link").click(function(e){
                e.preventDefault();
                window.location.href = "/admin/bot_users/destroy/" + clickedId;
            });
        })

    </script>
@endsection
