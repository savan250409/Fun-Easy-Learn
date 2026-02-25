@extends('layouts.app')

@section('styles')
    <style>
        .api-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
        }

        .api-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .api-method {
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .api-method-get {
            color: #2bd7ab;
            font-weight: 600;
        }

        .api-method-post {
            color: #b66df8;
            font-weight: 600;
        }

        .api-url-box {
            background: #f8f9fa;
            padding: 0.8rem 1rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.85rem;
            color: #444;
            word-break: break-all;
            margin-bottom: 0.5rem;
        }

        .api-label {
            font-size: 0.85rem;
            color: #b66df8;
            margin-bottom: 0.2rem;
            font-weight: 600;
        }

        .api-description {
            font-size: 0.85rem;
            color: #555;
            margin-top: 1rem;
            line-height: 1.5;
        }

        .api-params {
            font-family: monospace;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .api-params span {
            color: #ff7b94;
        }

        .module-badge {
            background-color: #2bd7ab;
            color: white;
            padding: 0.4rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 2rem;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="page-title-custom mb-0">API Documentation</h3>
            </div>

            <div class="text-center">
                <div class="module-badge">Fun Easy Learn Module</div>
            </div>

            <div class="row">
                <!-- API 1 -->
                <div class="col-md-6">
                    <div class="api-card">
                        <div class="api-title">1. Get All Categories</div>
                        <div class="api-method">Method: <span class="api-method-get">GET</span></div>

                        <div class="api-label">URL:</div>
                        <div class="api-url-box">
                            {{ url('/api/get-all-categories') }}
                        </div>

                        <div class="api-description">
                            <strong>Description:</strong><br>
                            Retrieves a list of all active main categories.
                        </div>
                    </div>
                </div>

                <!-- API 2 -->
                <div class="col-md-6">
                    <div class="api-card">
                        <div class="api-title">2. Get Category Data</div>
                        <div class="api-method">Method: <span class="api-method-post">POST</span></div>

                        <div class="api-label">URL:</div>
                        <div class="api-url-box">
                            {{ url('/api/get-category-data') }}
                        </div>

                        <div class="api-description">
                            <div class="api-label mt-3 mb-1">Parameters:</div>
                            <div class="api-params">
                                <span>category_id</span> (required) e.g. 1
                            </div>

                            <div class="mt-3">
                                <strong>Description:</strong><br>
                                Retrieves all active nested data (subcategories, child categories, and items) for a specific
                                main category based on the provided category_id parameter.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection