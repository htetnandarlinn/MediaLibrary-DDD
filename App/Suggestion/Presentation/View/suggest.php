<?php require BASE_PATH . '/view/layout/header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/validate.css">

<div class="section page">
    <div class="wrapper">

        <h1>Suggest a media item</h1>
        <br><br>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'thanks'): ?>
            <p>Thanks for the email! I&rsquo;ll check out your suggestion shortly!</p>
        <?php else: ?>
            <?php if (!empty($error_message)): ?>
                <p class="message"><?= htmlspecialchars($error_message) ?></p>
            <?php else: ?>
                <p>If you think there is something I&rsquo;m missing, let me know!</p>
                <p>Complete the form to send an email.</p>
            <?php endif; ?>

            <form method="post" class="suggest-form" action="<?= BASE_URL ?>/Public/index.php?page=suggest">

                <div class="form-group">
                    <label for="name">Name (required)</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($name ?? '') ?>"
                    >
                    <?php if (!empty($errors['name'] ?? null)): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email (required)</label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                    >
                    <?php if (!empty($errors['email'] ?? null)): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="category">Category (required)</label>
                    <select id="category" name="category">
                        <option value="" disabled <?= empty($category) ? 'selected' : '' ?>>Select One</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category'] ?? null)): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['category']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="title">Title (required)</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= htmlspecialchars($title ?? '') ?>"
                    >
                    <?php if (!empty($errors['title'] ?? null)): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['title']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="format">Format</label>
                    <select id="format" name="format">
                        <option value="" disabled <?= empty($format) ? 'selected' : '' ?>>Select One</option>
                        <?php foreach ($formats as $category => $optgroups): ?>
                            <optgroup label="<?= htmlspecialchars($category) ?>">
                                <?php foreach ($optgroups as $optgroup): ?>
                                    <option value="<?= htmlspecialchars($optgroup) ?>" <?= (isset($format) && $format === $optgroup) ? 'selected' : '' ?>><?= htmlspecialchars($optgroup) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre">
                        <option value="" disabled <?= empty($genre) ? 'selected' : '' ?>>Select One</option>
                        <?php foreach ($genres as $category => $options): ?>
                            <optgroup label="<?= htmlspecialchars($category) ?>">
                                <?php foreach ($options as $option): ?>
                                    <option value="<?= htmlspecialchars($option) ?>" <?= (isset($genre) && $genre === $option) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <input
                        type="text"
                        id="year"
                        name="year"
                        value="<?= htmlspecialchars($year ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="details">Additional Details</label>
                    <textarea name="details" id="details"><?= htmlspecialchars($details ?? '') ?></textarea>
                </div>

                <div class="form-group" style="display:none">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address">
                    <p>Please leave this field blank</p>
                </div>

                <input type="submit" value="Send" class="btn">
            </form>
        <?php endif; ?>

    </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
