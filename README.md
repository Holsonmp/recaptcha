# reCAPTCHA

This PHP package allows you to easily integrate Google reCAPTCHA (v2 and v3) into your projects. It supports the basic features of reCAPTCHA v2 (checkbox and invisible) as well as the new features of reCAPTCHA v3 (score-based).

- [Installation](#installation)
- [Initialization](#initialization)
- [Usage](#usage)
  - [reCAPTCHA v2](#recaptcha-v2)
  - [reCAPTCHA v3](#recaptcha-v3)
- [Customization](#customization)
  - [Theme](#theme)
  - [Language](#language)
  - [Type](#type)
  - [Size](#size)
  - [Score Threshold (v3 only)](#score-threshold-v3-only)
- [Full Examples](#full-examples)
  - [reCAPTCHA v2 Example](#recaptcha-v2-example)
  - [reCAPTCHA v3 Example](#recaptcha-v3-example)

---

## Installation

With Composer, add this line to the *require* section of your `composer.json` file:

```json
"Holduix/recaptcha": "dev-master"
```

Then run the following command:

```bash
composer update
```

or 

```bash
composer require holduix/recaptcha
```

---

## Initialization

To initialize reCAPTCHA, you need to provide your public key (site key) and your secret key (secret key). You can do this in two ways:

### Method 1: Directly in the builder

```php
require 'vendor/autoload.php';

use Holduix\Component\reCAPTCHA;

$reCAPTCHA = new reCAPTCHA('your-site-key', 'your-secret-key', 'v2'); // or 'v3' for reCAPTCHA v3
```

### Method 2: Via separate methods

```php
require 'vendor/autoload.php';

use Holduix\Component\reCAPTCHA;

$reCAPTCHA = new reCAPTCHA();
$reCAPTCHA->setSiteKey('your-site-key');
$reCAPTCHA->setSecretKey('your-secret-key');
$reCAPTCHA->setVersion('v2'); // or 'v3' for reCAPTCHA v3
```

---

## Usage

### reCAPTCHA v2

reCAPTCHA v2 is the classic version that displays an invisible checkbox or captcha. Here's how to use it:

#### Generate the script

```php
echo $reCAPTCHA->getScript();
```

#### Generate HTML block

```php
echo $reCAPTCHA->getHtml();
```

#### Server-side validation

```php
if ($reCAPTCHA->isValid($_POST['g-recaptcha-response'])) {
    // Le captcha est valide
    echo "Captcha valide !";
} else {
    // Afficher les erreurs
    var_dump($reCAPTCHA->getErrorCodes());
}
```

### reCAPTCHA v3

reCAPTCHA v3 works without user interaction and returns a score between 0.0 and 1.0. Here's how to use it:

#### Generate the script

```php
echo $reCAPTCHA->getScript();
```

#### Generate hidden field

```php
echo $reCAPTCHA->getHtml();
```

#### Server-side validation

```php
if ($reCAPTCHA->isValid($_POST['g-recaptcha-response'])) {
    // Le captcha est valide
    echo "Captcha valide !";
} else {
    // Afficher les erreurs
    var_dump($reCAPTCHA->getErrorCodes());
}
```

---

## Customization

### Theme

Several themes are available for reCAPTCHA v2: `light` (default) or `dark`.

```php
$reCAPTCHA->setTheme('dark');
```

### Language

You can change the language of reCAPTCHA. By default, the language is automatically detected.

```php
$reCAPTCHA->setLanguage('fr'); // Français
```

### Type

For reCAPTCHA v2 you can choose between `image` (default) or `audio`.

```php
$reCAPTCHA->setType('audio');
```

### Size

For reCAPTCHA v2 you can choose between `normal` (default) or `compact`.

```php
$reCAPTCHA->setSize('compact');
```

### Score Threshold (v3 only)

For reCAPTCHA v3, you can set a score threshold (between 0.0 and 1.0). By default, the threshold is set to 0.5.

```php
$reCAPTCHA->setScoreThreshold(0.7); // Custom Threshold
```

---

## Full Examples

### reCAPTCHA v2 Example

```php
<?php
require 'vendor/autoload.php';
use Holduix\Component\reCAPTCHA;

$reCAPTCHA = new reCAPTCHA('your-site-key', 'your-secret-key', 'v2');
$reCAPTCHA->setTheme('dark');
$reCAPTCHA->setLanguage('fr');
?>

<html>
<head>
    <title>reCAPTCHA v2 Example</title>
    <?php echo $reCAPTCHA->getScript(); ?>
</head>
<body>

<?php
if (isset($_POST['name'])) {
    if ($reCAPTCHA->isValid($_POST['g-recaptcha-response'])) {
        echo '<p>Captcha valide !</p>';
    } else {
        echo '<p>Erreur de captcha :</p>';
        var_dump($reCAPTCHA->getErrorCodes());
    }
}
?>

<form action="#" method="POST">
    <input type="text" name="name" placeholder="Nom">
    <?php echo $reCAPTCHA->getHtml(); ?>
    <input type="submit" value="Envoyer">
</form>

</body>
</html>
```

### reCAPTCHA v3 Example

```php
<?php
require 'vendor/autoload.php';
use Holduix\Component\reCAPTCHA;

$reCAPTCHA = new reCAPTCHA('your-site-key', 'your-secret-key', 'v3');
$reCAPTCHA->setScoreThreshold(0.7); // Seuil personnalisé
?>

<html>
<head>
    <title>reCAPTCHA v3 Example</title>
    <?php echo $reCAPTCHA->getScript(); ?>
</head>
<body>

<?php
if (isset($_POST['name'])) {
    if ($reCAPTCHA->isValid($_POST['g-recaptcha-response'])) {
        echo '<p>Captcha valide !</p>';
    } else {
        echo '<p>Erreur de captcha :</p>';
        var_dump($reCAPTCHA->getErrorCodes());
    }
}
?>

<form action="#" method="POST">
    <input type="text" name="name" placeholder="Nom">
    <?php echo $reCAPTCHA->getHtml(); ?>
    <input type="submit" value="Envoyer">
</form>

</body>
</html>
```
