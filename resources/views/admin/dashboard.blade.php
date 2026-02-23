@extends('layouts.app')

@section('content')
    <style>
        .dashboard-card-link {
            text-decoration: none !important;
            display: block;
            transition: transform 0.2s ease-in-out;
        }

        .dashboard-card-link:hover {
            transform: translateY(-5px);
        }

        .dashboard-card-link .card {
            cursor: pointer;
            border: none;
            border-radius: 10px;
        }

        .view-all-link {
            font-size: 0.85rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            margin-top: auto;
        }

        .view-all-link:hover {
            color: #fff !important;
        }

        .view-all-link i {
            font-size: 1rem;
            margin-left: 5px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            min-height: 180px;
            padding: 1.5rem !important;
        }

        .card-title-custom {
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .card-count-custom {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .card-icon-custom {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2.5rem;
            opacity: 0.3;
        }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span>Overview</span> <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-3 stretch-card grid-margin">
            <a href="{{ route('categories.index') }}" class="dashboard-card-link w-100">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('admin_panel/dist/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="card-title-custom">Total Categories</h4>
                        <h2 class="card-count-custom">{{ \App\Models\Category::count() }}</h2>
                        <i class="mdi mdi-folder card-icon-custom"></i>
                        <span class="view-all-link">View All <i class="mdi mdi-arrow-right"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <a href="{{ route('subcategories.index') }}" class="dashboard-card-link w-100">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('admin_panel/dist/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="card-title-custom">Total Subcategories</h4>
                        <h2 class="card-count-custom">{{ \App\Models\SubCategory::count() }}</h2>
                        <i class="mdi mdi-folder-outline card-icon-custom"></i>
                        <span class="view-all-link">View All <i class="mdi mdi-arrow-right"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <a href="{{ route('child-categories.index') }}" class="dashboard-card-link w-100">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('admin_panel/dist/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="card-title-custom">Total Child Categories</h4>
                        <h2 class="card-count-custom">{{ \App\Models\ChildCategory::count() }}</h2>
                        <i class="mdi mdi-file-tree card-icon-custom"></i>
                        <span class="view-all-link">View All <i class="mdi mdi-arrow-right"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <a href="{{ route('items.index') }}" class="dashboard-card-link w-100">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('admin_panel/dist/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="card-title-custom">Total Items</h4>
                        <h2 class="card-count-custom">{{ \App\Models\Item::count() }}</h2>
                        <i class="mdi mdi-view-list card-icon-custom"></i>
                        <span class="view-all-link">View All <i class="mdi mdi-arrow-right"></i></span>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection