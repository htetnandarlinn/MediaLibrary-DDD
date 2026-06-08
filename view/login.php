<?php require BASE_PATH . '/view/Layout/header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/validate.css">

<div class="section page">
    <div class="auth-card">
        <h2>Login</h2>
        <?php if (!empty($errors['error_message'] ?? null)): ?>
        <div class="field-error">
            <?= ($errors['error_message'] ?? ''); ?>
        </div>
        <?php endif; ?>
        <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=login" autocomplete="off">

            <label for="username_or_email">Username or Email</label>
            <input type="text" id="username_or_email" name="username_or_email" autocomplete="off"
                class="<?= !empty($errors['username_or_email'] ?? null) ? 'input-error' : '' ?>"
                value="<?= htmlspecialchars($usernameOrEmail ?? '') ?>">

            <?php if (!empty($errors['username_or_email'] ?? null)): ?>
            <div class="field-error">
                <?= ($errors['username_or_email'] ?? ''); ?>
            </div>
            <?php endif; ?>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password"
                class="<?= !empty($errors['password'] ?? null) ? 'input-error' : '' ?>">

            <?php if (!empty($errors['password'] ?? null)): ?>
            <div class="field-error">
                <?= ($errors['password'] ?? ''); ?>
            </div>
            <?php endif; ?>

            <button type="submit">Login</button>
        </form>

        <p class="auth-link">
            Don't have an account?
            <a href="<?= BASE_URL ?>/Public/index.php?page=register">Register here</a>.
        </p>
    </div>
</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>