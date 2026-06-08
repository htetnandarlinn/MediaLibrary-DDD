<?php require BASE_PATH . '/view/Layout/header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/validate.css">

<div class="section page">
    <div class="auth-card">

        <h2>Register</h2>

        <?php if (!empty($successMessage)): ?>
            <p class="message success">
                <?= htmlspecialchars($successMessage) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=register">

            <!-- Username -->
            <label for="username">Username</label>

            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>"
                class="<?= !empty($errors['username']) ? 'input-error' : '' ?>">

            <?php if (!empty($errors['username'])): ?>
                <div class="field-error">
                    <?= htmlspecialchars($errors['username']) ?>
                </div>
            <?php endif; ?>



            <!-- Email -->
            <label for="email">Email</label>

            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"
                class="<?= !empty($errors['email']) ? 'input-error' : '' ?>">

            <?php if (!empty($errors['email'])): ?>
                <div class="field-error">
                    <?= htmlspecialchars($errors['email']) ?>
                </div>
            <?php endif; ?>



            <!-- Password -->
            <label for="password">Password</label>

            <input type="password" id="password" name="password"
                class="<?= !empty($errors['password']) ? 'input-error' : '' ?>">

            <?php if (!empty($errors['password'])): ?>
                <div class="field-error">
                    <?= htmlspecialchars($errors['password']) ?>
                </div>
            <?php endif; ?>



            <!-- Confirm Password -->
            <label for="confirm_password">
                Confirm Password
            </label>

            <input type="password" id="confirm_password" name="confirm_password"
                class="<?= !empty($errors['confirm_password']) ? 'input-error' : '' ?>">

            <?php if (!empty($errors['confirm_password'])): ?>
                <div class="field-error">
                    <?= htmlspecialchars($errors['confirm_password']) ?>
                </div>
            <?php endif; ?>


            <button type="submit">
                Register
            </button>

        </form>

        <p class="auth-link">
            Already registered?
            <a href="<?= BASE_URL ?>/Public/index.php?page=login">
                Login here
            </a>.
        </p>

    </div>
</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>
