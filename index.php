<?php
// Cipherella: Caesar and Vigenere cipher tool.

$result = "";
$error = "";
$operation = "encrypt";
$cipher = "caesar";

function caesarEncrypt(string $text, int $shift): string
{
    $result = "";

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];

        if (ctype_upper($char)) {
            $result .= chr((ord($char) - 65 + $shift) % 26 + 65);
        } elseif (ctype_lower($char)) {
            $result .= chr((ord($char) - 97 + $shift) % 26 + 97);
        } else {
            $result .= $char;
        }
    }

    return $result;
}

function caesarDecrypt(string $text, int $shift): string
{
    return caesarEncrypt($text, 26 - ($shift % 26));
}

function vigenereEncrypt(string $text, string $key): string
{
    $result = "";
    $key = preg_replace('/[^A-Za-z]/', '', $key);

    if ($key === '') {
        return '';
    }

    $keyIndex = 0;
    $keyLength = strlen($key);

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];

        if (ctype_alpha($char)) {
            $shift = ord(strtoupper($key[$keyIndex % $keyLength])) - 65;
            $base = ctype_upper($char) ? 65 : 97;
            $result .= chr((ord($char) - $base + $shift) % 26 + $base);
            $keyIndex++;
        } else {
            $result .= $char;
        }
    }

    return $result;
}

