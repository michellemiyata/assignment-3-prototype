<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch user preferences
$stmt = $pdo->prepare("SELECT preferences FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch();
$preferences = json_decode($user_data['preferences'] ?? '{}', true);
$defaultLanguage = $preferences['language'] ?? 'en';

// Translations
$translations = [
    'en' => [
        'back' => 'Back',
        'profile' => 'Profile',
        'student' => 'Student',
        'account_settings' => 'Account Settings',
        'full_name' => 'Full Name',
        'email_address' => 'Email Address',
        'accessibility' => 'Accessibility',
        'high_contrast' => 'High Contrast',
        'increase_contrast' => 'Increase color contrast',
        'large_text' => 'Large Text',
        'increase_font' => 'Increase font size',
        'reduce_motion' => 'Reduce Motion',
        'minimize_animations' => 'Minimize animations',
        'preferences' => 'Preferences',
        'default_language' => 'Default Language'
    ],
    'es' => [
        'back' => 'Atrás',
        'profile' => 'Perfil',
        'student' => 'Estudiante',
        'account_settings' => 'Configuración de la Cuenta',
        'full_name' => 'Nombre Completo',
        'email_address' => 'Correo Electrónico',
        'accessibility' => 'Accesibilidad',
        'high_contrast' => 'Alto Contraste',
        'increase_contrast' => 'Aumentar contraste de color',
        'large_text' => 'Texto Grande',
        'increase_font' => 'Aumentar tamaño de fuente',
        'reduce_motion' => 'Reducir Movimiento',
        'minimize_animations' => 'Minimizar animaciones',
        'preferences' => 'Preferencias',
        'default_language' => 'Idioma Predeterminado'
    ],
    'fr' => [
        'back' => 'Retour',
        'profile' => 'Profil',
        'student' => 'Étudiant',
        'account_settings' => 'Paramètres du Compte',
        'full_name' => 'Nom Complet',
        'email_address' => 'Adresse E-mail',
        'accessibility' => 'Accessibilité',
        'high_contrast' => 'Contraste Élevé',
        'increase_contrast' => 'Augmenter le contraste des couleurs',
        'large_text' => 'Grand Texte',
        'increase_font' => 'Augmenter la taille de la police',
        'reduce_motion' => 'Réduire le Mouvement',
        'minimize_animations' => 'Minimiser les animations',
        'preferences' => 'Préférences',
        'default_language' => 'Langue par Défaut'
    ],
    'ja' => [
        'back' => '戻る',
        'profile' => 'プロフィール',
        'student' => '学生',
        'account_settings' => 'アカウント設定',
        'full_name' => '氏名',
        'email_address' => 'メールアドレス',
        'accessibility' => 'アクセシビリティ',
        'high_contrast' => 'ハイコントラスト',
        'increase_contrast' => '色のコントラストを上げる',
        'large_text' => '大きな文字',
        'increase_font' => 'フォントサイズを大きくする',
        'reduce_motion' => '視差効果を減らす',
        'minimize_animations' => 'アニメーションを最小限にする',
        'preferences' => '設定',
        'default_language' => '既定の言語'
    ]
];

$t = $translations[$defaultLanguage] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['profile']; ?> - Smart Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <a href="dashboard.php" class="glass-button"><span class="material-icons-round"
                    style="vertical-align: middle;">arrow_back</span> <?php echo $t['back']; ?></a>
            <h2><?php echo $t['profile']; ?></h2>
            <div style="width: 80px;"></div>
        </header>

        <div class="center-screen" style="min-height: 60vh; align-items: flex-start;">
            <div class="glass-panel" style="width: 100%; max-width: 500px; padding: 30px; text-align: center;">
                <div class="avatar" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto 20px;">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <h2><?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
                <p class="text-muted" style="margin-bottom: 30px;"><?php echo $t['student']; ?></p>

                <div style="text-align: left;">
                    <!-- Account Settings -->
                    <h3
                        style="margin-bottom: 15px; color: var(--accent-color); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                        <?php echo $t['account_settings']; ?></h3>

                    <div class="form-group">
                        <label><?php echo $t['full_name']; ?></label>
                        <input type="text" class="glass-input"
                            value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label><?php echo $t['email_address']; ?></label>
                        <input type="email" class="glass-input" value="user@example.com" readonly style="opacity: 0.7;">
                    </div>

                    <!-- Accessibility Settings -->
                    <h3
                        style="margin-top: 30px; margin-bottom: 15px; color: var(--accent-color); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                        <?php echo $t['accessibility']; ?></h3>

                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <span style="display: block; font-weight: 500;"><?php echo $t['high_contrast']; ?></span>
                            <small class="text-muted"><?php echo $t['increase_contrast']; ?></small>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="contrastToggle" onchange="toggleContrast()">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <span style="display: block; font-weight: 500;"><?php echo $t['large_text']; ?></span>
                            <small class="text-muted"><?php echo $t['increase_font']; ?></small>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="textToggle" onchange="toggleText()">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <span style="display: block; font-weight: 500;"><?php echo $t['reduce_motion']; ?></span>
                            <small class="text-muted"><?php echo $t['minimize_animations']; ?></small>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="motionToggle" onchange="toggleMotion()">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <!-- App Preferences -->
                    <h3
                        style="margin-top: 30px; margin-bottom: 15px; color: var(--accent-color); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                        <?php echo $t['preferences']; ?></h3>

                    <div class="form-group">
                        <label><?php echo $t['default_language']; ?></label>
                        <select class="glass-input" style="background: rgba(0,0,0,0.3);" id="languageSelect"
                            onchange="updateLanguage()">
                            <option value="en" <?php echo $defaultLanguage === 'en' ? 'selected' : ''; ?>>English (US)
                            </option>
                            <option value="es" <?php echo $defaultLanguage === 'es' ? 'selected' : ''; ?>>Spanish</option>
                            <option value="fr" <?php echo $defaultLanguage === 'fr' ? 'selected' : ''; ?>>French</option>
                            <option value="ja" <?php echo $defaultLanguage === 'ja' ? 'selected' : ''; ?>>Japanese</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleContrast() {
            document.body.classList.toggle('high-contrast');
            console.log("Contrast toggled");
        }

        function toggleText() {
            document.body.classList.toggle('large-text');
            console.log("Text size toggled");
        }

        function toggleMotion() {
            document.body.classList.toggle('reduce-motion');
            console.log("Motion toggled");
        }

        function updateLanguage() {
            const language = document.getElementById('languageSelect').value;
            fetch('api/update_preferences.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ language: language }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Language updated');
                        // Reload the page to apply the new language
                        location.reload();
                    } else {
                        console.error('Failed to update language');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>