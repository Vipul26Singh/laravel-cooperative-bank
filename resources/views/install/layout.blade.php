<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install — CoopBank ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .install-card { max-width: 640px; width: 100%; }
        .step-bar { display: flex; gap: 4px; margin-bottom: 24px; }
        .step-bar .step { flex: 1; height: 6px; border-radius: 3px; background: #dee2e6; }
        .step-bar .step.active { background: #1a73e8; }
        .step-bar .step.done { background: #00b894; }
    </style>
</head>
<body>
<div class="container">
    <div class="install-card mx-auto">
        <div class="text-center mb-4">
            <h2><i class="fas fa-university text-primary"></i> CoopBank ERP</h2>
            <p class="text-muted">Installation Wizard</p>
        </div>
        <div class="step-bar">
            <div class="step {{ $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' }}"></div>
            <div class="step {{ $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' }}"></div>
            <div class="step {{ $step >= 3 ? ($step > 3 ? 'done' : 'active') : '' }}"></div>
            <div class="step {{ $step >= 4 ? ($step > 4 ? 'done' : 'active') : '' }}"></div>
            <div class="step {{ $step >= 5 ? 'active' : '' }}"></div>
        </div>
        <div class="card shadow-sm border-0">
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
