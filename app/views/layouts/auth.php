<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Masuk') ?> &mdash; CMS</title>
    <link rel="stylesheet" href="<?= asset('css/core-ui.css') ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            padding: 1rem;
        }
        .auth-card {
            max-width: 420px;
            width: 100%;
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <!-- Flash Messages -->
        <?php foreach (Session::getFlashes() as $type => $messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : e($type) ?>">
                    <span><?= e($msg) ?></span>
                    <button type="button" class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <?= $content ?>
    </div>

    <script src="<?= asset('js/core-ui.js') ?>"></script>
</body>
</html>
