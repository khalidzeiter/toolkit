# Simple PHP Handlers
## Sessions Handler (sessionHandler.php)
### Usage:
```php
// Session Start
$session = new MySessionHandler();
$session->start();
// Check Session Validity
if (!$session->isValidFingerPrint()) { $session->kill(); }
// Handle $_SESSION variable
$session->name = "Your Name"; // = $_SESSION['name'] = "Your Name";
$session->age  = "Your Age";  // = $_SESSION['age']  = "Your Age";
// ...
```
## Database Handler (PDO)