function vigenereDecrypt(string $text, string $key): string
{
    $result = "";
    $key = preg_replace('/[^A-Za-z]/', '', $key);

    if ($key === '') {
        return '';
    }

    $keyIndex = 0;
    $keyLength = strlen($key);

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];

        if (ctype_alpha($char)) {
            $shift = ord(strtoupper($key[$keyIndex % $keyLength])) - 65;
            $base = ctype_upper($char) ? 65 : 97;
            $result .= chr((ord($char) - $base - $shift + 26) % 26 + $base);
            $keyIndex++;
        } else {
            $result .= $char;
        }
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? '';
    $cipher = $_POST['cipher'] ?? 'caesar';
    $operation = $_POST['operation'] ?? 'encrypt';

    if (trim($text) === '') {
        $error = 'Please enter some text first.';
    } elseif ($cipher === 'caesar') {
        $shift = isset($_POST['shift']) ? (int) $_POST['shift'] : 3;
        $shift = (($shift % 26) + 26) % 26;
        $result = $operation === 'encrypt'
            ? caesarEncrypt($text, $shift)
            : caesarDecrypt($text, $shift);
    } elseif ($cipher === 'vigenere') {
        $key = $_POST['key'] ?? '';

        if (trim($key) === '') {
            $error = 'Please enter a Vigenere key.';
        } elseif (!preg_match('/[A-Za-z]/', $key)) {
            $error = 'The Vigenere key must contain letters.';
        } else {
            $result = $operation === 'encrypt'
                ? vigenereEncrypt($text, $key)
                : vigenereDecrypt($text, $key);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cipherella</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap');

        :root {
            --ink: #f9edf5;
            --muted: #b99aaa;
            --panel: rgba(39, 21, 45, .68);
            --panel-light: rgba(255, 255, 255, .07);
            --line: rgba(255, 216, 239, .17);
            --rose: #f28bbd;
            --pink: #df5b9e;
            --plum: #24142b;
            --focus: rgba(242, 139, 189, .3);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            padding: clamp(24px, 5vw, 64px) 16px 32px;
            overflow-x: hidden;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            background-color: #120d18;
            background-image: linear-gradient(rgba(255, 255, 255, .025) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .025) 1px, transparent 1px), radial-gradient(circle at 12% 8%, rgba(221, 91, 158, .25), transparent 28%), radial-gradient(circle at 88% 92%, rgba(105, 61, 133, .2), transparent 32%), linear-gradient(135deg, #100b15, #211127 54%, #130d1b);
            background-size: 42px 42px, 42px 42px, auto, auto, auto;
        }

        .container { width: min(100%, 960px); margin: auto; }
        .header { margin-bottom: clamp(24px, 4vw, 38px); text-align: center; animation: reveal .7s ease both; }
        .logo {
            display: grid;
            place-items: center;
            width: 78px;
            height: 78px;
            margin: 0 auto 18px;
            border: 1px solid rgba(255, 210, 237, .32);
            border-radius: 26px;
            color: var(--rose);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 36px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .16), rgba(255, 255, 255, .035));
            box-shadow: 0 20px 55px rgba(0, 0, 0, .3), inset 0 1px rgba(255, 255, 255, .22);
            backdrop-filter: blur(18px);
        }

        .header h1 { color: #ffe8f5; font-family: 'Space Grotesk', sans-serif; font-size: clamp(34px, 6vw, 56px); letter-spacing: .02em; }
        .header p { max-width: 460px; margin: 10px auto 0; color: var(--muted); font-size: 15px; line-height: 1.6; }
        .card {
            padding: clamp(18px, 4vw, 34px);
            border: 1px solid var(--line);
            border-radius: 30px;
            background: linear-gradient(145deg, rgba(77, 41, 78, .5), rgba(25, 15, 31, .78));
            box-shadow: 0 28px 80px rgba(0, 0, 0, .36), inset 0 1px rgba(255, 255, 255, .14);
            backdrop-filter: blur(24px);
            animation: reveal .7s .12s ease both;
        }

        .mode-container { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: clamp(22px, 4vw, 30px); }
        .mode input { position: absolute; width: 1px; height: 1px; opacity: 0; }
        .mode label {
            display: block;
            min-height: 52px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 16px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            background: var(--panel-light);
            transition: border-color .2s, background .2s, color .2s, transform .2s;
        }
        .mode label:hover { border-color: rgba(242, 139, 189, .5); transform: translateY(-2px); }
        .mode input:focus-visible + label { outline: 3px solid var(--focus); outline-offset: 3px; }
        .mode input:checked + label { border-color: rgba(255, 210, 237, .35); color: #fff; background: linear-gradient(135deg, rgba(223, 91, 158, .8), rgba(112, 54, 117, .8)); box-shadow: 0 10px 28px rgba(223, 91, 158, .2); }
        .form-grid { position: relative; display: grid; grid-template-columns: 1fr 1fr; gap: 22px 18px; }
        .full { grid-column: 1 / -1; }
        .field label { display: block; margin-bottom: 9px; color: #e8cadc; font-size: 13px; font-weight: 600; letter-spacing: .04em; }
        textarea, input, select {
            width: 100%;
            min-height: 50px;
            padding: 14px 15px;
            border: 1px solid var(--line);
            border-radius: 14px;
            outline: none;
            color: var(--ink);
            font: inherit;
            background: rgba(12, 8, 18, .48);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        textarea { min-height: 170px; resize: vertical; line-height: 1.6; }
        textarea::placeholder, input::placeholder { color: #806579; }
        option { color: #211127; }
        textarea:focus, input:focus, select:focus { border-color: var(--rose); background: rgba(12, 8, 18, .7); box-shadow: 0 0 0 4px var(--focus), 0 8px 24px rgba(0, 0, 0, .15); }
        .button-container { margin-top: 26px; }
        button { width: 100%; min-height: 54px; padding: 15px 20px; border: 1px solid rgba(255, 220, 240, .28); border-radius: 15px; color: #fff; font: 700 15px 'DM Sans', sans-serif; cursor: pointer; background: linear-gradient(120deg, #d64f94, #9b4e9d); box-shadow: 0 12px 28px rgba(198, 68, 139, .28); transition: transform .2s, box-shadow .2s, filter .2s; }
        button:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 16px 34px rgba(198, 68, 139, .38); }
        button:active { transform: translateY(0); }
        button:focus-visible { outline: 3px solid var(--focus); outline-offset: 3px; }
        .cipher-field { transition: opacity .28s ease, transform .28s ease; }
        .cipher-field.is-hidden { position: absolute; right: 0; bottom: 0; left: 50%; visibility: hidden; opacity: 0; pointer-events: none; transform: translateY(8px); }
        .error, .result { margin-top: 22px; padding: clamp(16px, 3vw, 22px); border: 1px solid var(--line); border-radius: 20px; background: rgba(255, 255, 255, .055); animation: reveal .4s ease both; }
        .error { color: #ffc1cb; border-color: rgba(255, 143, 157, .28); background: rgba(167, 47, 74, .18); }
        .result-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 13px; }
        .result-header h2 { font-family: 'Space Grotesk', sans-serif; font-size: 18px; }
        .result-text { min-height: 110px; padding: 16px; overflow-wrap: anywhere; border: 1px solid rgba(255, 255, 255, .08); border-radius: 14px; white-space: pre-wrap; word-break: break-word; color: #f7deeb; font-family: 'Space Grotesk', monospace; line-height: 1.65; background: rgba(7, 5, 10, .42); }
        .badge { padding: 6px 10px; border: 1px solid rgba(242, 139, 189, .25); border-radius: 999px; color: var(--rose); font-size: 11px; font-weight: 700; letter-spacing: .08em; }
        .how-it-works { margin-top: clamp(24px, 5vw, 42px); animation: reveal .7s .24s ease both; }
        .how-heading { margin-bottom: 16px; color: #ffe8f5; font-family: 'Space Grotesk', sans-serif; font-size: clamp(22px, 4vw, 28px); text-align: center; }
        .how-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .how-card { padding: 20px; border: 1px solid var(--line); border-radius: 20px; background: rgba(255, 255, 255, .045); box-shadow: inset 0 1px rgba(255, 255, 255, .08); }
        .how-card h3 { margin-bottom: 8px; color: var(--rose); font-family: 'Space Grotesk', sans-serif; font-size: 17px; }
        .how-card p { color: var(--muted); font-size: 14px; line-height: 1.7; }
        .how-card code { display: inline-block; margin-top: 11px; padding: 7px 9px; border-radius: 9px; color: #ffe2f1; font-family: 'Space Grotesk', monospace; font-size: 12px; background: rgba(8, 5, 12, .48); }
        .footer { margin-top: 24px; color: #826579; font-size: 12px; line-height: 1.7; text-align: center; }
        @keyframes reveal { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; gap: 18px; } .full { grid-column: auto; } .cipher-field.is-hidden { right: 0; left: 0; } .how-grid { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { body { padding: 22px 12px 26px; } .card { border-radius: 22px; } .mode-container { grid-template-columns: 1fr; } .mode label { text-align: left; } textarea { min-height: 145px; } .how-card { padding: 17px; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo" aria-hidden="true">&#10022;</div>
            <h1>Cipherella</h1>
            <p>A simple classical cipher encryption and decryption tool</p>
        </header>

        <main class="card">
            <form method="POST">
                <div class="mode-container">
                    <div class="mode">
                        <input type="radio" name="cipher" value="caesar" id="caesar" <?= $cipher === 'caesar' ? 'checked' : '' ?>>
                        <label for="caesar">Caesar Cipher</label>
                    </div>
                    <div class="mode">
                        <input type="radio" name="cipher" value="vigenere" id="vigenere" <?= $cipher === 'vigenere' ? 'checked' : '' ?>>
                        <label for="vigenere">Vigenere Cipher</label>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field full">
                        <label for="text">Text</label>
                        <textarea name="text" id="text" placeholder="Enter your plaintext or ciphertext here..."><?= htmlspecialchars($_POST['text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="field">
                        <label for="operation">Operation</label>
                        <select name="operation" id="operation">
                            <option value="encrypt" <?= $operation === 'encrypt' ? 'selected' : '' ?>>Encrypt</option>
                            <option value="decrypt" <?= $operation === 'decrypt' ? 'selected' : '' ?>>Decrypt</option>
                        </select>
                    </div>
                    <div class="field cipher-field" id="caesarField">
                        <label for="shift">Shift Value</label>
                        <input type="number" name="shift" id="shift" value="<?= htmlspecialchars($_POST['shift'] ?? '3', ENT_QUOTES, 'UTF-8') ?>" min="0" max="25">
                    </div>
                    <div class="field cipher-field is-hidden" id="vigenereField">
                        <label for="key">Secret Key</label>
                        <input type="text" name="key" id="key" value="<?= htmlspecialchars($_POST['key'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: LEMON">
                    </div>
                </div>

                <div class="button-container"><button type="submit">Process Cipher</button></div>
            </form>

            <?php if ($error !== ''): ?>
                <div class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($result !== ''): ?>
                <section class="result">
                    <div class="result-header">
                        <h2>Result</h2>
                        <span class="badge"><?= htmlspecialchars(strtoupper($operation), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="result-text"><?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?></div>
                </section>
            <?php endif; ?>
        </main>

        <section class="how-it-works" aria-labelledby="how-heading">
            <h2 class="how-heading" id="how-heading">How it works</h2>
            <div class="how-grid">
                <article class="how-card">
                    <h3>Caesar Cipher</h3>
                    <p>Every letter moves through the alphabet by the shift value. Spaces, numbers, and symbols stay unchanged.</p>
                    <code>Encrypt: C = (P + shift) mod 26</code>
                </article>
                <article class="how-card">
                    <h3>Vigenere Cipher</h3>
                    <p>A repeating keyword gives each letter its own shift, creating a stronger pattern than one fixed rotation.</p>
                    <code>Encrypt: C = (P + key letter) mod 26</code>
                </article>
            </div>
        </section>

        <footer class="footer">Cipherella - Caesar and Vigenere Cipher</footer>
    </div>

    <script>
        const caesarRadio = document.getElementById('caesar');
        const vigenereRadio = document.getElementById('vigenere');
        const caesarField = document.getElementById('caesarField');
        const vigenereField = document.getElementById('vigenereField');

        function updateCipherFields() {
            const isVigenere = vigenereRadio.checked;
            caesarField.classList.toggle('is-hidden', isVigenere);
            vigenereField.classList.toggle('is-hidden', !isVigenere);
        }

        caesarRadio.addEventListener('change', updateCipherFields);
        vigenereRadio.addEventListener('change', updateCipherFields);
        updateCipherFields();
    </script>
</body>
</html>