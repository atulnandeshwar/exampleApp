@extends('admin.app')

@section('content')
<div class="container">
    <div class="rwo">
        <div class="col-md-4">
            <ul>
                <li>
                    <a class="" href="{{ route('admin-customers') }}"
                                      >
                                        {{ __('Customers') }}
                    </a>
                </li>
                <li><a class="" href="{{ route('admin-vendors') }}"
                                      >
                                        {{ __('Vendors') }}
                    </a></li>
            </ul>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    {{ __('You are in admin dashboard') }}
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection